<?php

class Map {
	// use \GlbObjFunc\__Get;

	public $tileList = [];
	public $itemList = [];
	public $buildingList = [];
	public $posX = null;
	public $posY = null;
	public $biomeType = null;

	public function __construct($biomeType = \Biome\Biome::GRASS) {
		$this->biomeType = $biomeType;
		$this->tileList[] = new Tile($biomeType);
	}
}