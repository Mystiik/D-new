<?php

class Map {
	const SIZE_X = 12;
	const SIZE_Y = 12;

	public $mapX = 0;
	public $mapY = 0;
	public $tileList = [];
	public $objectList = [];
	public $biomeType = null;
	public $mapCompressedToSend = null;

	public function __construct($biomeType = \Biome\Biome::GRASS) {
		$this->biomeType = $biomeType;
		$this->tileList[] = new Tile($biomeType);
	}

	public function getMapCompressedToSend() {
		if ($this->mapCompressedToSend == null) {
			$mapCompressed = 'MAP,' . $this->biomeType . ',' . $this->mapX . ',' . $this->mapY . ';';

			foreach ($this->objectList as $obj) {
				$class = $obj::class;
				$mapCompressed .=  $obj::$class . ',' . $obj->tileX . ',' . $obj->tileY . ',' . $obj->direction . ',' . $class::$sizeX . ',' . $class::$sizeY . ',' . $obj->skinId . ';';
			}

			$this->mapCompressedToSend = $mapCompressed;
		}
		return $this->mapCompressedToSend;
	}
}