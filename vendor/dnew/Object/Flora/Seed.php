<?php

namespace Object\Flora;

use \Action\Action;

class Tree extends __Base {
  // use \GlbObjFunc\__Get;

  // Ressource
  const TYPE = 'TRUNK';
  const RESSOURCE_TYPE = 'WOOD';
  const GROWTH_TIME = 2 * HOUR;
  public $ressourceNumber = 8;

  public $type = '';
  public $coordinate = \GlbObjFunc\Coordinate::set();
  // public $level = '';
}