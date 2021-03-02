<?php

namespace Object\Collectible;

use \GlbObjFunc\Gain;

class Rock extends __Base {
  // Ressource
  public $_ROCK = 8;

  // Size
  public static $sizeX = 1;
  public static $sizeY = 1;

  // Action
  public static $action = [];
  public static function __constructStatic() {
    self::$action[] = new \Action\Mine('PICK_AXE', 1.5, [
      new Gain('ROCK', 0.5, 3, 5)
    ]);
  }
}