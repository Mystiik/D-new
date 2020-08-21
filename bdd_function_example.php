<?php
	####################################################
	##                                                ##
	##             VERIFICATION DE DONNEE             ##
	##                                                ##
	####################################################
	
	function verif_user($login, $pwd)
	{
		// But: Vérifie un utilisateur
		// prend le login et le password en clair en argument
		// renvoie True si la personne est bien identifiée, False sinon
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT id, password FROM user
								WHERE login=:login');
		$req->execute(array('login' => $login));
		
		$req = $req->fetch();
		$hash = $req['password'];
		
		// Vérifions d'abord que le mot de passe correspond au hachage stocké
		if (password_verify($pwd, $hash))
		{
			/*
			// Le hachage correspond, on vérifie au cas où un nouvel algorithme de hachage
			// serait disponible ou si le coût a été changé
			
			if (password_needs_rehash($hash, PASSWORD_DEFAULT))
			{
				// On crée un nouveau hachage afin de mettre à jour l'ancien
				$newHash = password_hash($pwd, PASSWORD_DEFAULT);
			}
			*/
			
			// On connecte l'utilisateur
			return $req['id'];
		}
		else { return "0"; }
	}
	
	function verif_cristaux($id, $amount)
	{
		// But: Vérifie si un utilisateur a plus de $amount cristaux
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT cristaux FROM inventaire
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$cristaux = $req->fetch()['cristaux'];
		
		if($cristaux >= $amount) { return True; }
		else { return False; }
	}
	
	####################################################
	##                                                ##
	##              INSERTION DE DONNEE               ##
	##                                                ##
	####################################################
	
	function nouvel_user($login, $pwd)
	{
		// But: Crée un nouveau utilisateur
		// prend le login et le password en clair en argument
		
		global $bdd;
		
		$pwd = password_hash($pwd, PASSWORD_DEFAULT);
		
		// Création de l'utilisateur
		$req=$bdd->prepare('INSERT INTO user (login, password) VALUES (:login, :pwd)');
		$req->execute(array('login' => $login,
							'pwd' => $pwd));
		
		// Sélection de l'id
		$id = $bdd->lastInsertId();
		
		if($id !=0)
		{
			// Création des données ig
			$req=$bdd->prepare('INSERT INTO ig_autre (id) VALUES (:id)');
			$req->execute(array('id' => $id));
			
			$req=$bdd->prepare('INSERT INTO ig_comp (id) VALUES (:id)');
			$req->execute(array('id' => $id));
			
			$req=$bdd->prepare('INSERT INTO ig_info (id) VALUES (:id)');
			$req->execute(array('id' => $id));
			
			$req=$bdd->prepare('INSERT INTO ig_mission (id, mission) VALUES (:id, :mission)');
			$req->execute(array('id' => $id, 'mission' => "1,/1,0;1,0;1,0;1,0;1,0;1,0;1,0;1,0;1,0;1,0;1,0;1,0;1,0;1,0;1,0"));
			
			// Création de l'inventaire
			$req=$bdd->prepare('INSERT INTO inventaire (id) VALUES (:id)');
			$req->execute(array('id' => $id));
			
			// Création du vaisseau
			$no_vsx = insert_vsx(0, $id);
			
			// Sélection du vaisseau créé
			selected_vsx($id, $no_vsx);
			
			// Création d'une nouvelle arme
			$no_las = insert_las(1, $id);
			
			// Equipement de l'arme créée
			equip_las($no_vsx, $no_las);
			
			return "True";
		}
		else { return "False"; }
	}
	
	function insert_new()
	{
		// But: Crée une nouvelle entrée dans inv_ref
		
		global $bdd;
		
		$req=$bdd->prepare('INSERT INTO inv_ref () VALUES ()');
		$req->execute();
		
		$req = $bdd->prepare('SELECT id FROM inv_ref ORDER BY id DESC');
		$req->execute();
		
		return $req->fetch()['id'];
	}
	
	function insert_vsx($type, $id)
	{
		// But: Crée un nouveau vaisseau
		// prend le type du vaisseau et l'id du possesseur en argument
		
		global $bdd;
		
		$no = insert_new();
		
		$req=$bdd->prepare('INSERT INTO inv_vsx (id, type, possesseur) VALUES (:no, :type, :id)');
		$req->execute(array('no' => $no,
							'type' => $type,
							'id' => $id));
		
		return $no;
	}
	
	function insert_las($type, $id)
	{
		// But: Crée un nouveau laser
		// prend le type du laser et l'id du possesseur en argument
		
		global $bdd;
		
		$no = insert_new();
		
		$req=$bdd->prepare('INSERT INTO inv_las (id, type, possesseur) VALUES (:no, :type, :id)');
		$req->execute(array('no' => $no,
							'type' => $type,
							'id' => $id));
		
		return $no;
	}
	
	
	function insert_arm($type, $id)
	{
		// But: Crée une nouvelle armure
		// prend le type de l'armure et l'id du possesseur en argument
		
		global $bdd;
		
		$no = insert_new();
		
		$req=$bdd->prepare('INSERT INTO inv_arm (id, type, possesseur) VALUES (:no, :type, :id)');
		$req->execute(array('no' => $no,
							'type' => $type,
							'id' => $id));
		
		return $no;
	}
	
	function insert_bou($type, $id)
	{
		// But: Crée un nouveau bouclier
		// prend le type du bouclier et l'id du possesseur en argument
		
		global $bdd;
		
		$no = insert_new();
		
		$req=$bdd->prepare('INSERT INTO inv_bou (id, type, possesseur) VALUES (:no, :type, :id)');
		$req->execute(array('no' => $no,
							'type' => $type,
							'id' => $id));
		
		return $no;
	}
	
	function insert_mot($type, $id)
	{
		// But: Crée un nouveau moteur
		// prend le type du moteur et l'id du possesseur en argument
		
		global $bdd;
		
		$no = insert_new();
		
		$req=$bdd->prepare('INSERT INTO inv_mot (id, type, possesseur) VALUES (:no, :type, :id)');
		$req->execute(array('no' => $no,
							'type' => $type,
							'id' => $id));
		return $no;
	}
	
	function insert_ext($type, $id)
	{
		// But: Crée un nouvel extra
		// prend le type de l'extra et l'id du possesseur en argument
		
		global $bdd;
		
		$no = insert_new();
		
		$req=$bdd->prepare('INSERT INTO inv_ext (id, type, possesseur) VALUES (:no, :type, :id)');
		$req->execute(array('no' => $no,
							'type' => $type,
							'id' => $id));
		
		return $no;
	}
	
	####################################################
	##                                                ##
	##             MISE A JOUR DE DONNEE              ##
	##                                                ##
	####################################################
	
	function maj_pos($id, $posx, $posy, $dir, $esp)
	{
		// But: Met àjour la position d'un user
		
		global $bdd;
		
		$req = $bdd->prepare('UPDATE ig_info
								SET posx=:posx, posy=:posy, dir=:dir, espace=:esp
								WHERE id=:id');
		$req->execute(array('posx' => $posx,
							'posy' => $posy,
							'dir' => $dir,
							'esp' => $esp,
							'id' => $id));
	}
	
	####################################################
	##                                                ##
	##             SUPPRESSION DE DONNEE              ##
	##                                                ##
	####################################################
	
	function delete_las($id)
	{
		// But: Détruit un laser
		
		global $bdd;
		
		$req = $bdd->prepare('DELETE FROM inv_las
								WHERE id=:id');
		$req->execute(array('id' => $id));
	}
	
	function delete_arm($id)
	{
		// But: Détruit un laser
		
		global $bdd;
		
		$req = $bdd->prepare('DELETE FROM inv_arm
								WHERE id=:id');
		$req->execute(array('id' => $id));
	}
	
	function delete_bou($id)
	{
		// But: Détruit un laser
		
		global $bdd;
		
		$req = $bdd->prepare('DELETE FROM inv_bou
								WHERE id=:id');
		$req->execute(array('id' => $id));
	}
	
	function delete_mot($id)
	{
		// But: Détruit un laser
		
		global $bdd;
		
		$req = $bdd->prepare('DELETE FROM inv_mot
								WHERE id=:id');
		$req->execute(array('id' => $id));
	}
	
	function delete_ext($id)
	{
		// But: Détruit un laser
		
		global $bdd;
		
		$req = $bdd->prepare('DELETE FROM inv_ext
								WHERE id=:id');
		$req->execute(array('id' => $id));
	}
	
	####################################################
	##                                                ##
	##                  INVENTAIRE                    ##
	##                                                ##
	####################################################
	
	function recup_inv($id){
		// But: Récupere l'inventaire d'un user
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM inventaire WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch();
	}
	
	function maj_inv($id, $C, $P, $E, $H){
		// But: Met à jour l'inventaire d'un user
		global $bdd;
		
		$req = $bdd->prepare('UPDATE inventaire SET cristaux=:C, photon=:P, experience=:E, honneur=:H WHERE id=:id');
		$req->execute(array('id' => $id, 'C' => $C, 'P' => $P, 'E' => $E, 'H' => $H));
	}
	
	function search_vsx($id) {
		// But: Cherche tous les vaisseaux d'un user
		// prend l'id du possesseur en argument
		// renvois tout les numéros uniques des vaisseaux trouvés
		global $bdd;
		
		$req = $bdd->prepare('SELECT id FROM inv_vsx WHERE possesseur=:id ORDER BY id');
		$req->execute(array('id' => $id));
		
		return $req->fetchALL();
	}
	
	function search_las($id)
	{
		// But: Cherche tous les lasers non équipés d'un user
		// prend l'id du possesseur en argument
		// renvois tout les numéros uniques des lasers trouvés
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT id, type FROM inv_las
								WHERE possesseur=:id AND equip=0');
		$req->execute(array('id' => $id));
		
		return $req->fetchALL();
	}
	
	function search_arm($id)
	{
		// But: Cherche toutes les armures non équipées d'un user
		// prend l'id du possesseur en argument
		// renvois tout les numéros uniques des armures trouvées
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT id, type FROM inv_arm
								WHERE possesseur=:id AND equip=0');
		$req->execute(array('id' => $id));
		
		return $req->fetchALL();
	}
	
	function search_bou($id)
	{
		// But: Cherche tous les boucliers non équipés d'un user
		// prend l'id du possesseur en argument
		// renvois tout les numéros uniques des boucliers trouvés
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT id, type FROM inv_bou
								WHERE possesseur=:id AND equip=0');
		$req->execute(array('id' => $id));
		
		return $req->fetchALL();
	}
	
	function search_mot($id)
	{
		// But: Cherche tous les moteurs non équipés d'un user
		// prend l'id du possesseur en argument
		// renvois tout les numéros uniques des moteurs trouvés
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT id, type FROM inv_mot
								WHERE possesseur=:id AND equip=0');
		$req->execute(array('id' => $id));
		
		return $req->fetchALL();
	}
	
	function search_ext($id)
	{
		// But: Cherche tous les extras non équipés d'un user
		// prend l'id du possesseur en argument
		// renvois tout les numéros uniques des extras trouvés
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT id, type FROM inv_ext
								WHERE possesseur=:id AND equip=0');
		$req->execute(array('id' => $id));
		
		return $req->fetchALL();
	}
	
	function recup_info_vsx($id)
	{
		// But: Récupère les infos d'un vaisseau
		// prend l'id du vaisseau en argument
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM inv_vsx
								INNER JOIN com_vsx ON inv_vsx.type=com_vsx.type
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch();
	}
	
	function recup_info_las($id)
	{
		// But: Récupère les infos d'un laser
		// prend l'id du laser en argument
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM inv_las
								INNER JOIN com_las ON inv_las.type=com_las.type
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch();
	}
	
	function recup_info_arm($id)
	{
		// But: Récupère les infos d'une armure
		// prend l'id de l'armure en argument
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM inv_arm
								INNER JOIN com_arm ON inv_arm.type=com_arm.type
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch();
	}
	
	function recup_info_bou($id)
	{
		// But: Récupère les infos d'un bouclier
		// prend l'id du bouclier en argument
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM inv_bou
								INNER JOIN com_bou ON inv_bou.type=com_bou.type
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch();
	}
	
	function recup_info_mot($id)
	{
		// But: Récupère les infos d'un moteur
		// prend l'id du moteur en argument
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM inv_mot
								INNER JOIN com_mot ON inv_mot.type=com_mot.type
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch();
	}
	
	function recup_info_ext($id)
	{
		// But: Récupère les infos d'un extra
		// prend l'id de l'extra en argument
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM inv_ext
								INNER JOIN com_ext ON inv_ext.type=com_ext.type
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch();
	}
	
	function set_equip($no)
	{
		// But: Met un objet dans l'état équipé
		
		global $bdd;
		
		$req = $bdd->prepare('UPDATE inv_las SET equip=1 WHERE id=:id'); $req->execute(array('id' => $no));
		$req = $bdd->prepare('UPDATE inv_arm SET equip=1 WHERE id=:id'); $req->execute(array('id' => $no));
		$req = $bdd->prepare('UPDATE inv_bou SET equip=1 WHERE id=:id'); $req->execute(array('id' => $no));
		$req = $bdd->prepare('UPDATE inv_mot SET equip=1 WHERE id=:id'); $req->execute(array('id' => $no));
		$req = $bdd->prepare('UPDATE inv_ext SET equip=1 WHERE id=:id'); $req->execute(array('id' => $no));
	}
	
	function set_desequip($no)
	{
		// But: Met un objet dans l'état équipé
		
		global $bdd;
		
		$req = $bdd->prepare('UPDATE inv_las SET equip=0 WHERE id=:id'); $req->execute(array('id' => $no));
		$req = $bdd->prepare('UPDATE inv_arm SET equip=0 WHERE id=:id'); $req->execute(array('id' => $no));
		$req = $bdd->prepare('UPDATE inv_bou SET equip=0 WHERE id=:id'); $req->execute(array('id' => $no));
		$req = $bdd->prepare('UPDATE inv_mot SET equip=0 WHERE id=:id'); $req->execute(array('id' => $no));
		$req = $bdd->prepare('UPDATE inv_ext SET equip=0 WHERE id=:id'); $req->execute(array('id' => $no));
	}
	
	function nb_place_inv($id)
	{
		// But: Récupère le nombre de place de l'inventaire
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT place_inv FROM inventaire
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch()['place_inv'];
	}
	
	####################################################
	##                                                ##
	##                   COMMERCE                     ##
	##                                                ##
	####################################################
	
	function recup_com_vsx()
	{
		// But: Cherche tous les vaisseaux à vendre
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM com_vsx
								ORDER BY type');
		$req->execute();
		
		return $req->fetchALL();
	}
	
	function recup_com_las()
	{
		// But: Cherche tous les lasers à vendre
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM com_las
								ORDER BY type');
		$req->execute();
		
		return $req->fetchALL();
	}
	
	function recup_com_arm()
	{
		// But: Cherche toutes les armures à vendre
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM com_arm
								ORDER BY type');
		$req->execute();
		
		return $req->fetchALL();
	}
	
	function recup_com_bou()
	{
		// But: Cherche tous les boucliers à vendre
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM com_bou
								ORDER BY type');
		$req->execute();
		
		return $req->fetchALL();
	}
	
	function recup_com_mot()
	{
		// But: Cherche tous les moteurs à vendre
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM com_mot
								ORDER BY type');
		$req->execute();
		
		return $req->fetchALL();
	}
	
	function recup_com_ext()
	{
		// But: Cherche tous les extras à vendre
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM com_ext
								ORDER BY type');
		$req->execute();
		
		return $req->fetchALL();
	}
	
	function recup_selected_vsx($id)
	{
		// But: Renvoi le no du vaisseau sélectionné d'un user
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT selected_vsx FROM inventaire
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch()['selected_vsx'];
	}
	
	function cristaux_plus($id, $amount)
	{
		// But: Ajoute $amount cristaux
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT cristaux FROM inventaire
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$cristaux = $req->fetch()['cristaux']+$amount;
		
		$req = $bdd->prepare('UPDATE inventaire
								SET cristaux=:cristaux
								WHERE id=:id');
		$req->execute(array('cristaux' => $cristaux,
							'id' => $id));
	}
	
	####################################################
	##                                                ##
	##                 AMELIORATION                   ##
	##                                                ##
	####################################################
	
	function selected_vsx($id, $no)
	{
		// But: Sélectionne un vsx d'un user
		
		global $bdd;
		
		$req = $bdd->prepare('UPDATE inventaire
								SET selected_vsx=:selected_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id,
							'selected_vsx' => $no));
		
		maj_ig_info($id, "egal");
	}
	
	function no_vsx($id, $type)
	{
		// But: Renvoi le no d'un vsx à partir de son type et de son possesseur
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT id FROM inv_vsx
								WHERE possesseur=:id AND type=:type');
		$req->execute(array('id' => $id,
							'type' => $type));
		
		return $req->fetch()['id'];
	}
	
	####################################################
	##                                                ##
	##                  EQUIPEMENT                    ##
	##                                                ##
	####################################################
	
	function equip_las($id, $no) // SI IL NEST PAS DEJA EQUIPE
	{
		// But: Equipe un laser à un vaisseau
		global $bdd;
		
		$req = $bdd->prepare('SELECT las FROM inv_vsx WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$las = $req->fetch()['las'].",".$no;
		
		$req = $bdd->prepare('UPDATE inv_vsx SET las=:las WHERE id=:id');
		$req->execute(array('las' => $las, 'id' => $id));
		
		set_equip($no);
	}
	
	function equip_arm($id, $no)
	{
		// But: Equipe une armure à un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT arm FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$arm = $req->fetch()['arm'].",".$no;
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET arm=:arm
								WHERE id=:id');
		$req->execute(array('arm' => $arm,
							'id' => $id));
		
		set_equip($no);
	}
	
	function equip_bou($id, $no)
	{
		// But: Equipe un bouclier à un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT bou FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$bou = $req->fetch()['bou'].",".$no;
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET bou=:bou
								WHERE id=:id');
		$req->execute(array('bou' => $bou,
							'id' => $id));
		
		set_equip($no);
	}
	
	function equip_mot($id, $no)
	{
		// But: Equipe un extra à un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT mot FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$mot = $req->fetch()['mot'].",".$no;
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET mot=:mot
								WHERE id=:id');
		$req->execute(array('mot' => $mot,
							'id' => $id));
		
		set_equip($no);
	}
	
	function equip_ext($id, $no)
	{
		// But: Equipe un extra à un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT ext FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$ext = $req->fetch()['ext'].",".$no;
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET ext=:ext
								WHERE id=:id');
		$req->execute(array('ext' => $ext,
							'id' => $id));
		
		set_equip($no);
	}
	
	function equip_mods($id, $no)
	{
		// But: Equipe une modification à un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT mods FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$mods = $req->fetch()['mods'].",".$no;
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET mods=:mods
								WHERE id=:id');
		$req->execute(array('mods' => $mods,
							'id' => $id));
		
		set_equip($no);
	}
	
	function desequip_las($id, $no)
	{
		// But: Desequipe un laser d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT las FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$las = explode(",", $req->fetch()['las']);
		$key = array_search($no, $las);
		if($key !== False) { unset($las[$key]); }
		$las = implode(",", $las);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET las=:las
								WHERE id=:id');
		$req->execute(array('las' => $las,
							'id' => $id));
		
		set_desequip($no);
		
		if($key === False) { return False; }
		else { return True; }
	}
	
	function desequip_arm($id, $no)
	{
		// But: Desequipe un laser d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT arm FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$arm = explode(",", $req->fetch()['arm']);
		$key = array_search($no, $arm);
		if($key !== False) { unset($arm[$key]); }
		$arm = implode(",", $arm);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET arm=:arm
								WHERE id=:id');
		$req->execute(array('arm' => $arm,
							'id' => $id));
		
		set_desequip($no);
		
		if($key === False) { return False; }
		else { return True; }
	}
	
	function desequip_bou($id, $no)
	{
		// But: Desequipe un laser d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT bou FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$bou = explode(",", $req->fetch()['bou']);
		$key = array_search($no, $bou);
		if($key !== False) { unset($bou[$key]); }
		$bou = implode(",", $bou);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET bou=:bou
								WHERE id=:id');
		$req->execute(array('bou' => $bou,
							'id' => $id));
		
		set_desequip($no);
		
		if($key === False) { return False; }
		else { return True; }
	}
	
	function desequip_mot($id, $no)
	{
		// But: Desequipe un laser d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT mot FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$mot = explode(",", $req->fetch()['mot']);
		$key = array_search($no, $mot);
		if($key !== False) { unset($mot[$key]); }
		$mot = implode(",", $mot);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET mot=:mot
								WHERE id=:id');
		$req->execute(array('mot' => $mot,
							'id' => $id));
		
		set_desequip($no);
		
		if($key === False) { return False; }
		else { return True; }
	}
	
	function desequip_ext($id, $no)
	{
		// But: Desequipe un laser d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT ext FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$ext = explode(",", $req->fetch()['ext']);
		$key = array_search($no, $ext);
		if($key !== False) { unset($ext[$key]); }
		$ext = implode(",", $ext);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET ext=:ext
								WHERE id=:id');
		$req->execute(array('ext' => $ext,
							'id' => $id));
		
		set_desequip($no);
		
		if($key === False) { return False; }
		else { return True; }
	}
	
	function desequip_mods($id, $no)
	{
		// But: Desequipe un laser d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT mods FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$mods = explode(",", $req->fetch()['mods']);
		$key = array_search($no, $mods);
		if($key !== False) { unset($mods[$key]); }
		$mods = implode(",", $mods);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET mods=:mods
								WHERE id=:id');
		$req->execute(array('mods' => $mods,
							'id' => $id));
		
		set_desequip($no);
		
		if($key === False) { return False; }
		else { return True; }
	}
	
	function swap_las($id, $no1, $no2)
	{
		// But: Swap 2 lasers d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT las FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$las = explode(",", $req->fetch()['las']);
		$key1 = array_search($no1, $las);
		$key2 = array_search($no2, $las);
		if($key1 !== False AND $key2 !== False)
		{
			$tmp = $las[$key1];
			$las[$key1] = $las[$key2];
			$las[$key2] = $tmp;
		}
		$las = implode(",", $las);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET las=:las
								WHERE id=:id');
		$req->execute(array('las' => $las,
							'id' => $id));
	}
	
	function swap_arm($id, $no1, $no2)
	{
		// But: Swap 2 lasers d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT arm FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$arm = explode(",", $req->fetch()['arm']);
		$key1 = array_search($no1, $arm);
		$key2 = array_search($no2, $arm);
		if($key1 !== False AND $key2 !== False)
		{
			$tmp = $arm[$key1];
			$arm[$key1] = $arm[$key2];
			$arm[$key2] = $tmp;
		}
		$arm = implode(",", $arm);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET arm=:arm
								WHERE id=:id');
		$req->execute(array('arm' => $arm,
							'id' => $id));
	}
	
	function swap_bou($id, $no1, $no2)
	{
		// But: Swap 2 lasers d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT bou FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$bou = explode(",", $req->fetch()['bou']);
		$key1 = array_search($no1, $bou);
		$key2 = array_search($no2, $bou);
		if($key1 !== False AND $key2 !== False)
		{
			$tmp = $bou[$key1];
			$bou[$key1] = $bou[$key2];
			$bou[$key2] = $tmp;
		}
		$bou = implode(",", $bou);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET bou=:bou
								WHERE id=:id');
		$req->execute(array('bou' => $bou,
							'id' => $id));
	}
	
	function swap_mot($id, $no1, $no2)
	{
		// But: Swap 2 lasers d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT mot FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$mot = explode(",", $req->fetch()['mot']);
		$key1 = array_search($no1, $mot);
		$key2 = array_search($no2, $mot);
		if($key1 !== False AND $key2 !== False)
		{
			$tmp = $mot[$key1];
			$mot[$key1] = $mot[$key2];
			$mot[$key2] = $tmp;
		}
		$mot = implode(",", $mot);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET mot=:mot
								WHERE id=:id');
		$req->execute(array('mot' => $mot,
							'id' => $id));
	}
	
	function swap_ext($id, $no1, $no2)
	{
		// But: Swap 2 lasers d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT ext FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$ext = explode(",", $req->fetch()['ext']);
		$key1 = array_search($no1, $ext);
		$key2 = array_search($no2, $ext);
		if($key1 !== False AND $key2 !== False)
		{
			$tmp = $ext[$key1];
			$ext[$key1] = $ext[$key2];
			$ext[$key2] = $tmp;
		}
		$ext = implode(",", $ext);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET ext=:ext
								WHERE id=:id');
		$req->execute(array('ext' => $ext,
							'id' => $id));
	}
	
	function swap_mods($id, $no1, $no2)
	{
		// But: Swap 2 lasers d'un vaisseau
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT mods FROM inv_vsx
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$mods = explode(",", $req->fetch()['mods']);
		$key1 = array_search($no1, $mods);
		$key2 = array_search($no2, $mods);
		if($key1 !== False AND $key2 !== False)
		{
			$tmp = $mods[$key1];
			$mods[$key1] = $mods[$key2];
			$mods[$key2] = $tmp;
		}
		$mods = implode(",", $mods);
		
		$req = $bdd->prepare('UPDATE inv_vsx
								SET mods=:mods
								WHERE id=:id');
		$req->execute(array('mods' => $mods,
							'id' => $id));
	}
	
	####################################################
	##                                                ##
	##                    MISSION                     ##
	##                                                ##
	####################################################
	
	function recup_mission($id)
	{
		// But: Récupere les missions d'un user
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT mission FROM ig_mission
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch()['mission'];
	}
	
	function maj_mission($id, $mission)
	{
		// But: Récupere les missions d'un user
		
		global $bdd;
		
		$req = $bdd->prepare('UPDATE ig_mission
								SET mission=:mission
								WHERE id=:id');
		$req->execute(array('mission' => $mission,
							'id' => $id));
		
		/*$req2=$bdd->prepare('INSERT INTO ig_mission (id, mission) VALUES (:id, :mission)');
		$req2->execute(array('mission' => $mission,
							'id' => rand(1, 10000)));*/
	}
	
	function maj_cmpt($id, $place, $no_cmpt)
	{
		// But: Met à jour une compétence d'un user
		
		global $bdd;
		
		$req = $bdd->prepare('SELECT comp FROM ig_comp
								WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$cmpt = $req->fetch()['comp'];
		$cmpt = explode(",", $cmpt);
		$cmpt[$place-1] = $no_cmpt;
		$cmpt = implode(",", $cmpt);
		
		$req = $bdd->prepare('UPDATE ig_comp
								SET comp=:cmpt
								WHERE id=:id');
		$req->execute(array('cmpt' => $cmpt,
							'id' => $id));
	}
	
	
	####################################################
	##                                                ##
	##                  COMPETENCE                    ##
	##                                                ##
	####################################################
	
	
	function recup_cmpt_classe($classe) {
		// But: Récupere les id des compétenecs d'une classe
		global $bdd;
		
		$req = $bdd->prepare('SELECT id, degat FROM competence WHERE classe=:classe');
		$req->execute(array('classe' => $classe));
		
		return $req->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function info_cmpt($no) {
		// But: Recupere les infos d'une compétence
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM competence WHERE id=:id');
		$req->execute(array('id' => $no));
		
		return $req->fetch(PDO::FETCH_ASSOC);
	}
	
	function recup_ig_comp($id) {
		// But: Recupere les infos ig_comp d'un user
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM ig_comp WHERE id=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch();
	}
	
	function set_ig_comp($id, $comp) {
		// But: Met à jour les infos ig_comp d'un user
		global $bdd;
		
		$req = $bdd->prepare('UPDATE ig_comp SET comp=:comp WHERE id=:id');
		$req->execute(array('id' => $id, 'comp' => $comp));
	}
	
	function up_comp($id, $cmpt) {
		// But: Met à jour les infos in_comp d'un user
		global $bdd;
		
		$req = $bdd->prepare('INSERT INTO ig_amelio_cmpt(possesseur, cmpt) VALUES (:id, :cmpt)');
		$req->execute(array('id' => $id, 'cmpt' => $cmpt));
	}
	
	function lvl_tot_comp($id) {
		// But: Met à jour les infos in_comp d'un user
		global $bdd;
		
		$req = $bdd->prepare('SELECT COUNT(*) FROM ig_amelio_cmpt WHERE possesseur=:id');
		$req->execute(array('id' => $id));
		
		return $req->fetch()[0];
	}
	
	function lvl_comp($id, $no) {
		// But: Met à jour les infos in_comp d'un user
		global $bdd;
		
		$req = $bdd->prepare('SELECT COUNT(*) FROM ig_amelio_cmpt WHERE cmpt=:no AND possesseur=:id');
		$req->execute(array('no' => $no, 'id' => $id));
		
		return $req->fetch()[0];
	}
	
	function lvl_joueur($id) {
		// But: Retourne le niveau du joueur
		$inv = recup_inv($id);
			
		if($inv['experience'] < 2000) { $lvl = 1; }
		else { $lvl = floor( log($inv['experience']/2000)/log(2) )+2; }
		
		return $lvl;
	}
	
	function amelio_comp($id) {
		// But: Retourne le nombre de point de compétence restant
		return 5*(lvl_joueur($id)-1)-lvl_tot_comp($id);
	}
	
	function id_exist($id) {
		// But: Retourne true si id est trouvé, false sinon
		global $bdd;
		
		$req = $bdd->prepare('SELECT COUNT(id) FROM user WHERE id=:id');
		$req->execute(array('id' => $id));
		
		$result = $req->fetch()[0];
		
		if($result==0) { $result=false; }
		else { $result=true; }
		
		return $result;
	}
	
?>

 









