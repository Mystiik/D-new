<?php
if (isset($_POST['user']) and isset($_POST['modif'])) {
	// if (true) {
	require_once($_SERVER['DOCUMENT_ROOT'] . '/init.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/function_modif.php');

	$user = htmlspecialchars($_POST['user']);
	$modif = htmlspecialchars($_POST['modif']);
	// $user = "6wllEXObPTlGv8lSjXMH";
	// $modif = "CREATE 27 17 20 TERRE";

	$modif = explode(" ", $modif);
	$action = $modif[0];
	if (isset($modif[1])) {
		$x = $modif[1];
	}
	if (isset($modif[2])) {
		$y = $modif[2];
	}
	if (isset($modif[3])) {
		$couche = $modif[3];
	}
	if (isset($modif[4])) {
		$bloc = $modif[4];
	}
	$modif = implode(" ", $modif);

	$user_info = user_info($user);
	$map_info = get_map($user);
	$map = map_str_to_array($map_info['map_modif']);
	$map_name = $map_info['name'];


	//CREATE => USER, X, Y, COUCHE_ID, BLOC_NAME
	if ($action == "CREATE") {
		create($user, $x, $y, $couche, $bloc);
	}


	//DESTROY => USER, X, Y, COUCHE_ID
	if ($action == "DESTROY") {
		destroy($user, $x, $y, $couche);
	}


	//MOVE => USER, X, Y
	if ($action == "MOVE") {
		move($user, $x, $y);
	}


	//MESSAGE => USER, MESSAGE
	if ($action == "MESSAGE") {
		message($user, $x);
	}


	//TELEPORT => USER, X, Y
	if ($action == "TELEPORT") {
		teleport($user, $x, $y);
	}

	//COMBAT_POS_VALIDE, X
	if ($action == "COMBAT_POS_VALIDE") {
		//COMBAT_POS_VALIDE => USER, ETAT
		combat_pos_valide($user, $x);
	}
}