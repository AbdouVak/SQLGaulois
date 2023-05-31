<?php
session_start();
ob_start();
?>

<?php

require_once "connexion.php";

$db = connexion();

// recupere les colonne des nom de specialites avec nb de perso par specialite
$sqlNBgaulois = '
    SELECT nom_specialite,COUNT(personnage.id_specialite) AS persoSpecialite

    FROM specialite
    RIGHT JOIN personnage
    ON specialite.id_specialite = personnage.id_specialite

    GROUP BY personnage.id_specialite
    ORDER BY persoSpecialite DESC;';

$nbGauloisStatement = $db->prepare($sqlNBgaulois);
$nbGauloisStatement->execute();
$nbGaulois = $nbGauloisStatement->fetchAll();

?>

<table>
    <thead>
        <tr>
            <th>nom specialte </th>
            <th>nombre gaulois</th>
        </tr>
    </thead>
    
    <?php
    foreach ($nbGaulois as $nbGauloi) {
    ?>
        <tbody>
            <tr>
            <td><?= $nbGauloi['nom_specialite']?></td>
            <td><?= $nbGauloi['persoSpecialite']?></td>
            </tr>
        </tbody>
        
    <?php
    }
    ?>    
</table>

<?php
$contenu = ob_get_clean();
require "template.php";