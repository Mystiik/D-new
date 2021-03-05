<?php
// Via cmd dans le dossier => php test-13-server_socket.php

//----------------------------------------------------------------------------------------------------------------
// Initialisation
//----------------------------------------------------------------------------------------------------------------

use GlbObjFunc\Text;

error_reporting(E_ALL);

include_once('include/init.php');

// if (file_exists(Server::$savePath . '/save.txt')) {
if (false) {
  $_world = GlbObjFunc\Serializer::unserialize(file_get_contents(Server::$savePath . '/save.txt'));
} else {
  // Initialisation
  $_world = [];
  $_world['list'] = [];
  $_world['list']['ipToUser'] = [];
  $_world['user'] = [];
  $_world['warning'] = [];

  // Map
  for ($x = 1; $x <= World::SIZE_X; $x++) {
    for ($y = 1; $y <= World::SIZE_Y; $y++) {
      $_world['map'][$x][$y] = Biome\Grass::mapCreate($x, $y);
    }
  }
}

// Tickers
Ticker::tickerInit();

//----------------------------------------------------------------------------------------------------------------
// Opening all files
//----------------------------------------------------------------------------------------------------------------
$stream = array();
for ($i = 0; $i <= Server::$communicationNumber; $i++) {
  $stream[] = fopen(Server::$communicationPath . '/stream_' . $i . '.txt', 'w+');
}

while (true) {
  //----------------------------------------------------------------------------------------------------------------
  // Read messages
  //----------------------------------------------------------------------------------------------------------------
  foreach ($stream as $file) {
    rewind($file);
    if (@fread($file, 8) != 'REQUEST|') continue;

    $data = fread($file, 2048);

    //----------------------------------------------------------------------------------------------------------------
    // Handle message
    //----------------------------------------------------------------------------------------------------------------
    if (!empty($data)) {
      // Initialisation
      $response = '';
      $data = explode('|', $data);
      $ip = $data[0];
      $userId = $data[1];
      unset($data[0]);
      unset($data[1]);

      if (User::isValidId($userId)) {
        if ($userId == 'NOID') $user = new User($ip);
        else $user = $_world['user'][$userId] ?? null;

        if ($user != null) {
          // Send User info
          $response .= 'USER,' . $user->id . ',' . $user->mapPosX . ',' . $user->mapPosX . '|';

          //
          foreach ($data as $message) {
            $message = explode(';', $message);

            if (strlen(trim($message[0])) > 0) {
              $command = '\\Command\\' . Text::camelCase($message[0]);

              if (is_callable([$command, 'command'])) {
                $response .= $command::command($user, $ip, $message);
              } else {
                // warning ?
                echo $command . '::command() as been commanded line ' . (__LINE__ - 3) . ' but isn\'t callable: [' . implode(';', $message) . ']' . PHP_EOL;
              }
            }
          }
        } else {
          Security::addWarning($ip, Security::FAKE_USER_ID);
        }
      } else {
        Security::addWarning($ip, Security::FAKE_USER_ID);
      }


      // Send response
      if (flock($file, LOCK_EX)) {
        ftruncate($file, 0);
        rewind($file);
        fwrite($file, "RESPONSE|" . $response);
        // fflush($file); // ??
        flock($file, LOCK_UN);
      } else {
        echo "Impossible de verrouiller le fichier !";
      }
    }
  }

  //----------------------------------------------------------------------------------------------------------------
  // TICKERS
  //----------------------------------------------------------------------------------------------------------------
  Ticker::tickerCheck();
}