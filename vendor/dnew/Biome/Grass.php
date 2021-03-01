<?php

namespace Biome;

use Tile;

class Grass implements Biome {
	// use \GlbObjFunc\__Get;

	public static function mapCreate() {
		$map = new \Map(Biome::GRASS);
		return $map;
	}
}