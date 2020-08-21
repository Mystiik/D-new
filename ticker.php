<?php
	include_once('bdd_connexion.php');
	include_once('bdd_function.php');
	
	$req = $bdd->prepare('SELECT type, timestamp FROM system WHERE type:type');
	$req->execute(array('type' => "ticker_map"));
	$ticker_map = $req->fetch()[0];
	
	$req = $bdd->prepare('SELECT type, timestamp FROM system WHERE type:type');
	$req->execute(array('type' => "ticker_fight"));
	$ticker_fight = $req->fetch()[0];
	
	//Update ticker quickly (to avoid multiple ticker been calculated at the same time)
	if($ticker_map<=microtime(true))
	{
		$req = $bdd->prepare('UPDATE system SET timestamp=:timestamp WHERE type=:type');
		$req->execute(array('type' => "ticker_map", 'timestamp' => microtime(true)+1));
	}
	if($ticker_fight<=microtime(true))
	{
		$req = $bdd->prepare('UPDATE system SET timestamp=:timestamp WHERE type=:type');
		$req->execute(array('type' => "ticker_fight", 'timestamp' => microtime(true)+0.2));
	}