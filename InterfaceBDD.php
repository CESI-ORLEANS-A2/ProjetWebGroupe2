<?php

define("BDD_HOST", "localhost");
define("BDD_NAME", "ProjetWeb");
define("BDD_USER", "Administrateur");
define("BDD_PASS", "AdminPassword");


try {
    $dbh = new PDO('mysql:host='. BDD_HOST . ';dbname=' . BDD_NAME, BDD_USER, BDD_PASS, 
    array(PDO::ATTR_PERSISTENT => true));
} catch (PDOException $e) {
    exit("Impossible de se connecter au serveur SQL : " . $e->getMessage());
}







