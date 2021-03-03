<?php

namespace Biome;

use \Object\Building;
use \Object\Collectible;
use \Object\Flora;

class Grass implements Biome {
	// use \GlbObjFunc\__Get;

	public static function mapCreate() {
		$map = new \Map(Biome::GRASS);
		$objectList = [];
		for ($x = 1; $x <= \Map::SIZE_X; $x++) {
			for ($y = 1; $y <= \Map::SIZE_Y; $y++) {
				$rand = rand(0, 100);
				switch (true) {
					case (0 <= $rand and $rand <= 0):
						$objectList[] = new Collectible\Branch($x, $y);
						break;
					case (1 <= $rand and $rand <= 1):
						$objectList[] = new Collectible\Gravel($x, $y);
						break;
					case (2 <= $rand and $rand <= 2):
						$objectList[] = new Collectible\Rock($x, $y);
						break;
					case (3 <= $rand and $rand <= 3):
						$objectList[] = new Collectible\Trunk($x, $y);
						break;
				}
			}
		}
		$map->objectList = $objectList;
		return $map;
	}
}