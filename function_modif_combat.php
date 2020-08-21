<?php
	// MAP STR TO ARRAY
	//function send_modif($user, $map_name, $modif)
	
	//DAMAGE => USER, X Y, MIN, MAX, TYPE
	function damage($user, $position, $min, $max, $type)
	{
		global $bdd;
		$user_info = user_info($user);
		$map_info = get_map($user);
		//$map = map_str_to_array($map_info['map_modif']);
		$map_name = $map_info['name'];
		
		
		$REQ = $bdd->prepare('SELECT id, vie FROM user WHERE position=:position AND map=:map');
		$REQ->execute(array('map' => $map_name, 'position' => $position));
		$user = $REQ->fetchALL();
		
		for($i=0;$i<count($user); $i++)
		{
			$user[$i]['vie'] -= random_int($min, $max);
			
			$req = $bdd->prepare('UPDATE user SET vie=:vie WHERE id=:id');
			$req->execute(array('id' => $user[$i]['id'], 'vie' => $user[$i]['vie']));
			
			send_modif($user[$i]['id'], $map_name, "SET_VIE ".$user[$i]['id']." ".$user[$i]['vie']);
		}
		
		//var_dump($user);
	}
	
	
	//RETRAIT_PM => USER, X Y, MIN, MAX
	function retrait_pm($user, $position, $min, $max)
	{
		global $bdd;
		$user_info = user_info($user);
		$map_info = get_map($user);
		//$map = map_str_to_array($map_info['map_modif']);
		$map_name = $map_info['name'];
		
		$REQ = $bdd->prepare('SELECT id, PM FROM user WHERE position=:position AND map=:map');
		$REQ->execute(array('map' => $map_name, 'position' => $position));
		$user = $REQ->fetchALL();
		
		for($i=0;$i<count($user); $i++)
		{
			$user[$i]['PM'] -= random_int($min, $max);
			
			$req = $bdd->prepare('UPDATE user SET PM=:PM WHERE id=:id');
			$req->execute(array('id' => $user[$i]['id'], 'PM' => $user[$i]['PM']));
			
			send_modif($user[$i]['id'], $map_name, "SET_PM ".$user[$i]['id']." ".$user[$i]['PM']);
		}
		
		//var_dump($user);
	}
	
	
	//END_TURN => USER
	function end_turn($user)
	{
		global $bdd;
		$user_info = user_info($user);
		$map_info = get_map($user);
		//$map = map_str_to_array($map_info['map_modif']);
		$map_name = $map_info['name'];
		
		
		//On met à jour l'initiative
		$REQ = $bdd->prepare('SELECT initiative FROM combat_setup WHERE id=:id');
		$REQ->execute(array('id' => $map_name));
		$initiative = $REQ->fetch()[0];
		
		$initiative = explode("/", $initiative); //JOUEUR_ACTUEL/J1 J2 J3 J4...
		$initiative[1] = explode(" ", $initiative[1]);
		
		$key = array_search($initiative[0], $initiative[1]);
		$key = ($key+1) % count($initiative[1]);
		
		$initiative[0] = $initiative[1][$key];
		$initiative[1] = implode(" ", $initiative[1]);
		$initiative = implode("/", $initiative);
		
		$req = $bdd->prepare('UPDATE combat_setup SET initiative=:initiative WHERE id=:id');
		$req->execute(array('id' => $map_name, 'initiative' => $initiative));
		
		$initiative = str_replace(" ", "*", $initiative);
		send_modif($user, $map_name, "INITIATIVE $initiative");
		
		//On remet les PA et PM au max
		$req = $bdd->prepare('UPDATE user SET PA=PA_max, PM=PM_max WHERE id=:id');
		$req->execute(array('id' => $user));
		
		send_modif($user, $map_name, "SET_PA $user ".$user_info['PA_max']);
		send_modif($user, $map_name, "SET_PM $user ".$user_info['PM_max']);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>