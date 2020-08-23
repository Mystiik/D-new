<?php
if (isset($_POST['user'])) {
	// if (true) {
	require_once($_SERVER['DOCUMENT_ROOT'] . '/init.php');

	$user = htmlspecialchars($_POST['user']);
	// $user = "6wllEXObPTlGv8lSjXMH";
	$user = new Dnew\User($user);
	$map = new Dnew\Map($user->getMap());

	//Update last_seen
	$user->updateLastSeen();

	// Si l'user se connecte depuis longtemps
	if (isset($_POST['get_map'])) {
		// if (true) {
		//On envoie la map entière
		echo "SET_MAP;" . str_replace(",", " ", $map->getMapModif()) . ";";

		//On envoie les joueurs connectés sur la map
		$req = $map->getConnectedUsers();

		for ($i = 0; $i < count($req); $i++) {
			$connectedUser = new Dnew\User($req[$i]['id']);
			echo "USER " . $connectedUser->serializeUser() . ";";
		}

		//On envoie les mobs de la map
		$req = $map->getMobs();

		for ($i = 0; $i < count($req); $i++) {
			$mob = new Dnew\User($req[$i]);
			echo "MOB " . $mob->serializeUser() . ";";
		}

		//On prévient de notre connection
		$modif = "USER " . $user->serializeUser() . ";";
		$user->sendModif($modif);


		//MAP COMBAT
		// if (is_map_combat($map_info['name'])) {
		// 	//On récupère les informations de la map
		// 	$REQ = $bdd->prepare('SELECT * FROM combat_setup WHERE id=:id');
		// 	$REQ->execute(array('id' => $map_info['name']));
		// 	$req = $REQ->fetch(PDO::FETCH_ASSOC);

		// 	echo ";COMBAT_ETAPE " . $req['etape'];
		// 	echo ";COMBAT_IA_LEADER " . $req['ia_leader'];
		// 	echo ";COMBAT_GROUPE " . str_replace(" ", "/", $req['team']);
		// 	echo ";COMBAT_POS_PLACE " . str_replace(" ", "*", $req['pos_place']);
		// 	echo ";INITIATIVE " . str_replace(" ", "*", $req['initiative']);

		// 	$pos_valide = explode(" ", $req['pos_valide']);
		// 	for ($i = 0; $i < count($pos_valide); $i++) {
		// 		echo ";COMBAT_POS_VALIDE " . $pos_valide[$i] . " ON";
		// 	}
		// }
	}

	// Si l'user s'est connecté récemment
	else {
		//On envoie les modifications depuis son absence
		$req = $user->getMapModif();

		if (count($req) != 0) {
			echo "UPDATE_MAP;" . implode(";", $req) . ";";
		}


		//On envoie les joueurs déconnectés sur la map
		$req = $bdd->prepare('SELECT id FROM user WHERE map=:map AND last_seen>=:timestamp-3 AND last_seen<=:timestamp-2');
		$req->execute(array('map' => $user->getMap(), 'timestamp' => microtime(true)));
		$req = $req->fetchALL();

		for ($i = 0; $i < count($req); $i++) {
			echo "DISCONNECT " . $req[$i]['id'] . ";";
		}
	}
}