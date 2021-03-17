<?php

class Character {
  public $mapPosX = 0;
  public $mapPosY = 0;
  public $posX = 0;
  public $posY = 0;

  public function __construct($mapPosX = null, $mapPosY = null) {
    $this->mapPosX = $mapPosX ?? round(\World::SIZE_X / 2);
    $this->mapPosY = $mapPosY ?? round(\World::SIZE_Y / 2);
    $this->posX = rand(1, Map::SIZE_X - 2);
    $this->posY = rand(1, Map::SIZE_Y - 2);
  }
}