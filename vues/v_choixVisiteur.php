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
<div class="row">
<h2>Frais à valider</h2>
        <h3>Choix du visiteur : </h3>
    <div class="col-md-4">
        <form action="index.php?uc=validerFrais&action=afficheFicheFrais" 
              method="post" role="form">
            <div class="form-group">
                <label for="lstMois" accesskey="n">Mois : </label>
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
                <label for="lstVisiteur" accesskey="n">Visiteur : </label>
                <select id="lstVisiteur" name="lstVisiteur" class="form-control">
                <?php
                    foreach ($listeVisiteurs as $unVisiteur) {
                        $id = $unVisiteur['id'];
                        $nom = $unVisiteur['nom'];
                        $prenom = $unVisiteur['prenom'];
                        $unNom = $nom.$prenom;
                        if ($id == $visiteurASelectionner) {
                            ?>
                            <option selected value="<?php echo $id  ?>">
                                <?php echo $nom." ".$prenom ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $id ?>">
                                <?php echo $nom." ".$prenom  ?> </option>
                            <?php
                        }
                    }
                    ?>    
                </select>
            </div>
            <input id="ok" type="submit" value="Valider" class="btn btn-success" 
                   role="button">
        </form>
    </div>
</div>