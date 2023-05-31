<?php
session_start();
ob_start();

require_once "connexion.php";

$db = connexion();

// ------------------------ recupere une table  ------------------------
// fonction pour recuper une table SQL
function infoTable($table,$db){
// requête pour obtenir les information d'un gaulois en fonction de l'id qu'on recois   
$sqlTable = 'SELECT * FROM '.$table.' WHERE id_personnage = :id';
// effectue la requête 
$tablesStatement = $db->prepare($sqlTable);
// $personnagesStatement->bindValue("id",$_GET['id'])
// execute requête  SQL avec pour id du personnage récupérer avec GET
$tablesStatement->execute(["id" =>$_GET['id']]);
// return les information qui ont était chercher dans la table
return  $tablesStatement->fetchAll(); 
}

// function pour pouvoir rechercher un element dans une table sql
function infoSQL($sqlInfo,$db,$infoRechercher){

    // effectue la requête 
    $infosStatement = $db->prepare($sqlInfo);
    // execute la requête SQL 
    $infosStatement->execute();
    // va chercher les inforamations
    $infos = $infosStatement->fetchAll();
    // boucle
    foreach($infos as $info){
        return $info[$infoRechercher];
    }
}

?></th>
<?php
if (isset($_GET['action'])){
    switch($_GET['action']){
        case "showGaulois":
            ?>
            <table>
            
            <tr>
                <th>nom personnage</th>
                <th>lieu personnage</th>
                <th>specialite personnage</th>
            </tr>
            
            <tr>
                <?php 
                // varaible avec tous les elements du personnage 
                $personnages = infoTable('personnage',$db);
                
                foreach($personnages as $personnage){?>
                    <!-- affiche le nom du personnage-->
                    <td><?= $personnage['nom_personnage'] ?></td>

                    <td><?php
                    // ------- affiche le lieu du personnage -------
                    // requête pour obtenir les information du lieu de la personnage
                    $sqlLieux = ('SELECT * FROM lieu WHERE id_lieu = '.$personnage['id_lieu']);
                    echo infoSQL($sqlLieux,$db,'nom_lieu')
                    ?></td>
                    
                    <td><?php
                    // ------- affiche la specialite du personnage -------
                    // requête pour obtenir les information du specialite de la personnage
                    $sqlSpecialite = ('SELECT * FROM specialite WHERE id_specialite = '.$personnage['id_specialite']);
                    echo infoSQL($sqlSpecialite,$db,'nom_specialite');
                    
                    ?></td>
                <?php
                }?>
            </tr>
                
            <tr>
                <th>nom bataille</th>
                <th>lieu bataille</th>
                <th>date bataille</th>
                <th>quantité de casque pris</th>
            </tr>
            
            <tr>
            <?php 
            // varaible avec tous les elements de prendre casque 
            $pCasques = infoTable('prendre_casque',$db);
            foreach($pCasques as $pCasque){
                ?>
    
                <?php
                // affiche le nom du personnage
                $sqlBatailles = ('SELECT * FROM bataille WHERE id_bataille = :id');
                $bataillesStatement = $db->prepare($sqlBatailles);
                $bataillesStatement->execute(["id" => $pCasque['id_bataille']]);
                $batailles = $bataillesStatement->fetchAll();
                ?>

                <tr><?php 

                // boucle pour afficher les info sur les batailles
                foreach($batailles as $bataille){
                    ?><tr>
                        <!-- affiche nom de la bataille -->
                        <td><?php echo $bataille['nom_bataille']?></td>

                        <td><?php 
                        // ------- affiche lieux de bataille du personnage -------
                        // requête pour obtenir les information du lieu de la bataille
                        $sqlLieux = ('SELECT * FROM lieu WHERE id_lieu ='.$bataille['id_lieu']);
                        echo infoSQL($sqlLieux,$db,'nom_lieu');
                        ?></td>

                        <!-- affiche date de la bataille -->
                        <td><?php  echo $bataille['date_bataille']; ?></td>

                        <!-- affiche quantité de casque pris -->
                        <td><?= $pCasque['qte']; ?></td>
                    </tr><?php ;
                }
                
                ?></tr>
            <?php
            }?>
            </tr>

            </table>
            <?php
    }
}
?>

<?php
$contenu = ob_get_clean();
require "template.php";