<?php
// Initialisation
if (count($_POST) == 0) die('Hello World!');

include_once('../include/init.php');

$ip = $_SERVER['REMOTE_ADDR'];
$userId = $_POST['id'];
$message = $_POST['message'];

//----------------------------------------------------------------------------------------------------------------
// CONNECTION
//----------------------------------------------------------------------------------------------------------------
for ($i = 0; $i < 1000; $i++) {
  $path = Server::$communicationPath . '/stream_' . rand(0, Server::$communicationNumber) . '.txt';
  if (filesize($path) == 0) {
    $file = fopen($path, 'r+');
    if (flock($file, LOCK_EX | LOCK_NB)) {
      break;
    }
    unset($file);
  }
}

if (!isset($file)) die('ERROR; Server is at max capacity!');

//----------------------------------------------------------------------------------------------------------------
// SEND REQUEST
//----------------------------------------------------------------------------------------------------------------
fwrite($file, "REQUEST|$ip|$userId|$message");
// fflush($file); // ??
flock($file, LOCK_UN);

//----------------------------------------------------------------------------------------------------------------
// READ RESPONSE
//----------------------------------------------------------------------------------------------------------------
$time = microtime(true) + 5;
while (@fread($file, 9) != 'RESPONSE|') {
  if (microtime(true) < $time) {
    rewind($file);
    usleep(2000);
  } else {
    ftruncate($file, 0);
    die('ERROR; Server took too long to respond!');
  }
}

clearstatcache(); // refresh filesize info
echo fread($file, filesize($path));

// Cleaning & Closing file
ftruncate($file, 0);
fclose($file);