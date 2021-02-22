<?php

// Déclaration des méthodes manuellement car gérées automatiquement par \__Get
/**
 * @method getCommunicationPath()
 * @method getCommunicationNumber()
 */
class Server {
  use \__Get;

  public static $communicationPath = __ROOT__ . '/server.com/';
  public static $communicationNumber = 1000;

  public function __construct() {
  }
}