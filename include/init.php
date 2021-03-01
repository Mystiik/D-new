<?php
//date_default_timezone_set("Europe/Paris");

// Define
define("__ROOT__", dirname(__DIR__));
define("MINUTE", 60);
define("HOUR", 3600);
define("DAY", 24 * HOUR);

// Autoloader
spl_autoload_register(function ($class_name) {
  require_once __ROOT__ . "/vendor/dnew/$class_name.php";
});