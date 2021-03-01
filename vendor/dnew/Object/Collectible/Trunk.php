<?php

namespace Object\Collectible;

use \Action\Action;

class Trunk extends __Base {
  // use \GlbObjFunc\__Get;

  // Ressource
  const TYPE = 'TRUNK';
  const RESSOURCE_TYPE = 'WOOD';
  const FORAGING_TIME = 1.5;
  public $ressourceNumber = 8;

  // Action
  const ACTION = [Action::CHOP];

  // Coordinate
  public $coordinate = \GlbObjFunc\Coordinate::set();
  public $direction = 0;
}