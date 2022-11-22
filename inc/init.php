<?php

// ########## DECLARATION DE LA SESSION ##########
// C'est obligatoire de déclarer au tout début de notre site

    session_start();

// Fin de la déclaration de la session

    try {

        $type_bdd   = "mysql";
        $host       = "localhost";
        $dbname     = "compte";
        $username   = "root";
        $password   = "";
        $options   = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC           // Ici, je définis que le mode de récupération des données par défaut sera sous forme associative.
                    ];

        $bdd = new PDO("$type_bdd:host=$host;dbname=$dbname", $username, $password, $options);
    } catch (Exception $e) {
        die("ERREUR CONNEXION BDD : " . $e->getMessage());
    }



    
    // Appel des fonctions

    require_once "functions.php";



    // Déclaration des variables "globales"

    $errorMessage = "";
    $successMessage = "";

?>