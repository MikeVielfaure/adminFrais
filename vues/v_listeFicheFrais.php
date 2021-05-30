<?php
/**
 * Vue Accueil des Comptables
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Mike Vielfaure
 * @copyright 2021
 * @version   GIT: <0>
 */
?>
<h2>Fiches de frais en cours de validation et mises en paiement</h2>
<div class="row">
    <h3>Sélectionner une fiche de frais : </h3>
</div>
<div  style="margin-top: 10px" class="row">
<div  class="panel panel-primary">
    <div class="panel-heading">Liste des fiches : 
    </div>
    <div>
    <div class="col-md-4" style="margin-top: 5px; margin-left: 10px">
        <form action="index.php?uc=suivrePaiement&action=listeFicheFrais"  
                          method="post" role="form">
    <label>Entrer un nom : </label>
    <input name="nom"   />
    <input style="background-color: white;border-color: transparent;
                           color: #337ab7; padding: 0;"
                            type="submit" value="Valider" 
                           class="btn"  role="button"/>
        </form>
    </div>
       <div class="col-md-7" style="margin-top: 5px; margin-left: 10px">
        <form action="index.php?uc=suivrePaiement&action=listeFicheFrais"  
                          method="post" role="form">
            <div style="float:left; margin-top: 5px">
    <label>Tri par lettre  : </label>
            </div>
    <div class="col-md-4">
    <select  id="lstLettre" name="lstLettre" class="form-control">
                <?php
                    foreach ($listeLettre as $uneLettre) {
                        ?>
                        
                            
                            <option value="<?php echo $uneLettre ?>">
                                <?php echo $uneLettre  ?> </option>
                            <?php
                        
                    }
                    ?>    
                </select>
    </div>
            <div style="float:left; margin-top: 5px">
    <input style="background-color: white;border-color: transparent;
                           color: #337ab7; padding: 0;"
                            type="submit" value="Valider" 
                           class="btn"  role="button"/>
            </div>
        </form>
    </div>
    </div>
    
    
        <table   style="overflow : no-content;" class="table  table-responsive scrollTable">
        <thead> 
        <tr>
            <th width = "15%" >mois</th>
            <th width = "17%" >nom</th>
            <th width = "17%" >prenom</th> 
            <th width = "15%" >Justificatifs</th>
            <th width = "15%" >date modif</th>
            <th width = "15%" >montant</th>
            <th width = "6% " ></th>
        </tr>
        </thead>
        </table>
    
    <div style="min-height:20px;
max-height:200px;/*pour IE qui comprend rien, et qui ne reconnait pas min-height, mais qui comprend mal height*/
min-width:10px;
/*pour IE qui comprend rien*/
overflow-y:scroll;/*pour activer les scrollbarres*/">
    <table  class="table table-bordered table-responsive scrollTable">
        <tbody>
        <?php
        foreach ($fichesFrais as $uneFicheFrais) {
            $idVisiteur = $uneFicheFrais['id'];
            $mois = $uneFicheFrais['mois'];
            $libelle = $uneFicheFrais['libelle'];
            $montant = $uneFicheFrais['montantValide']; 
            $nom = $uneFicheFrais['nom'];
            $prenom = $uneFicheFrais['prenom'];
            $dateModif = $uneFicheFrais['dateModif'];
            $nbJustificatifs = $uneFicheFrais['nbJustificatifs'];
            ?>
            <tr>
                <td width = "15%" ><?php echo $mois ?></td>
                <td width = "17%"><?php echo $nom ?></td>
                <td width = "17%" ><?php echo $prenom ?></td>
                <td width = "15%" ><?php echo $nbJustificatifs ?></td>
                <td width = "15%"><?php echo $dateModif ?></td>
                <td width = "15%"><?php echo $montant ?></td>
                <td style="padding:0" width = "6% "><form action="index.php?uc=suivrePaiement&action=detailFicheFrais" 
                          method="post" role="form">
                    <input style="background-color: white;border-color: transparent;
                           font-size:25px; color: #337ab7; padding: 0; margin-left: 20px
                           " id="ok" type="submit" value=">" 
                           class="btn"  role="button"/>
                    <input type="hidden" name="idVisiteur" value="<?php echo $idVisiteur ?>" />
                    <input type="hidden" name="mois" value="<?php echo $mois ?>" />
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    </div>
</div>
</div>