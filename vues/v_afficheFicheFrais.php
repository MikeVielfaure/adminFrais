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

<hr>
<div class="panel panel-primary">
    <div class="panel-heading">Fiche de frais du mois 
        <?php echo $numMois . '-' . $numAnnee ?> : </div>
    <div class="panel-body">
        <strong><u>Etat :</u></strong> <?php echo $libEtat ?>
        depuis le <?php echo $dateModif ?> <br> 
        <strong><u>Montant validé :</u></strong> <?php echo $montantValide ?>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Eléments forfaitisés  <?php echo $actualise ?></div>
    <form action="index.php?uc=validerFrais&action=actualiseFicheFrais" 
              method="post" role="form">
    <table style="margin-bottom: 0;" class="table table-bordered table-responsive">
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $libelle = $unFraisForfait['libelle']; ?>
                <th> <?php echo $libelle ?></th>
                <?php
            }
            ?>
                <th>Type de voiture : </th>
        </tr>
        <tr>
            <?php
               
                $frais1 = $lesFraisForfait[0];
                $frais2 = $lesFraisForfait[1];
                $frais3 = $lesFraisForfait[2];
                $frais4 = $lesFraisForfait[3];
                $idVisiteur = $frais1['idVisiteur'];
                $mois = $frais1['mois'];
                $qte1 = $frais1['quantite'];
                $qte2 = $frais2['quantite'];
                $qte3 = $frais3['quantite'];
                $qte4 = $frais4['quantite'];
                ?>
            <td class="qteForfait"> 
                <input type="text" name="Etape"  value= "<?php echo $qte1 ?>" /> 
            </td>
            <td class="qteForfait"> 
                <input type="text" name="Kilométrique"  value= "<?php echo $qte2 ?>" /> 
            </td>
            <td class="qteForfait"> 
                <input type="text" name="Nuitee"  value= "<?php echo $qte3 ?>" /> 
            </td>
            <td class="qteForfait"> 
                <input type="text" name="Repas"  value= "<?php echo $qte4 ?>" /> 
            </td>
            <td class="qteForfait">
                <select id="lstVehicule" name="lstVehicule" class="form-control">
                <?php
                    foreach ($listePuissanceVehicule as $unePuissance) {
                        $puissanceVehicule = $unePuissance['puissanceVehicule'];
                        $idP = $unePuissance['idPuissanceVehicule'];
                        if ($idP == $idPuissanceVehicule) {
                            ?>
                            <option selected value="<?php echo $idP ?>">
                                <?php echo $puissanceVehicule ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $idP ?>">
                                <?php echo $puissanceVehicule  ?> </option>
                            <?php
                        }
                    }
                    ?>    
                </select>
            </td>
        </tr>
        <input type="hidden" name="idVisiteur" value="<?php echo $idVisiteur ?>" />
        <input type="hidden" name="mois" value="<?php echo $mois ?>" />
    </table>
        <div style="text-align:center;">
        <input style="background-color: #337ab7;border-color: #337ab7; margin-top: 
               10px;margin-bottom: 10px" id="ok" 
               type="submit" value="Sauvegarder" class="btn btn-success" role="button"/>
        </div>
    </form>
</div>
<form action="index.php?uc=validerFicheFrais&action=validerFicheFrais" 
     method="post" role="form">
        <?php if($lesFraisHorsForfait != null){
        ?>
<div class="panel panel-info">
    <div class="panel-heading">Descriptif des éléments hors forfait - 
        <?php echo $nbJustificatifs ?> justificatifs reçus</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>
            <th class='montant'>Montant</th> 
            <th class="libelle" style='width: 10%'>Refuser</th>
            <th class="libelle" style='width: 10%'>Report</th>
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
            $montant = $unFraisHorsForfait['montant']; 
            $iD = $unFraisHorsForfait['id']?>
            <tr>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
                <td>
                    <input type="checkbox"  name="fraisHF[]" value="<?php echo $iD ?> ">
                </td>
                <td>
                    <input type="checkbox"  name="fraisReport[]" value="<?php echo $iD ?> ">
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>
    <?php }else{
        ?>
<div style="margin-top: 10px" class="alert alert-info" role="alert">
    <p>pas de frais hors forfait pour ce visiteur à ce mois ! 
    </p>
</div>
    <?php }
    ?>
<div style="text-align:center;">
    <input style="margin-bottom: 20px" id="ok" type="submit" name="submit" 
           value="Valider" class="btn btn-success" role="button"/>
</div>
</form>

