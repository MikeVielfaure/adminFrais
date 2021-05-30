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

require_once '../includes/fct.inc.php';
require_once '../includes/class.pdogsb.inc.php';
session_start();
$pdo = PdoGsb::getPdoGsb();
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$listeMois = array('01','02','03','04','05','06','07','08','09','10','11','12');
$listeEtat = array('CL','CR','RB','VA');
$moisASelectionner = $listeMois[0];
$etatASelectionner = $listeEtat[0];
include '../vues/v_admin.php';
switch ($action) {
case 'creerFicheFrais':
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $listeVisiteur = $pdo->getListeVisiteur();
    foreach ($listeVisiteur as $unVisiteur){
        $idVisiteur = $unVisiteur['id'];
        $info = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $idEtat = $info['idEtat'];
        if($idEtat == null){
        $pdo->creeNouvellesLignesFraisAdmin($idVisiteur, $leMois);
        $date = "2021-".$leMois."-".$leMois;
        $pdo->creeNouveauFraisHorsForfait($idVisiteur,$leMois,"fournitures",$date,45);
        $pdo->creeNouveauFraisHorsForfait($idVisiteur,$leMois,"transport",$date,15);
        
        $puissanceVehicule = $pdo->getPuissanceVehicule($idVisiteur);
        $leMontant = $puissanceVehicule['montant'];
        $pdo->majMontantFraisForfait("KM", $leMontant);
        $montantFraisForfait = $pdo->getMontantTotalFraisForfait($idVisiteur, $leMois);
        $montantTotalFraisForfait = $montantFraisForfait['montantTotal'];
        $montantFraisHorsForfait = $pdo->getMontantTotalFraisHorsForfait($idVisiteur, $leMois);
        $montantTotalFraisHorsForfait = $montantFraisHorsForfait['montantTotal'];
        $montantTotal = floatval($montantTotalFraisForfait) + floatval($montantTotalFraisHorsForfait);
        $pdo->majFiche($idVisiteur, $leMois, "VA", $montantTotal);
        } else{
            
        }
    }
    break;
case 'afficherListe':
    $_SESSION['moisSession'] = "";
    $_SESSION['idVisiteurSession'] = "";
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $fichesFrais = $pdo->getFichesFraisAdmin($leMois);
    include '../vues/v_listeFicheFraisAdmin.php'; 
    break;
case 'detailFicheFraisAdmin':
    $leMois = $_SESSION['moisSession'] ; 
    $idVisiteur = $_SESSION['idVisiteurSession'];
    $leMois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_STRING);
    $idVisiteur = filter_input(INPUT_POST, 'idVisiteur', FILTER_SANITIZE_STRING);
    $_SESSION['moisSession'] = $leMois;
    $_SESSION['idVisiteurSession'] = $idVisiteur;
    $infoVisiteur = $pdo->getNomPrenomVisiteur($idVisiteur);
    $nom = $infoVisiteur['nom'];
    $prenom = $infoVisiteur['prenom'];
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $listePuissanceVehicule = $pdo->getListePuissanceVehicule();
    $laPuissanceVehicule = $pdo->getPuissanceVehicule($idVisiteur);
    $idPuissanceVehicule = $laPuissanceVehicule['idPuissanceVehicule'];
    include '../vues/v_detailsFicheFraisAdmin.php'; 
    break;
case 'saveFicheFraisAdmin':
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
    // update les frais forfaitisés
    $pdo->majFicheFrais($leVisiteurId, $leMois, $etape, "Forfait Etape");
    $pdo->majFicheFrais($leVisiteurId, $leMois, $km, "Frais Kilométrique");
    $pdo->majFicheFrais($leVisiteurId, $leMois, $nuite, "Nuitée Hôtel");
    $pdo->majFicheFrais($leVisiteurId, $leMois, $repas, "Repas Restaurant");
    $pdo->majIdPuissanceVehicule($leVisiteurId, $idPuissance);
    break;

case 'saveFraisHorsAdmin':
    $leMois = $_SESSION['moisSession'] ;
    $leVisiteurId = $_SESSION['idVisiteurSession'] ; 
    // récupère les quantité modifié
    $libelle =(filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING));
    $montant = (int)(filter_input(INPUT_POST, 'montant', FILTER_SANITIZE_STRING));
    $date = (filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
    // update les frais forfaitisés
    $pdo->creeNouveauFraisHorsForfait($leVisiteurId,$leMois,$libelle,$date,$montant);

    break;

case 'modifierEtat' :
    $etat = filter_input(INPUT_POST, 'lstEtat', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $pdo->majFicheMois($leMois, $etat);
    if($etat == "VA"){
        
    }
    break;
    
}
include '../vues/v_pied.php';