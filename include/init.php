<?php
//date_default_timezone_set("Europe/Paris");

// Define ROOT
define("__ROOT__", dirname(__DIR__));

// Autoloader
spl_autoload_register(function ($class_name) {
  require_once __ROOT__ . "/vendor/dnew/$class_name.php";
});