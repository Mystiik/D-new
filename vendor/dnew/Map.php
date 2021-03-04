<?php

class Map {
	const SIZE_X = 12;
	const SIZE_Y = 12;

	public $posX = 0;
	public $posY = 0;
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
			$mapCompressed = 'MAP,' . $this->biomeType . ',' . $this->posX . ',' . $this->posY . ';';

			foreach ($this->objectList as $obj) {
				$class = $obj::class;
				$mapCompressed .=  $obj::$class . ',' . $obj->posX . ',' . $obj->posY . ',' . $obj->direction . ',' . $class::$sizeX . ',' . $class::$sizeY . ';';
			}

			$this->mapCompressedToSend = $mapCompressed;
		}
		return $this->mapCompressedToSend;
	}
}