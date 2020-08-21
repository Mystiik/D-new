<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bdd_connexion.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/user/function.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/map/function.php');

// MAP STR TO ARRAY
function send_modif($user, $map_name, $modif)
{
	global $bdd;

	$req = $bdd->prepare('INSERT INTO modif (id_user, name_map, modif, timestamp) VALUES (:id_user, :name_map, :modif, :timestamp)');
	$req->execute(array('id_user' => $user, 'name_map' => $map_name, 'modif' => $modif, 'timestamp' => microtime(true)));
}


// CREATE => USER, X, Y, COUCHE_ID, BLOC_NAME
function create($user, $x, $y, $couche, $bloc)
{
	global $bdd;
	$user_info = user_info($user);
	$map_info = get_map($user);
	$map = map_str_to_array($map_info['map_modif']);
	$map_name = $map_info['name'];
	$modif = "";

	if (!isset($map[$x][$y][$couche]) and isset($bloc)) {

		// On vérifie la hauteur de la case
		if (0 <= $couche and $couche <= 50) {

			// On vérifie que la case ne dépasse pas la map
			$distance = abs(25 - $x) + abs(25 - $y);
			if ($distance <= 15) {

				// S'il y a une couche en dessous
				if (isset($map[$x][$y][$couche - 10])) {
					$modif = "CREATE $x $y $couche $bloc;";
					$map[$x][$y][$couche] = get_bloc($bloc);

					if ($couche <= 20 and !($couche == 20 and ((isset($map[$x][$y - 1][0]) and !isset($map[$x][$y - 1][10])) or (isset($map[$x + 1][$y][0]) and !isset($map[$x + 1][$y][10]))))) {
						unset($map[$x][$y][$couche - 10]);
						$modif .= "DESTROY $x $y " . ($couche - 10) . ";";
					}

					if ($couche == 10) {
						if (isset($map[$x - 1][$y][20]) and isset($map[$x - 1][$y][10])) {
							unset($map[$x - 1][$y][10]);
							$modif .= "DESTROY " . ($x - 1) . " $y 10;";
						}

						if (isset($map[$x][$y + 1][20]) and isset($map[$x][$y + 1][10])) {
							unset($map[$x][$y + 1][10]);
							$modif .= "DESTROY $x " . ($y + 1) . " 10;";
						}
					}
				}

				// S'il n'y a pas de couche en dessous
				else {
					// On cherche la couche minimum // -10 s'il n'y en a pas
					$couche_min = $couche;
					while ($couche_min >= 0) {
						if (isset($map[$x][$y][$couche_min])) {
							break;
						}
						$couche_min -= 10;
					}

					// S'il n'y a pas de couche minimum on crée
					if ($couche_min == -10) {
						$modif .= "CREATE $x $y $couche $bloc;";
						$map[$x][$y][$couche] = get_bloc($bloc);
					}

					// S'il y a une couche minimum, la case a été créée trop haut (il y a un vide)
					else {
						$modif .= "CREATE $x $y $couche $bloc;";
						$modif .= "BLOC_FALL $x $y $couche " . ($couche_min + 10) . ";";
						$map[$x][$y][$couche_min + 10] = get_bloc($bloc);
					}
				}

				update_map($map_name, $map);
				send_modif($user, $map_name, $modif);
			}
		}
	}
}

//DESTROY => USER, X, Y, COUCHE_ID
function destroy($user, $x, $y, $couche)
{
	global $bdd;
	$user_info = user_info($user);
	$map_info = get_map($user);
	$map = map_str_to_array($map_info['map_modif']);
	$map_name = $map_info['name'];

	$modif = "DESTROY $x $y $couche;";

	if (isset($map[$x][$y][$couche])) {
		$bloc = $map[$x][$y][$couche];
		unset($map[$x][$y][$couche]);


		if (!isset($map[$x][$y][$couche - 10]) and $couche <= 20) {
			$map[$x][$y][$couche - 10] = $bloc;
			$modif .= "CREATE $x $y " . ($couche - 10) . " " . get_bloc($bloc) . ";";
		}

		if ($couche == 10) {
			if (isset($map[$x - 1][$y][20]) and !isset($map[$x - 1][$y][10])) {
				$map[$x - 1][$y][10] = $map[$x - 1][$y][20];
				$modif .= "CREATE " . ($x - 1) . " $y 10 " . get_bloc($map[$x - 1][$y][10]) . ";";
			}

			if (isset($map[$x][$y + 1][20]) and !isset($map[$x][$y + 1][10])) {
				$map[$x][$y + 1][10] = $map[$x][$y + 1][20];
				$modif .= "CREATE $x " . ($y + 1) . " 10 " . get_bloc($map[$x][$y + 1][10]) . ";";
			}
		}

		update_map($map_name, $map);
	}

	send_modif($user, $map_name, $modif);
}


//MOVE => USER, X, Y
function move($user, $x, $y)
{
	global $bdd;
	$user_info = user_info($user);
	$map_info = get_map($user);
	$map = map_str_to_array($map_info['map_modif']);
	$map_name = $map_info['name'];

	$modif = "MOVE $user $x $y;";

	if (isset($map[$x][$y])) {
		$req = $bdd->prepare('UPDATE user SET position=:position WHERE id=:id');
		$req->execute(array('position' => "$x $y", 'id' => $user));

		//Si l'user est en combat, on lui retire des PM
		if (is_map_combat($map_name)) {
			$position = explode(" ", $user_info['position']);
			$PM = $user_info['PM'] - abs($position[0] - $x) - abs($position[1] - $y);

			$req = $bdd->prepare('UPDATE user SET PM=:PM WHERE id=:id');
			$req->execute(array('id' => $user, 'PM' => $PM));

			$req = $bdd->prepare('INSERT INTO modif (id_user, name_map, modif, timestamp) VALUES (:id_user, :name_map, :modif, :timestamp)');
			$req->execute(array('id_user' => $user, 'name_map' => $map_name, 'modif' => "SET_PM $user $PM", 'timestamp' => microtime(true)));
		}
	}

	send_modif($user, $map_name, $modif);
}


//MESSAGE => USER, MESSAGE
function message($user, $message)
{
	global $bdd;
	//$user_info = user_info($user);
	$map_info = get_map($user);
	//$map = map_str_to_array($map_info['map_modif']);
	$map_name = $map_info['name'];

	send_modif($user, $map_name, "MESSAGE $user " . str_replace(" ", "__", $message));
}


//TELEPORT => USER, X, Y
function teleport($user, $x, $y)
{
	global $bdd;
	//$user_info = user_info($user);
	$map_info = get_map($user);
	$map = map_str_to_array($map_info['map_modif']);
	$map_name = $map_info['name'];

	$modif = "TELEPORT $user $x $y;";

	if (isset($map[$x][$y])) {
		$req = $bdd->prepare('UPDATE user SET position=:position WHERE id=:id');
		$req->execute(array('position' => "$x $y", 'id' => $user));
	}

	send_modif($user, $map_name, $modif);
}


//COMBAT_POS_VALIDE => USER, ETAT
function combat_pos_valide($user, $etat)
{
	global $bdd;
	$user_info = user_info($user);
	$map_info = get_map($user);
	//$map = map_str_to_array($map_info['map_modif']);
	$map_name = $map_info['name'];

	$modif = "COMBAT_POS_VALIDE $user $etat;";

	$REQ = $bdd->prepare('SELECT team, pos_valide FROM combat_setup WHERE id=:id');
	$REQ->execute(array('id' => $map_name));
	$req = $REQ->fetch();

	$team = explode(" ", str_replace(",", " ", $req['team']));
	if ($req['pos_valide'] != "") {
		$pos_valide = explode(" ", $req['pos_valide']);
	} else {
		$pos_valide = [];
	}

	if ($etat == "SUPPR") {
		//On supprime l'user de la team
		$team = str_replace(" $user", "", $req['team']);

		//On met à jour la team
		$req = $bdd->prepare('UPDATE combat_setup SET team=:team WHERE id=:id');
		$req->execute(array('team' => $team, 'id' => $map_name));

		//On met à jour le joueur
		$req = $bdd->prepare('UPDATE user SET map=map_tmp, map_tmp="" WHERE id=:id');
		$req->execute(array('id' => $user));

		$team = explode(" ", str_replace(",", " ", $team));
		$etat = "OFF";
	}

	if ($etat == "ON") {
		//On valide la position de l'user 
		//$pos_valide .= "$user ";
		//$pos_valide = explode(" ", $pos_valide);
		$pos_valide[count($pos_valide)] = $user;
		$pos_valide = implode(" ", $pos_valide);

		$req = $bdd->prepare('UPDATE combat_setup SET pos_valide=:pos_valide WHERE id=:id');
		$req->execute(array('pos_valide' => $pos_valide, 'id' => $map_name));
	}

	if ($etat == "OFF") {
		//On supprime l'user des positions validées
		//$pos_valide = explode(" ", $pos_valide);

		for ($i = 0; $i < count($pos_valide); $i++) {
			if ($pos_valide[$i] == $user) {
				unset($pos_valide[$i]);
			}
		}

		$pos_valide = implode(" ", $pos_valide);

		//On met à jour la liste 
		$req = $bdd->prepare('UPDATE combat_setup SET pos_valide=:pos_valide WHERE id=:id');
		$req->execute(array('pos_valide' => $pos_valide, 'id' => $map_name));
	}


	//Si tous les users ont validé, le combat commence 
	$pos_valide = explode(" ", $pos_valide);
	if (count($pos_valide) == count($team)) {
		$req = $bdd->prepare('UPDATE combat_setup SET etape=:etape WHERE id=:id');
		$req->execute(array('id' => $map_name, 'etape' => "2"));

		send_modif($user, $map_name, "COMBAT_ETAPE 2");

		//On crée le systeme d'initiative -> JOUEUR_ACTUEL/J1 J2 J3 J4...
		$initiative = [];

		for ($i = 0; $i < count($pos_valide); $i++) {
			$initiative[$pos_valide[$i]] = user_info($pos_valide[$i])['initiative'] + random_int(0, 40000);
		}
		asort($initiative);

		// $array as $key => $value
		foreach ($initiative as $key => $value) {
			$initiative[$key] = $key;
		}

		$initiative = implode(" ", $initiative); //J1 J2 J3 J4...
		$initiative = explode(" ", $initiative)[0] . "/" . $initiative; //JOUEUR_ACTUEL/J1 J2 J3 J4...

		$req = $bdd->prepare('UPDATE combat_setup SET initiative=:initiative WHERE id=:id');
		$req->execute(array('id' => $map_name, 'initiative' => $initiative));

		$initiative = str_replace(" ", "*", $initiative);
		send_modif($user, $map_name, "INITIATIVE $initiative");
	}

	send_modif($user, $map_name, $modif);
}