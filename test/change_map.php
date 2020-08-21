<?php
	if(isset($_POST['user']))
	{
		include_once('bdd_connexion.php');
		include_once('bdd_function.php');
		
		$user = "6wllEXObPTlGv8lSjXMH";
		$map = "0-0";
		
		$REQ = $bdd->prepare('UPDATE user SET map=:map, last_seen=:last_seen WHERE id=:id');
		$REQ->execute(array('id' => $user, 'map' => $map, 'last_seen' => microtime(true)-2));
?>