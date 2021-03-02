<?php

namespace Object\Flora;

use \Action\Action;

class Tree extends __Base {
	// use \GlbObjFunc\__Get;

	// Ressource
	const TYPE = 'TRUNK';
	const RESSOURCE_TYPE = 'WOOD';
	public $ressourceNumber = 8;

	// Action
	const ACTION = [];

	// Size
	const SIZE_X = 1;
	const SIZE_Y = 1;

	// Coordinate
	public $posX = 0;
	public $posY = 0;
	public $direction = 0;
}