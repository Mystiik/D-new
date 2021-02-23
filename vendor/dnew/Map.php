<?php

// Déclaration des méthodes manuellement car gérées automatiquement par \__Get
/**
 * @method getSavePath()
 * @method getCommunicationPath()
 * @method getCommunicationNumber()
 */
class Map {
	use \__Get;

	public static $savePath = __ROOT__ . '/server/save/';
	public static $communicationPath = __ROOT__ . '/server/com/';
	public static $communicationNumber = 1000;

	public function __construct() {
	}

	public function generate() {
	}
}