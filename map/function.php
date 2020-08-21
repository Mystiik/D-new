<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bdd_connexion.php');

// MAP STR TO ARRAY
function map_str_to_array($str)
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

// GET MAP
function get_map($user)
{
	global $bdd;

	$req = $bdd->prepare('SELECT map.* FROM map INNER JOIN user ON user.map=map.name WHERE user.id=:id');
	$req->execute(array('id' => $user));

	return $req->fetch(PDO::FETCH_ASSOC);
}

// GET MODIF
function get_modif($user)
{
	global $bdd;

	$REQ = $bdd->prepare('SELECT modif.modif FROM modif INNER JOIN user ON user.map=modif.name_map WHERE user.id=:id AND modif.timestamp>=user.last_seen');
	$REQ->execute(array('id' => $user));
	$req = $REQ->fetchALL();

	for ($i = 0; $i < count($req); $i++) {
		$req[$i] = $req[$i]['modif'];
	}

	return implode(";", $req);
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

	$req = $req->fetch(PDO::FETCH_ASSOC);

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