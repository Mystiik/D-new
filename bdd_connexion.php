<?php
try {
	$bdd = new PDO('mysql:host=localhost; dbname=dnew', "root", "");
} catch (PDOException $e) {
	die("Erreur:" . $e->getMessage());
}
?>