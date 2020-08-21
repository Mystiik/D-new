<?php

namespace Dnew;

class Map
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $mapInitial;

	/**
	 * @var string
	 */
	private $mapModif;

	/**
	 * @var string
	 */
	private $decor;

	/**
	 * @var string
	 */
	private $combatPos;

	// private $map_str;
	// private $map_array;

	function __construct(string $name)
	{
		$this->setName($name);

		$infos = $this->getInfos();
		$this->setMapInitial($infos['mapInitial']);
		$this->setMapModif($infos['mapModif']);
		$this->setDecor($infos['decor']);
		$this->setCombatPos($infos['combatPos']);
	}

	/**
	 * Contient l'ID des users connectés à la map
	 *
	 * @return array
	 */
	public function getConnectedUsers()
	{
		global $bdd;

		$req = $bdd->prepare('SELECT id FROM user WHERE map=:map AND lastSeen>=:timestamp');
		$req->execute(array('map' => $this->getName(), 'timestamp' => microtime(true) - 2));
		$req = $req->fetchALL(\PDO::FETCH_ASSOC);

		return $req;
	}

	/**
	 * Contient l'ID des mobs connectés à la map
	 *
	 * @return array
	 */
	public function getMobs()
	{
		global $bdd;

		$req = $bdd->prepare('SELECT id FROM user WHERE map=:map AND skin>=1000');
		$req->execute(array('map' => $this->getName()));
		$req = $req->fetchALL(\PDO::FETCH_ASSOC);

		return $req;
	}



	//##############################################################################################################
	// MAP STR TO ARRAY
	protected function map_str_to_array($str)
	{
		$array = [];
		$str = explode(";", $str);

		for ($i = 0; $i < count($str); $i++) {
			$tmp = explode(",", $str[$i]);

			if (isset($tmp[0]) and isset($tmp[1]) and isset($tmp[2]) and isset($tmp[3])) {
				$array[$tmp[0]][$tmp[1]][$tmp[2]] = $tmp[3];
			}
		}

		return $array;
	}

	// MAP ARRAY TO STR
	function map_array_to_str($array)
	{
		$str = "";

		// $array as $key => $value
		foreach ($array as $x => $tmp) {
			foreach ($tmp as $y => $tmp) {
				foreach ($tmp as $couche => $type) {
					$str .= "$x,$y,$couche,$type;";
				}
			}
		}

		return $str;
	}

	// IS_MAP_COMBAT
	function is_map_combat($map)
	{
		global $bdd;

		$REQ = $bdd->prepare('SELECT COUNT(*) FROM combat_setup WHERE id=:id');
		$REQ->execute(array('id' => $map));
		$req = $REQ->fetch()[0];

		if ($req == 0) {
			return false;
		} else {
			return true;
		}
	}

	// BLOC_TABLE
	function get_bloc($bloc)
	{
		global $bdd;

		$req = $bdd->prepare('SELECT * FROM table_bloc WHERE id=:bloc OR type=:bloc');
		$req->execute(array('bloc' => $bloc));

		$req = $req->fetch(\PDO::FETCH_ASSOC);

		if (is_numeric($bloc)) {
			return $req['type'];
		} else {
			return $req['id'];
		}
	}

	// UPDATE MAP
	function update_map($map_name, array $map)
	{
		global $bdd;

		$req = $bdd->prepare('UPDATE map SET map_modif=:map_modif WHERE name=:name');
		$req->execute(array('map_modif' => map_array_to_str($map), 'name' => $map_name));
	}


	// ################################################################################################################################################
	// ################################################################################################################################################
	/**
	 * Récupère les infos de la base
	 *
	 * @return array all columns from 'map' table
	 */
	public function getInfos()
	{
		global $bdd;

		$req = $bdd->prepare('SELECT * FROM map WHERE name=:name');
		$req->execute(array('name' => $this->getName()));

		return $req->fetch(\PDO::FETCH_ASSOC);
	}

	public function setName(string $name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setMapInitial(string $mapInitial)
	{
		$this->mapInitial = $mapInitial;
	}

	public function getMapInitial()
	{
		return $this->mapInitial;
	}

	public function setMapModif(string $mapModif)
	{
		$this->mapModif = $mapModif;
	}

	public function getMapModif()
	{
		return $this->mapModif;
	}

	public function setDecor(string $decor)
	{
		$this->decor = $decor;
	}

	public function getDecor()
	{
		return $this->decor;
	}

	public function setCombatPos(string $combatPos)
	{
		$this->combatPos = $combatPos;
	}

	public function getCombatPos()
	{
		return $this->combatPos;
	}
}