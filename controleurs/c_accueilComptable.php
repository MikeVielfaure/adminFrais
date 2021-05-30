<?php
/**
 * Gestion de l'accueil des Comptables
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Mike Vielfaure
 * @copyright 2021
 * @version   GIT: <0>
 */

if ($estConnecte) {
    include 'vues/v_accueilComptable.php';
} else {
    include 'vues/v_connexion.php';
}

