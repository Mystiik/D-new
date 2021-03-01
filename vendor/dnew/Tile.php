<?php

class Tile {
	// use \GlbObjFunc\__Get;

	public $type = "";
	const GRASS = 'GRASS';

	public function __construct($type = self::GRASS) {
		$this->type = $type;
	}
}