<?php

namespace Biome;

use \Object\Building;
use \Object\Collectible;
use \Object\Flora;

class Grass implements Biome {
	// use \GlbObjFunc\__Get;

	public static function mapCreate($mapX = 0, $mapY = 0) {
		$map = new \Map(Biome::GRASS);
		$map->mapX = $mapX;
		$map->mapY = $mapY;
		$objectList = [];
		for ($x = 1; $x < \Map::SIZE_X - 1; $x++) {
			for ($y = 1; $y < \Map::SIZE_Y - 1; $y++) {
				$rand = rand(0, 100);
				switch (true) {
						// case (0 <= $rand and $rand <= 0):
						// 	$objectList[] = new Collectible\Branch($x, $y);
						// 	break;
					case (1 <= $rand and $rand <= 1):
						$objectList[] = new Collectible\Gravel($x, $y);
						break;
					case (2 <= $rand and $rand <= 2):
						$objectList[] = new Collectible\Rock($x, $y);
						break;
					case (3 <= $rand and $rand <= 3):
						$objectList[] = new Collectible\Trunk($x, $y);
						break;
					case (4 <= $rand and $rand <= 6):
						$objectList[] = new Collectible\Flower($x, $y);
						break;
					case (7 <= $rand and $rand <= 8):
						$objectList[] = new Collectible\Bush($x, $y);
						break;
					case (9 <= $rand and $rand <= 12):
						$objectList[] = new Collectible\Plant($x, $y);
						break;
				}
			}
		}
		$map->objectList = $objectList;
		return $map;
	}
}
