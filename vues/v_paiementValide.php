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
    <p>Paiement Validé ! <a href="index.php?uc=suivrePaiement&action=listeFicheFrais">Cliquez ici</a>
        pour revenir à la page des suivis des paiements.</p>
</div>
<?php
header("Refresh: 3;URL=index.php?uc=suivrePaiement&action=listeFicheFrais");



