<?php

namespace Object\Collectible;

use \Action\Action;

class Gravel extends __Base {
  // use \GlbObjFunc\__Get;

  // Ressource
  const TYPE = 'GRAVEL';
  const RESSOURCE_TYPE = 'ROCK';
  const FORAGING_TIME = 1.5;
  public $ressourceNumber = 3;

  // Action
  const ACTION = [Action::PICK_UP];

  // Coordinate
  public $coordinate = \GlbObjFunc\Coordinate::set();
  public $direction = 0;
}