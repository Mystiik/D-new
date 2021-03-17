<?php

namespace Object\Flora;

use \Action\Action;

class Tree extends __Base {
  // Ressource
  const TYPE = 'TRUNK';
  const RESSOURCE_TYPE = 'WOOD';
  const GROWTH_TIME = 2 * DAY;
  public $ressourceNumber = 8;

  // Action
  const ACTION = [];

  // Size
  const SIZE_X = 1;
  const SIZE_Y = 1;

  // Coordinate
  public $tileX = 0;
  public $tileY = 0;
  public $direction = 0;
}
