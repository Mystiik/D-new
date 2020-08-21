<?php
	if(isset($_POST['id']) and isset($_POST['user']) and isset($_POST['position']))
	{
		include_once('bdd_connexion.php');
		include_once('bdd_function.php');
		
		//Create combat
		//$id = "MmOZKDdMDJ";
		//$user = "jmy901apG5DkjolEKKpm";
		//$position = "24 24,25 25";
		//$team = "jmy901apG5DkjolEKKpm,ElxM3ntc4DJp9BIeJrQx"; //mettre un isset(POST..)
		
		//Add user to team
		//$id = "MmOZKDdMDJ";
		//$user = "6wllEXObPTlGv8lSjXMH";
		//$position = "24 24";
		
		$id = htmlspecialchars($_POST['id']);
		$user = htmlspecialchars($_POST['user']);
		$position = htmlspecialchars($_POST['position']);
		if(isset($_POST['team'])) { $team = htmlspecialchars($_POST['team']); }
		
		$map_info = get_map($user);
		$map_name = $map_info['name'];
		
		//Creation combat
		if(isset($team))
		{
			//On crée la map de combat
			$req=$bdd->prepare('INSERT INTO map (name, map_initial, map_modif, combat_pos) VALUES (:name, :map_initial, :map_modif, :combat_pos)');
			$req->execute(array('name' => $id, 'map_initial' => $map_info['map_modif'], 'map_modif' => $map_info['map_modif'], 'combat_pos' => $map_info['combat_pos']));
			
			//Y a-t-il une team de mob ?
			$ia_leader = "";
			$team_mob = -1;
			$team_user = -1;
			$tmp_team = explode(",", $team);
			$tmp_team[0] = explode(" ",$tmp_team[0]);
			$tmp_team[1] = explode(" ",$tmp_team[1]);
			if(is_mob($tmp_team[0][0])) { $team_mob = 0; $team_user = 1; }
			if(is_mob($tmp_team[1][0])) { $team_mob = 1; $team_user = 0; }
			
			//Si oui, le mob est-il dans un groupe de mob ?
			if($team_mob!=-1)
			{
				//Si oui, on modifie la team de mob, on rajoute tous le groupe
				if(groupe_mob($tmp_team[$team_mob][0])!=null)
				{
					$tmp_team[$team_mob] = groupe_mob($tmp_team[$team_mob][0]);
					$tmp_team[$team_user] = implode(" ",$tmp_team[$team_user]);
					$team = implode(",", $tmp_team);
					$tmp_team[0] = explode(" ",$tmp_team[0]);
					$tmp_team[1] = explode(" ",$tmp_team[1]);
				}
				
				//On sélectionne un ia_leader
				$ia_leader = $tmp_team[$team_user][random_int(0, count($tmp_team[$team_user]) - 1)];
			}
			
			//On choisit au hasard les coordonnées de positionnement
			$positionnement = explode(" ", $map_info['combat_pos']);
			$combat_pos = $positionnement[random_int(0, count($positionnement) - 1)];
			
			//On récupère les coordonnées de positionnement
			$REQ = $bdd->prepare('SELECT position FROM table_position WHERE id=:id');
			$REQ->execute(array('id' => $combat_pos));
			$pos_place = $REQ->fetch()[0];
			
			//On choisit au hasard le placement des teams
			$pos_place = explode("/", $pos_place);
			$tmp = $pos_place[0];
			
			if(random_int(0, 1)==1) { $pos_place[0]=$pos_place[1]; $pos_place[1]=$tmp; }
			
			$pos_place = implode("/", $pos_place);
			
			//On enregistre le combat
			$req=$bdd->prepare('INSERT INTO combat_setup (id, map_clone, position, team, ia_leader, pos_place, timestamp) VALUES (:id, :map_clone, :position, :team, :ia_leader, :pos_place, :timestamp)');
			$req->execute(array('id' => $id, 'map_clone' => $map_name, 'position' => $position, 'team' => $team, 'ia_leader' => $ia_leader, 'pos_place' => $pos_place, 'timestamp' => microtime(true)));
			
			//On crée les modifs pour une équipe
			$position = explode(",", $position);
			$team = explode(",", $team);
			if($team_mob==0) { $is_mob = 1; } else { $is_mob = 0; }
			$modif = "COMBAT $id ".$position[0]." $is_mob ".str_replace(" ", ",", $team[0]); //COMBAT ID X Y IS_MOB TEAM
			
			$req=$bdd->prepare('INSERT INTO modif (id_user, name_map, modif, timestamp) VALUES (:id_user, :name_map, :modif, :timestamp)');
			$req->execute(array('id_user' => $user, 'name_map' => $map_name, 'modif' => $modif, 'timestamp' => microtime(true)+15));
			
			//Et pour l'autre
			if($team_mob==1) { $is_mob = 1; } else { $is_mob = 0; }
			$modif = "COMBAT $id ".$position[1]." $is_mob ".str_replace(" ", ",", $team[1]); //COMBAT ID X Y TEAM
			
			$req=$bdd->prepare('INSERT INTO modif (id_user, name_map, modif, timestamp) VALUES (:id_user, :name_map, :modif, :timestamp)');
			$req->execute(array('id_user' => $user, 'name_map' => $map_name, 'modif' => $modif, 'timestamp' => microtime(true)+15));
			
			//On téléporte toutes les teams
			$tmp_team = implode(" ", $team);
			$tmp_team = explode(" ", $tmp_team);
			
			for($i=0; $i<count($tmp_team); $i++)
			{
				//On prévoit les bugs où des mobs seraient déjà parti en combat, leur map de base deviendrait une map de combat
				if(user_info($tmp_team[$i])['map_tmp']=="")
				{
					$req = $bdd->prepare('UPDATE user SET map_tmp=map, map=:map, position=:position WHERE id=:id');
					$req->execute(array('id' => $tmp_team[$i], 'map' => $id, 'position' => "0 0"));
				}
				
				$req=$bdd->prepare('INSERT INTO modif (id_user, name_map, modif, timestamp) VALUES (:id_user, :name_map, :modif, :timestamp)');
				$req->execute(array('id_user' => $user, 'name_map' => $id, 'modif' => "REFRESH ".$tmp_team[$i], 'timestamp' => microtime(true)));
			}
		}
		
		//Rajout d'un user dans une team
		else
		{
			$REQ = $bdd->prepare('SELECT * FROM combat_setup WHERE id=:id');
			$REQ->execute(array('id' => $id));
			$res = $REQ->fetch(PDO::FETCH_ASSOC);
			
			$res['position'] = explode(",", $res['position']);
			$res['team'] = explode(",", $res['team']);
			
			//var_dump($res);
			
			//On vérifie que le nouvel user n'est dans aucune team
			if(!(strpos($res['team'][0], $user) !== false or strpos($res['team'][1], $user) !== false))
			{
				if($position == $res['position'][0] or $position == $res['position'][1])
				{
					if($position == $res['position'][0]) { $pos = 0; }
					if($position == $res['position'][1]) { $pos = 1; }
					
					//S'il y a moins de 4 joueurs dans la team, on ajoute l'user
					if(count($res['team'][$pos]) < 4)
					{
						$res['team'][$pos] .= " $user";
						
						$req = $bdd->prepare('UPDATE combat_setup SET team=:team WHERE id=:id');
						$req->execute(array('id' => $id, 'team' => implode(",", $res['team'])));
						
						$modif = "COMBAT $id ".$position." ".str_replace(" ", ",", $res['team'][$pos]); //COMBAT ID X Y TEAM
						$req=$bdd->prepare('INSERT INTO modif (id_user, name_map, modif, timestamp) VALUES (:id_user, :name_map, :modif, :timestamp)');
						$req->execute(array('id_user' => $user, 'name_map' => $map_name, 'modif' => $modif, 'timestamp' => microtime(true)));
						
						//On téléporte l'user
						$req = $bdd->prepare('UPDATE user SET map_tmp=map, map=:map, position=:position WHERE id=:id');
						$req->execute(array('id' => $user, 'map' => $id, 'position' => "0 0"));
						
						$req=$bdd->prepare('INSERT INTO modif (id_user, name_map, modif, timestamp) VALUES (:id_user, :name_map, :modif, :timestamp)');
						$req->execute(array('id_user' => $user, 'name_map' => $map_name, 'modif' => "REFRESH ".$user, 'timestamp' => microtime(true)));
						
						$req=$bdd->prepare('INSERT INTO modif (id_user, name_map, modif, timestamp) VALUES (:id_user, :name_map, :modif, :timestamp)');
						$req->execute(array('id_user' => $user, 'name_map' => $id, 'modif' => "REFRESH ".$user, 'timestamp' => microtime(true)));
						
						//On envoie la mise à jour des équipes dans la map combat
						$req=$bdd->prepare('INSERT INTO modif (id_user, name_map, modif, timestamp) VALUES (:id_user, :name_map, :modif, :timestamp)');
						$req->execute(array('id_user' => $user, 'name_map' => $id, 'modif' => "COMBAT_GROUPE ".str_replace(" ", "/",implode(",", $res['team'])), 'timestamp' => microtime(true)));
					}
				}
			}
		}
	}
?>