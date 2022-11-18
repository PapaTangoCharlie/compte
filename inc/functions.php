<?php

    /*
        Fonction DEV qui permet un affichage clair des données (array, string, number, etc.).
    */

    function debug($value){
        echo "<pre>";
            print_r($value);
        echo "</pre>";
    }

    

?>