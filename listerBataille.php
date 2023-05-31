<?php
session_start();
ob_start();
?>

<?php

require_once "connexion.php";


$db = connexion();

// recuper les colonne des bataille avec la date et le lieu
$sqlBataille = '
    SELECT nom_bataille,DATE_FORMAT(date_bataille, "%d/%m/%Y"),nom_lieu

    FROM bataille
    RIGHT JOIN lieu
    ON bataille.id_lieu = lieu.id_lieu

    ORDER  BY date_bataille DESC;';

$bataillesStatement = $db->prepare($sqlBataille);
$bataillesStatement->execute();
$batailles = $bataillesStatement->fetchAll();

?>

<table>
    <thead>
        <tr>
            <th>nom bataille </th>
            <th>date</th>
            <th>lieu</th>
        </tr>
    </thead>
    
    <?php
    foreach ($batailles as $bataille) {
    ?>
        <tbody>
            <tr>
            <td><?=$bataille['nom_bataille']?></td>
            <td><?=$bataille['DATE_FORMAT(date_bataille, "%d/%m/%Y")']?></td>
            <td><?=$bataille['nom_lieu']?></td>      
            </tr>
        </tbody>
        
    <?php
    }
    ?>    
</table>

<?php
$contenu = ob_get_clean();
require "template.php";