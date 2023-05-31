
<?php

function connexion()
{
    try {
        // Souvent on identifie cet objet par la variable $conn ou $db
        $db = new PDO(
            'mysql:host=localhost;dbname=gaulois;charset=utf8',
            'root',
            ''
        );

        return $db;
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}
