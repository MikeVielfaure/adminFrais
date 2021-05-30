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
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$idVisiteur = $_SESSION['idVisiteur'];
switch ($action) {
case 'choixVisiteur':
    $listeVisiteurs = $pdo->getLesVisiteurs();
    $listeMois = listeMois();
    $lesClesVisiteurs = array_keys($listeVisiteurs);
    $clesVisiteur = $lesClesVisiteurs[0];
    $visiteurASelectionner = $clesVisiteur['id'];
    $lesClesMois = array_keys($listeMois);
    $moisASelectionner = $lesClesMois[0];
    include 'vues/v_choixVisiteur.php';
    break;
case 'afficheFicheFrais':
    $actualise = "";
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $leVisiteurId = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
    $listeMois = listeMois();
    $listeVisiteurs = $pdo->getLesVisiteurs();
    $visiteurASelectionner = $leVisiteurId;
    $moisASelectionner = $leMois;
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteurId, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteurId, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFraisEtat($leVisiteurId, $leMois, "CL");
    if($lesInfosFicheFrais == null){
        include 'vues/v_choixVisiteur.php';
        include 'vues/v_noFicheFrais.php';
    }else{
    include 'vues/v_choixVisiteur.php';
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
    $_SESSION['moisSession'] = $leMois;
    $_SESSION['idVisiteurSession'] = $leVisiteurId;
    $listePuissanceVehicule = $pdo->getListePuissanceVehicule();
    $laPuissanceVehicule = $pdo->getPuissanceVehicule($_SESSION['idVisiteurSession']);
    $idPuissanceVehicule = $laPuissanceVehicule['idPuissanceVehicule'];
    include 'vues/v_afficheFicheFrais.php';
    }
    break;

case 'actualiseFicheFrais':
    $actualise = "actualisée";
    $leMois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_STRING);
    $leVisiteurId = filter_input(INPUT_POST, 'idVisiteur', FILTER_SANITIZE_STRING);
    $idPuissance = filter_input(INPUT_POST, 'lstVehicule', FILTER_SANITIZE_STRING);
    $leMois = $_SESSION['moisSession'] ;
    $leVisiteurId = $_SESSION['idVisiteurSession'] ; 
    // récupère les quantité modifié
    $etape =(int)(filter_input(INPUT_POST, 'Etape', FILTER_SANITIZE_STRING));
    $km = (int)(filter_input(INPUT_POST, 'Kilométrique', FILTER_SANITIZE_STRING));
    $nuite = (int)(filter_input(INPUT_POST, 'Nuitee', FILTER_SANITIZE_STRING));
    $repas = (int)(filter_input(INPUT_POST, 'Repas', FILTER_SANITIZE_STRING));
    $listeMois = listeMois();
    $listeVisiteurs = $pdo->getLesVisiteurs();
    $visiteurASelectionner = $leVisiteurId;
    $moisASelectionner = $leMois;
    include 'vues/v_choixVisiteur.php';
    // update les frais forfaitisés
    $pdo->majFicheFrais($leVisiteurId, $leMois, $etape, "Forfait Etape");
    $pdo->majFicheFrais($leVisiteurId, $leMois, $km, "Frais Kilométrique");
    $pdo->majFicheFrais($leVisiteurId, $leMois, $nuite, "Nuitée Hôtel");
    $pdo->majFicheFrais($leVisiteurId, $leMois, $repas, "Repas Restaurant");
    $pdo->majIdPuissanceVehicule($leVisiteurId, $idPuissance);
    // récupère la fiche de frais 
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteurId, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteurId, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($leVisiteurId, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
    // récupère la liste des puissances et la puissance duvéhicule du visiteur
    $listePuissanceVehicule = $pdo->getListePuissanceVehicule();
    $laPuissanceVehicule = $pdo->getPuissanceVehicule($_SESSION['idVisiteurSession']);
    $idPuissanceVehicule = $laPuissanceVehicule['idPuissanceVehicule'];
    include 'vues/v_afficheFicheFrais.php';
    break;
case 'validerFicheFrais':
    $leMois = $_SESSION['moisSession'] ;
    $leVisiteurId = $_SESSION['idVisiteurSession'] ; 
    if(isset($_POST['submit'])){
        if(!empty($_POST['fraisHF'])) {
            foreach($_POST['fraisHF'] as $value){
                $pdo->majFicheFraisHF($leVisiteurId, $value, $leMois,"REFUSE ");
            }
        }
        if(!empty($_POST['fraisReport'])){
            foreach ($_POST['fraisReport'] as $val) {
                echo "value : ".$val.'<br/>';
                $ficheFrais = $pdo->getLesInfosFicheFrais($leVisiteurId, moisSuivant($leMois));
                if($ficheFrais == null){
                    $pdo->creeNouvellesLignesFrais($leVisiteurId, $leMois);
                }else {
                    
                }
                    $fraisHF = $pdo->getFraisHorsForfait($val);
                    $montant = $fraisHF['mnt'];
                    $libelle = $fraisHF['lbl'];
                    $date = $fraisHF['dte'];
                    $pdo->creeNouveauFraisHorsForfait($leVisiteurId, moisSuivant($leMois), $libelle, $date, $montant);
                    $pdo->supprimerFraisHorsForfait($val);
            }
        }
    }
    $puissanceVehicule = $pdo->getPuissanceVehicule($leVisiteurId);
    $leMontant = $puissanceVehicule['montant'];
    $pdo->majMontantFraisForfait("KM", $leMontant);
    $montantFraisForfait = $pdo->getMontantTotalFraisForfait($leVisiteurId, $leMois);
    $montantTotalFraisForfait = $montantFraisForfait['montantTotal'];
    $montantFraisHorsForfait = $pdo->getMontantTotalFraisHorsForfait($leVisiteurId, $leMois);
    $montantTotalFraisHorsForfait = $montantFraisHorsForfait['montantTotal'];
    $montantTotal = floatval($montantTotalFraisForfait) + floatval($montantTotalFraisHorsForfait);
    $pdo->majFiche($leVisiteurId, $leMois, "VA", $montantTotal);
include 'vues/v_ficheFraisValide.php';

    break;
default :
    $listeVisiteurs = $pdo->getLesVisiteurs();
    $listeMois = listeMois();
    $lesClesVisiteurs = array_keys($listeVisiteurs);
    $clesVisiteur = $lesClesVisiteurs[0];
    $visiteurASelectionner = $clesVisiteur['id'];
    $lesClesMois = array_keys($listeMois);
    $moisASelectionner = $lesClesMois[0];
    include 'vues/v_choixVisiteur.php'; 
}

