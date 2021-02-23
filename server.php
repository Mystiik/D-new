<?php
// Via cmd dans le dossier => php test-13-server_socket.php

//----------------------------------------------------------------------------------------------------------------
// Initialisation
//----------------------------------------------------------------------------------------------------------------
include_once('include/init.php');
error_reporting(E_ALL);

if (file_exists(Server::$savePath . '/save.txt')) {
  $_world = \test\Serializer::unserialize(file_get_contents(Server::$savePath . '/save.txt'));
} else {
  // Initialisation
  $_world = [];

  // Generate map
  $map = new Map();
  $map->generate();
  $_world['map'][] = $map;
}

// Tickers
$_world['ticker']['saveWorld'] = time() + 60;
$_world['ticker']['printServerInfo'] = time() + 60;

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
  // SaveWorld
  if (time() >= $_world['ticker']['saveWorld']) {
    $fileDefault = fopen(Server::$savePath . 'save.txt', 'w');
    $file = fopen(Server::$savePath . 'save' . time() . '.txt', 'w');

    $_worldSerialized = \test\Serializer::serialize($_world);

    fwrite($fileDefault, $_worldSerialized);
    fwrite($file, $_worldSerialized);
    fclose($fileDefault);
    fclose($file);

    $_world['ticker']['saveWorld'] = time() + 60;
  }

  // PrintServerInfo
  if (time() >= $_world['ticker']['printServerInfo']) {
    $memoryUsageMb = round(memory_get_usage() / 1000 / 1000, 2);
    $memoryLimitMb = ini_get('memory_limit');
    $memoryLimitMb = str_replace('K', '000', $memoryLimitMb);
    $memoryLimitMb = str_replace('M', '000000', $memoryLimitMb);
    $memoryLimitMb = round($memoryLimitMb / 1000 / 1000, 2);
    $ratio = round($memoryUsageMb / $memoryLimitMb * 100, 2);

    echo PHP_EOL . "SERVER_INFO" . PHP_EOL;
    echo "-- Memory usage: $memoryUsageMb Mb ($ratio%)" . PHP_EOL;
    echo "-- Memory limit: $memoryLimitMb Mb" . PHP_EOL;

    $_world['ticker']['printServerInfo'] = time() + 60;
  }
}