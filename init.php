<?php
//date_default_timezone_set("Europe/Paris");

// Define ROOT
define("__ROOT__", $_SERVER['DOCUMENT_ROOT']);

// Connexion bdd
require_once __ROOT__ . '/bdd_connexion.php';

// Autoloader Composer
require_once __ROOT__ . '/vendor/autoload.php';

// Autoloader
// spl_autoload_register(function ($class_name) {
// 	require_once __ROOT__ . "/$class_name/$class_name.php";
// });