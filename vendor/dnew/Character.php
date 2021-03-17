<?php

class Character {
  public $mapX = 0;
  public $mapY = 0;
  public $tileX = 0;
  public $tileY = 0;

  public function __construct($mapX = null, $mapY = null) {
    $this->mapX = $mapX ?? round(\World::SIZE_X / 2);
    $this->mapY = $mapY ?? round(\World::SIZE_Y / 2);
    $this->tileX = rand(1, Map::SIZE_X - 2);
    $this->tileY = rand(1, Map::SIZE_Y - 2);
  }
}