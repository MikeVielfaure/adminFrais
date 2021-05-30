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

<div class="alert alert-info" role="alert">
    <p>Fiche Frais Validé ! <a href="index.php?uc=validerFrais&action=choixVisiteur">Cliquez ici</a>
        pour revenir à la page de validation des fiches de frais.</p>
</div>
<?php
header("Refresh: 3;URL=index.php");

