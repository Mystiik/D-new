<?php

namespace Dnew;

// Déclaration des méthodes manuellement car gérées automatiquement par \GN\GlbObjFunc\__Get
/**
 * @method getId()
 * @method getPseudo()
 * @method getMap()
 * @method getMapTmp()
 * @method getPosition()
 * @method getSkin()
 * @method getLastSeen()
 * @method getVie()
 * @method getVieMax()
 * @method getBouclier()
 * @method getBouclierMax()
 * @method getVitesse()
 * @method getPA()
 * @method getPAMax()
 * @method getPM()
 * @method getPMMax()
 * @method getInitiative()
 */
class User
{
	use \GN\GlbObjFunc\__Get;
	use \GN\GlbObjFunc\Hydrate;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $pseudo;

	/**
	 * @var string
	 */
	private $map;

	/**
	 * @var string
	 */
	private $mapTmp;

	/**
	 * @var string "X Y"
	 */
	private $position;

	/**
	 * @var string
	 */
	private $skin;

	/**
	 * @var string
	 */
	private $lastSeen;

	/**
	 * @var string
	 */
	private $vie;

	/**
	 * @var string
	 */
	private $vieMax;

	/**
	 * @var string
	 */
	private $bouclier;

	/**
	 * @var string
	 */
	private $bouclierMax;

	/**
	 * @var string
	 */
	private $vitesse;

	/**
	 * @var string
	 */
	private $PA;

	/**
	 * @var string
	 */
	private $PAMax;

	/**
	 * @var string
	 */
	private $PM;

	/**
	 * @var string
	 */
	private $PMMax;

	/**
	 * @var string
	 */
	private $initiative;


	function __construct(string $id)
	{
		$this->id = $id;

		// Si l'utilisateur se connecte pour la première fois, on le crée en bdd
		if (!$this->isCreatedInBdd()) {
			$this->createInBdd();
		}

		$infos = $this->getInfos();
		$this->hydrate($infos);
	}

	//-----------------------------------------------------------------------------------
	// Basic function
	//-----------------------------------------------------------------------------------
	private function isCreatedInBdd()
	{
		global $bdd;

		$req = $bdd->prepare('SELECT COUNT(*) FROM user WHERE id=:id');
		$req->execute(array('id' => $this->getId()));
		$req = $req->fetch()[0];

		if ($req == 0) {
			return false;
		}
		return true;
	}

	private function createInBdd()
	{
		global $bdd;

		$req = $bdd->prepare('INSERT INTO user (id) VALUES (:id)');
		$req->execute(array('id' => $this->getId()));
	}

	/**
	 * Retourne les infos de la base
	 *
	 * @return array all columns from 'user' table
	 */
	private function getInfos()
	{
		global $bdd;

		$req = $bdd->prepare('SELECT * FROM user WHERE id=:id');
		$req->execute(array('id' => $this->getId()));

		return $req->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Récupère les infos de la base
	 *
	 * @return array all columns from 'user' table
	 */
	public function serializeUser()
	{
		return
			$this->getId() . " " .
			$this->getPosition() . " " .
			$this->getPseudo() . " " .
			$this->getVie() . " " .
			$this->getVieMax() . " " .
			$this->getBouclier() . " " .
			$this->getBouclierMax() . " " .
			$this->getVitesse() . " " .
			$this->getPA() . " " .
			$this->getPM();
	}


	public function sendModif($modif, $mapName = "")
	{
		global $bdd;

		if ($mapName == "") {
			$mapName = $this->getMap();
		}

		$req = $bdd->prepare('INSERT INTO modif (userId, mapName, modif, timestamp) VALUES (:userId, :mapName, :modif, :timestamp)');
		$req->execute(array('userId' => $this->getId(), 'mapName' => $mapName, 'modif' => $modif, 'timestamp' => microtime(true)));
	}


	//-----------------------------------------------------------------------------------

	/**
	 * Contient les modifications de la map depuis notre dernière requête
	 *
	 * @return array
	 */
	public function getMapModif()
	{
		global $bdd;

		$req = $bdd->prepare('SELECT modif.modif FROM modif INNER JOIN user ON user.map=modif.mapName WHERE user.id=:id AND modif.timestamp>=user.lastSeen');
		$req->execute(array('id' => $this->getId()));
		$req = $req->fetchALL();

		for ($i = 0; $i < count($req); $i++) {
			$req[$i] = $req[$i]['modif'];
		}

		return $req;
	}

	// LastSeen
	public function updateLastSeen()
	{
		global $bdd;
		$this->lastSeen = microtime(true);

		$req = $bdd->prepare('UPDATE user SET lastSeen=:lastSeen WHERE id=:id');
		$req->execute(array('id' => $this->getId(), 'lastSeen' => $this->lastSeen));
	}
}