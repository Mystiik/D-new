<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bdd_connexion.php');

//################################
//###########   USER   ###########
//################################
function user_info($user)
{
	global $bdd;

	$req = $bdd->prepare('SELECT * FROM user WHERE id=:id');
	$req->execute(array('id' => $user));

	return $req->fetch(PDO::FETCH_ASSOC);
}

//#################################
//###########    MOB    ###########
//#################################

function is_mob($user)
{
	global $bdd;

	$REQ = $bdd->prepare('SELECT skin FROM user WHERE id=:id');
	$REQ->execute(array('id' => $user));
	$res = $REQ->fetch(PDO::FETCH_ASSOC)['skin'];

	if ($res < 1000) {
		return false;
	} else {
		return true;
	}
}

function groupe_mob($user)
{
	global $bdd;

	$REQ = $bdd->prepare('SELECT groupe FROM groupe WHERE chef=:id');
	$REQ->execute(array('id' => $user));
	$res = $REQ->fetch(PDO::FETCH_ASSOC)['groupe'];

	return $res;
}