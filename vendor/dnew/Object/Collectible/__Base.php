<?php

namespace Object\Collectible;

class __Base {
  // Coordinate
  public $tileX = 0;
  public $tileY = 0;
  public $direction = 0;

  // Skin
  public $skinId = 0;

  // Construct
  public function __construct($tileX = 0, $tileY = 0) {
    $this->tileX = $tileX;
    $this->tileY = $tileY;
  }
}
