<?php
// Via cmd dans le dossier => php test-13-server_socket.php

//----------------------------------------------------------------------------------------------------------------
// Initialisation
//----------------------------------------------------------------------------------------------------------------
error_reporting(E_ALL);

include_once('include/init.php');

// if (file_exists(Server::$savePath . '/save.txt')) {
if (false) {
  $_world = GlbObjFunc\Serializer::unserialize(file_get_contents(Server::$savePath . '/save.txt'));
} else {
  // Initialisation
  $_world = [];

  // Map
  for ($x = 1; $x <= World::SIZE_X; $x++) {
    for ($y = 1; $y <= World::SIZE_Y; $y++) {
      $_world['map'][] = Biome\Grass::mapCreate($x, $y);
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

    $response = "";
    $data = fread($file, 2048);

    // Handle message
    if (!empty($data)) {

      // Receive message
      $data = explode('|', $data);
      foreach ($data as $message) {
        //----------------------------------------------------------------------------------------------------------------
        // DO STUFF
        //----------------------------------------------------------------------------------------------------------------
        echo "Message received: $message: " . strlen($message) . PHP_EOL;
        $response .= "You sent: $message";
      }

      //----------------------------------------------------------------------------------------------------------------
      // SEND RESPONSE
      //----------------------------------------------------------------------------------------------------------------
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