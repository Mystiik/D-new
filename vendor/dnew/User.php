<?php

namespace Dnew;

class User
{
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


	function sendModif($modif, $mapName = "")
	{
		global $bdd;

		if ($mapName == "") {
			$mapName = $this->getMap();
		}

		$req = $bdd->prepare('INSERT INTO modif (userId, mapName, modif, timestamp) VALUES (:userId, :mapName, :modif, :timestamp)');
		$req->execute(array('userId' => $this->getId(), 'mapName' => $mapName, 'modif' => $modif, 'timestamp' => microtime(true)));
	}

	function __construct(string $id)
	{
		$this->setId($id);

		// Si l'utilisateur se connecte pour la première fois, on le crée en bdd
		if (!$this->isCreatedInBdd()) {
			$this->createInBdd();
		}

		$infos = $this->getInfos();
		$this->setPseudo($infos['pseudo']);
		$this->setMap($infos['map']);
		$this->setMapTmp($infos['mapTmp']);
		$this->setPosition($infos['position']);
		$this->setSkin($infos['skin']);
		$this->lastSeen = $infos['lastSeen'];
		$this->setVie($infos['vie']);
		$this->setVieMax($infos['vieMax']);
		$this->setBouclier($infos['bouclier']);
		$this->setBouclierMax($infos['bouclierMax']);
		$this->setVitesse($infos['vitesse']);
		$this->setPA($infos['PA']);
		$this->setPAMax($infos['PAMax']);
		$this->setPM($infos['PM']);
		$this->setPMMax($infos['PMMax']);
		$this->setinitiative($infos['initiative']);
	}

	public function isCreatedInBdd()
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

	// ################################################################################################################################################
	// ################################################################################################################################################

	// Id
	public function setId(string $id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	// Pseudo
	public function setPseudo(string $pseudo)
	{
		$this->pseudo = $pseudo;
	}

	public function getPseudo()
	{
		return $this->pseudo;
	}

	// Map
	public function setMap(string $map)
	{
		$this->map = $map;
	}

	public function getMap()
	{
		return $this->map;
	}

	// MapTmp
	public function setMapTmp(string $mapTmp)
	{
		$this->mapTmp = $mapTmp;
	}

	public function getMapTmp()
	{
		return $this->mapTmp;
	}

	// Position
	public function setPosition(string $position)
	{
		$this->position = $position;
	}

	public function getPosition()
	{
		return $this->position;
	}

	// Skin
	public function setSkin(string $skin)
	{
		$this->skin = $skin;
	}

	public function getSkin()
	{
		return $this->skin;
	}

	// LastSeen
	public function updateLastSeen()
	{
		global $bdd;
		$this->lastSeen = microtime(true);

		$req = $bdd->prepare('UPDATE user SET lastSeen=:lastSeen WHERE id=:id');
		$req->execute(array('id' => $this->getId(), 'lastSeen' => $this->lastSeen));
	}

	public function getLastSeen()
	{
		return $this->lastSeen;
	}

	// Id
	public function setVie(string $vie)
	{
		$this->vie = $vie;
	}

	public function getVie()
	{
		return $this->vie;
	}

	// VieMax
	public function setVieMax(string $vieMax)
	{
		$this->vieMax = $vieMax;
	}

	public function getVieMax()
	{
		return $this->vieMax;
	}

	// Bouclier
	public function setBouclier(string $bouclier)
	{
		$this->bouclier = $bouclier;
	}

	public function getBouclier()
	{
		return $this->bouclier;
	}

	// BouclierMax
	public function setBouclierMax(string $bouclierMax)
	{
		$this->bouclierMax = $bouclierMax;
	}

	public function getBouclierMax()
	{
		return $this->bouclierMax;
	}

	// Vitesse
	public function setVitesse(string $vitesse)
	{
		$this->vitesse = $vitesse;
	}

	public function getVitesse()
	{
		return $this->vitesse;
	}

	// PA
	public function setPA(string $PA)
	{
		$this->PA = $PA;
	}

	public function getPA()
	{
		return $this->PA;
	}

	// PAMax
	public function setPAMax(string $PAMax)
	{
		$this->PAMax = $PAMax;
	}

	public function getPAMax()
	{
		return $this->PAMax;
	}

	// PM
	public function setPM(string $PM)
	{
		$this->PM = $PM;
	}

	public function getPM()
	{
		return $this->PM;
	}

	// PMMax
	public function setPMMax(string $PMMax)
	{
		$this->PMMax = $PMMax;
	}

	public function getPMMax()
	{
		return $this->PMMax;
	}

	// Initiative
	public function setInitiative(string $initiative)
	{
		$this->initiative = $initiative;
	}

	public function getInitiative()
	{
		return $this->initiative;
	}
}