<?php

namespace Object\Collectible;

class __Base {
  // Coordinate
  public $posX = 0;
  public $posY = 0;
  public $direction = 0;

  // Skin
  public $skinId = 0;

  // Construct
  public function __construct($posX = 0, $posY = 0) {
    $this->posX = $posX;
    $this->posY = $posY;
  }
}