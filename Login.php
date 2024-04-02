<?php

require 'InterfaceBDD.php';

$test1 = "testAccount";
$test2 = "testPassword";

try {
    $request = $dbh->prepare("SELECT * FROM Accounts WHERE Username= :usernameinput AND Password = :passwordinput;");
    $request->bindParam(':usernameinput', $test1, PDO::PARAM_STR);
    $request->bindParam(':passwordinput', $test2, PDO::PARAM_STR);
    $request->execute();
    if ($request){
        $result = $request->fetch();
        if ($result) {
            echo "Authentification réussie";
        }else {
            echo "Authentification échouée";
        }
    }else{
        die("Erreur lors de l'authentification.");
    }
} catch (Exception $e) {
    die("Erreur d'authentification : " . $e->getMessage());
}

$request->closeCursor();

$dbh = null;







