<?php

class User {
  public $id = '';
  public $ipAdress = [];
  public $inventory = null;
  public $mapPosX = round(\World::SIZE_X / 2);
  public $mapPosY = round(\World::SIZE_Y / 2);

  public function __construct() {
    // $this->inventory = new Inventory();
  }

  public static function isValidId(string $id) {
    if (!preg_match('/[^A-Za-z0-9]/', $id)) {
      return true;
    } else {
      // Flag Hack
      return false;
    }
  }

  public static function generateId() {
    $array = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

    $id = '';
    for ($i = 0; $i < 20; $i++) {
      $id .= $array[rand(0, 61)];
    }
    return $id;
  }
}