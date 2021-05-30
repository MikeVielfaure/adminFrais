<?php

/**
 * Gestion des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Mike Vielfaure
 * @copyright 2021
 * @license   Réseau CERTA
 * @version   GIT: <0>
 */

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="UTF-8">
        <title>Intranet du Laboratoire Galaxy-Swiss Bourdin</title> 
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../styles/bootstrap/bootstrap.css" rel="stylesheet">
        <link href="../styles/style.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
<div class="row">
    <h2>Administration fiche frais</h2>
    <h3>Créer fiche frais pour tout les visteurs : </h3>
        <div>
        <form action="c_admin.php?action=creerFicheFrais" 
              method="post" role="form">
            <div class="form-group">
                <div class="form-group">
                <div class="col-md-12">
                <div class="col-md-4">
                <label for="lstMois" accesskey="n">Mois : </label>
                </div>
                </div>
                </div>
                <div class="form-group">
                <div class="col-md-12">
                <div class="col-md-4">
                <select id="lstMois" name="lstMois" class="form-control">
                    <?php
                    foreach ($listeMois as $unMois) {
                        $mois = $unMois;
                        if ($mois == $moisASelectionner) {
                            ?>
                            <option selected value="<?php echo $mois ?>">
                                <?php echo "/".$mois?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>">
                                <?php echo "/".$mois ?> </option>
                            <?php
                        }
                    }
                    ?>    
                </select>
                </div>
               <div class="col-md-1">     
            <input id="ok" type="submit" value="Créer" class="btn btn-success" 
                   role="button">
               </div>
                </div>
                </div>
            </div>
        </form>
</div>
</div>
            
<div class="row">
    <h3>Modifier état fiche frais pour un mois : </h3>
        <div>
        <form action="c_admin.php?action=modifierEtat" 
              method="post" role="form">
            <div class="form-group">
                <div class="col-md-12">
                <div class="form-group">
                <div class="col-md-2">
                <label for="lstMois" accesskey="n">Mois : </label>
                </div>
                <div class="col-md-2">
                <label for="lstMois" accesskey="n">Etat : </label>
                </div>
                </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                    <div class="col-md-2">
                        <select id="lstMois" name="lstMois" class="form-control">
                    <?php
                    foreach ($listeMois as $unMois) {
                        $mois = $unMois;
                        if ($mois == $moisASelectionner) {
                            ?>
                            <option selected value="<?php echo $mois ?>">
                                <?php echo "/".$mois?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>">
                                <?php echo "/".$mois ?> </option>
                            <?php
                        }
                    }
                    ?>    
                </select>
                </div>
                <div class="col-md-2">
                <select id="lstEtat" name="lstEtat" class="form-control">
                    <?php
                    foreach ($listeEtat as $unEtat) {
                        $etat = $unEtat;
                        if ($etat == $etatASelectionner) {
                            ?>
                            <option selected value="<?php echo $etat ?>">
                                <?php echo "/".$etat?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $etat ?>">
                                <?php echo "/".$etat ?> </option>
                            <?php
                        }
                    }
                    ?>    
                </select>
                </div>
                    <div class="col-md-1">
                    <input id="ok" type="submit" value="Modifier" class="btn btn-success" 
                           role="button">
                    </div>
                </div>
                </div>
            </div>
        </form>
    </div>
</div>
            
<div class="row">
    <h3>Liste des fiches pour un mois : </h3>
        <div>
        <form action="c_admin.php?action=afficherListe" 
              method="post" role="form">
            <div class="form-group">
                <div class="form-group">
                <div class="col-md-12">
                <div class="col-md-4">
                <label for="lstMois" accesskey="n">Mois : </label>
                </div>
                </div>
                </div>
                <div class="form-group">
                <div class="col-md-12">
                  <div class="col-md-4">  
                <select id="lstMois" name="lstMois" class="form-control">
                    <?php
                    foreach ($listeMois as $unMois) {
                        $mois = $unMois;
                        if ($mois == $moisASelectionner) {
                            ?>
                            <option selected value="<?php echo $mois ?>">
                                <?php echo "/".$mois?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>">
                                <?php echo "/".$mois ?> </option>
                            <?php
                        }
                    }
                    ?>    
                </select>
                
            </div>
                    <div class="col-md-1">
            <input id="ok" type="submit" value="Afficher" class="btn btn-success" 
                   role="button">
                    </div>
                </div>
                </div>
        </form>
    </div>
</div>
</div>
            
            
