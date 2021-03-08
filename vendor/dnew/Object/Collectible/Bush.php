<?php

namespace Object\Collectible;

use \GlbObjFunc\Gain;
use \GlbObjFunc\Text;

class Bush extends __Base {
  // Ressource
  public $_Berry = 5;

  // Size
  public static $sizeX = 1;
  public static $sizeY = 1;

  // Construct
  public static $action = [];
  public static $class = '';

  public static function __constructStatic() {
    self::$action[] = new \Action\PickUp('NONE', 1.5, [
      new Gain('Berry', 1, 1, 3)
    ]);
    self::$class = Text::getClassName(__CLASS__);
  }

  // Skin
  public function __construct($posX = 0, $posY = 0) {
    $this->skinId = rand(0, 3);
    parent::__construct($posX, $posY);
  }
}