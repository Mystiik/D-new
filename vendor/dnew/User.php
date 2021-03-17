<?php

class User {
  public $id = '';
  public $ipAdress = [];
  public $inventory = null;
  public $characterList = [];
  public $mapPosX = 0;
  public $mapPosY = 0;

  public function __construct($ipAdress = null) {
    // Initialisation
    $this->id = self::generateId();
    // $this->inventory = new Inventory();
    $this->mapPosX = round(\World::SIZE_X / 2);
    $this->mapPosY = round(\World::SIZE_Y / 2);
    $this->characterList[] = new Character($this->mapPosX, $this->mapPosY);
    $this->addIpAdress($ipAdress);

    // World
    global $_world;
    $_world['user'][$this->id] = $this;
  }

  public function addIpAdress($ipAdress = null) {
    global $_world;
    if (isset($ipAdress)) {
      $this->ipAdress[] = $ipAdress;
      $_world['list']['ipToUser'][$ipAdress] = $this;
    }
  }

  //-------------------------------------------------------------------------------------------------
  // STATIC
  //-------------------------------------------------------------------------------------------------
  public static function isValidId(string $id) {
    if (!preg_match('/[^A-Za-z0-9]/', $id)) {
      return true;
    }
    return false;
  }

  public static function generateId() {
    $array = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    $id = '';
    for ($i = 0; $i < 20; $i++) {
      $id .= $array[rand(0, 61)];
    }
    return $id;
  }
}