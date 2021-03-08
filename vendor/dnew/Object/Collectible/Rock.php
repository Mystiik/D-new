<?php

namespace Object\Collectible;

use \GlbObjFunc\Gain;
use \GlbObjFunc\Text;

class Rock extends __Base {
  // Ressource
  public $_ROCK = 8;
  public $skinId = 0;

  // Size
  public static $sizeX = 1;
  public static $sizeY = 1;

  // Construct
  public static $action = [];
  public static $class = '';

  public static function __constructStatic() {
    self::$action[] = new \Action\Mine('PICK_AXE', 1.5, [
      new Gain('ROCK', 0.5, 3, 5)
    ]);
    self::$class = Text::getClassName(__CLASS__);
  }
}