<?php
session_start();
ob_start();
?>

<?php
require_once "connexion.php";


$db = connexion();
// ------------------------ recupere la table personnage ------------------------
// Prendre tout ce qu'il y a dans la table 
$personnagesStatement = $db->prepare('SELECT * FROM personnage');
// récupérer les données
$personnagesStatement->execute();
$personnages = $personnagesStatement->fetchAll();

?>
<table>
    <thead>
        <tr>
            <th>nom personnage </th>
            <th>specialite personnage</th>
            <th>nom lieu</th>
        </tr>
    </thead>
    
    <?php
    foreach ($personnages as $personnage) {
    ?>
        <tbody>
            <tr>
            <!-- envoie une requete a afficherGaulois.php avec son action et id pour afficher les info du personnage  -->
            <td> <?php 
                $id_perso = $personnage['id_personnage'];
                echo "<a href='afficherGaulois.php?action=showGaulois&id=$id_perso'>" ?>
                <!-- afficher le nom du personnage-->
                <?php echo $personnage['nom_personnage']?></a>
            </td>

            <td ><?php 
            // ------------------------ recupere la table specialite ------------------------
            $specialitesStatement = $db->prepare('SELECT * FROM specialite WHERE id_specialite = :id');
            $specialitesStatement->execute(["id" => $personnage['id_specialite']]);
            $specialites = $specialitesStatement->fetchAll();
            // boucle pour afficher le nom de la specialite
            foreach($specialites as $specialite){
                echo $specialite['nom_specialite'];
            }?></td>

            <td ><?php 
            // ------------------------ recupere la table lieu ------------------------
            $lieusStatement = $db->prepare('SELECT * FROM  lieu WHERE id_lieu = :id');
            $lieusStatement->execute(["id" => $personnage['id_lieu']]);
            $lieus = $lieusStatement->fetchAll();
            // boucle pour afficher le nom du lieu
            foreach($lieus as $lieu){
                echo $lieu['nom_lieu'];
            }
            ?>
            </td>
            </tr>
        </tbody>
        
    <?php
    }
    ?>    

</table>

<?php
$contenu = ob_get_clean();
require "template.php";