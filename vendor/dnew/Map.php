<?php

class Map {
	const SIZE_X = 12;
	const SIZE_Y = 12;

	public $tileList = [];
	public $objectList = [];
	public $biomeType = null;

	public function __construct($biomeType = \Biome\Biome::GRASS) {
		$this->biomeType = $biomeType;
		$this->tileList[] = new Tile($biomeType);
	}
}