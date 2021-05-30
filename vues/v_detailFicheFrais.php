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
<hr>
</div>
<div class="row">
        <h3>Détails des frais de <?php echo $leNom." ".$lePrenom ?> pour le mois <?php echo $leMois ?> </h3>
        <!--<a href="index.php?uc=telechargerPDF">Télécharger PDF
        </a>-->
</div>
<div class="row">
<div class="panel panel-info">
    <div class="panel-heading">Eléments forfaitisés</div>
    <table style="margin-bottom: 0;" class="table table-bordered table-responsive">
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $libelle = $unFraisForfait['libelle']; ?>
                <th> <?php echo $libelle ?></th>
                <?php
            }
            ?>
        </tr>
        <tr>
            <?php
               
                $frais1 = $lesFraisForfait[0];
                $frais2 = $lesFraisForfait[1];
                $frais3 = $lesFraisForfait[2];
                $frais4 = $lesFraisForfait[3];
                $idVisiteur = $frais1['idVisiteur'];
                $mois = $frais1['mois'];
                $qte1 = $frais1['montant'];
                $qte2 = $frais2['montant'];
                $qte3 = $frais3['montant'];
                $qte4 = $frais4['montant'];
                $qteTotale = 0;
                $qteTotale= $qteTotale + $qte1+$qte2+$qte3+$qte4;
                ?>
            <td class="qteForfait"> 
                <?php echo $qte1 ?>
            </td>
            <td class="qteForfait"> 
                <?php echo $qte2 ?>
            </td>
            <td class="qteForfait"> 
                <?php echo $qte3 ?>
            </td>
            <td class="qteForfait"> 
                <?php echo $qte4 ?>
            </td>
        </tr>
    </table>
</div>
    
<form action="index.php?uc=suivrePaiement&action=paiement" 
     method="post" role="form">
    <?php if ($lesFraisHorsForfait != null){
        ?>
<div class="panel panel-info">
    <div class="panel-heading">Descriptif des éléments hors forfait - 
        <?php echo $nbJustificatifs ?> justificatifs reçus</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>
            <th class='montant'>Montant</th> 
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
            $montant = $unFraisHorsForfait['montant']; 
            $iD = $unFraisHorsForfait['id'];
                    $qteTotale = $qteTotale + $montant;
                    ?>
            <tr>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>
    <div> <label>Montant Total = <?php echo $qteTotale ?> </label> </div>
    <?php 
        }else{
    ?>
<div style="margin-top: 10px" class="alert alert-info" role="alert">
    <p>pas de frais hors forfait pour ce visiteur à ce mois ! 
    </p>
</div>
        <?php }
        ?>
<div style="text-align:center;">
    <input style="margin-bottom: 20px; background-color: #337ab7;border-color: #337ab7;" id="ok" type="submit" name="submit" 
           value="Paiement" class="btn btn-success" role="button"/>
</div>
</form>
</div>

