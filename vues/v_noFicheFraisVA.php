 
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
    <p>pas de fiche de frais en cours de validation  ! <a href="index.php">Cliquez ici</a>
        pour revenir à la page d'accueil.</p>
</div>
<?php
header("Refresh: 3;URL=index.php");