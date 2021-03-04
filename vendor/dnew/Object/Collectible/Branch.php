<?php

namespace Object\Collectible;

use \GlbObjFunc\Gain;
use \GlbObjFunc\Text;

class Branch extends __Base {
  // Ressource
  public $_WOOD = 3;

  // Size
  public static $sizeX = 1;
  public static $sizeY = 1;

  // Construct
  public static $action = [];
  public static $class = '';

  public static function __constructStatic() {
    self::$action[] = new \Action\PickUp('NONE', 1.5, [
      new Gain('WOOD', 0.5, 3, 5)
    ]);
    self::$class = Text::getClassName(__CLASS__);
  }
}