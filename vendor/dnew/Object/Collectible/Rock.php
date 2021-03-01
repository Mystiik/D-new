<?php

namespace Object\Collectible;

use \Action\Action;

class Rock extends __Base {
  // use \GlbObjFunc\__Get;

  // Ressource
  const TYPE = 'ROCK';
  const RESSOURCE_TYPE = 'ROCK';
  const FORAGING_TIME = 1.5;
  public $ressourceNumber = 8;

  // Action
  const ACTION = [Action::MINE];

  // Coordinate
  public $coordinate = \GlbObjFunc\Coordinate::set();
  public $direction = 0;
}