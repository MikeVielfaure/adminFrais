<?php
/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=mysql-saisiedevosfrais.alwaysdata.net';
    private static $bdd = 'dbname=saisiedevosfrais_applicompatablemvc';
    private static $user = '226360_bts';
    private static $mdp = 'saisiedevosfrais';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$user,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

  /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT utilisateur.id AS id, utilisateur.nom AS nom, '
            . 'utilisateur.prenom AS prenom '
            . 'FROM utilisateur '
            . 'WHERE utilisateur.login = :unLogin AND utilisateur.mdp = SHA2(:unMdp, 512) '
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }    
    
    /**
     * Retourne type d'utilisateur
     *
     * @param String $login Login du visiteur
     * @param String $mdp Mot de passe du visiteur
     *
     * @return comptable ou visiteur
     */
    public function estVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT utilisateur.id AS id, utilisateur.nom AS nom, '
            . 'utilisateur.prenom AS prenom '
            . 'FROM utilisateur INNER JOIN visiteur ON utilisateur.id = visiteur.id '
            . 'WHERE utilisateur.login = :unLogin AND utilisateur.mdp = sha2(:unMdp, 512)'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();

        if($requetePrepare->fetch()){
            $type = "visiteur";
        }else{
            $type = "comptable";
        }
        return $type;
       
    }
    
    

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais, '
            . 'lignefraisforfait.idVisiteur as idVisiteur, '
            . 'lignefraisforfait.mois as mois, '
            . 'fraisforfait.libelle as libelle,'
            . 'fraisforfait.montant * lignefraisforfait.quantite as montant, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraisforfait '
                . 'SET lignefraisforfait.quantite = :uneQte '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    ) {
        //$dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'INSERT INTO lignefraishorsforfait (idvisiteur, mois, libelle, date, montant)'
            . 'VALUES (:unIdVisiteur, :unMois, :unLibelle, :uneDateFr, '
            . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $date, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif,'
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idetat = :unEtat, datemodif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Retourne les visiteurs
     *
     * @return un tableau avec prénom et nom des visiteurs
     */
    public function getLesVisiteurs()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT utilisateur.id AS id, utilisateur.nom AS nom, utilisateur.prenom AS prenom FROM utilisateur INNER JOIN visiteur ON utilisateur.id = visiteur.id '
            .'ORDER BY utilisateur.nom ASC'
        );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $nom = $laLigne['nom'];
            $prenom = $laLigne['prenom'];
            $id = $laLigne['id'];
            $lesVisiteurs[] = array(
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom,
            );
        }
        return $lesVisiteurs;
    }
    
     /**
     * Modifie un frais
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param int $quantite       Nouvel quantité
     * @param String $libelle     nom du frai
     *
     * @return null
     */
    public function majFicheFrais($idVisiteur, $mois, $quantite, $libelle)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraisforfait '
            . 'SET quantite = :uneQuantite '
            . 'WHERE lignefraisforfait.idfraisforfait = (SELECT fraisforfait.id from fraisforfait where libelle =:unLibelle) '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'AND lignefraisforfait.idVisiteur = :unIdVisiteur '
        );
        $requetePrepare->bindParam(':uneQuantite', $quantite, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Modifie un libelle fraisHF (mention REFUSE devant le libelle)
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $id ID du frais HF
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    le libellé à ajouter
     *
     * @return null
     */
    public function majFicheFraisHF($idVisiteur, $id, $mois, $libelle)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET libelle = concat(:refus ,libelle) '
            . 'WHERE idvisiteur = :unIdVisiteur '
            . 'AND mois = :unMois '
            . 'AND id = :unId '
        );
        $requetePrepare->bindParam(':refus',$libelle , PDO::PARAM_STR);
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

/**
     * Modifie l'état et la date d'une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois 
     * @param String $etat etat de la fiche de frais
     * @param float $montant montant validé
     *
     * @return null
     */
    public function majFiche($idVisiteur, $mois, $etat, $montant)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET idetat = :unEtat, dateModif = now(), montantvalide = :unMontant '
            . 'WHERE idvisiteur = :unIdVisiteur '
            . 'AND mois = :unMois '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Modifie l'état et la date d'une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois 
     * @param String $etat etat de la fiche de frais
     *
     * @return null
     */
    public function majFicheEtat($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET idetat = :unEtat, dateModif = now() '
            . 'WHERE idvisiteur = :unIdVisiteur '
            . 'AND mois = :unMois '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Retourne un frais hors forfait
     *
     * @param String $id fraisHF
     *
     * @return un tableau avec des champs d'un frais hors forfait
     */
    public function getFraisHorsForfait($id)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT lignefraishorsforfait.montant as mnt, '
            . 'lignefraishorsforfait.libelle as lbl, '
            . 'lignefraishorsforfait.date as dte '
            . 'FROM lignefraishorsforfait '
            . 'WHERE id = :unId '
        );
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }
    
     /**
     * Retourne la liste des fiches de frais pour un état avec les informations
     *
     * @param String $etat l'état des fiches retournées
     *
     * @return un tableau avec des champs des jointure des tables fichefrais, 
     * visteur, utilisateur et etat.
     */
    public function getFichesFrais($etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif, '
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'fichefrais.mois as mois, '
            . 'fichefrais.idvisiteur as idVisiteur, '
            . 'utilisateur.nom as nom, '
            . 'utilisateur.prenom as prenom, '
            . 'etat.libelle as libelle '
            . 'FROM fichefrais '
            . 'INNER JOIN visiteur ON fichefrais.idvisiteur = visiteur.id '
            . 'INNER JOIN utilisateur ON visiteur.id = utilisateur.id '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idetat = :unEtat '
            . 'ORDER BY utilisateur.nom ASC '
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesFichesFrais = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $nom = $laLigne['nom'];
            $prenom = $laLigne['prenom'];
            $idVisiteur = $laLigne['idVisiteur'];
            $idEtat = $laLigne['idEtat'];
            $dateModif = $laLigne['dateModif'];
            $nbJustificatis = $laLigne['nbJustificatifs'];
            $montantValide = $laLigne['montantValide'];
            $mois = $laLigne['mois'];
            $libelle = $laLigne['libelle'];
            $lesFichesFrais[] = array(
                'id' => $idVisiteur,
                'nom' => $nom,
                'prenom' => $prenom,
                'idEtat' => $idEtat,
                'dateModif' => $dateModif,
                'nbJustificatifs' => $nbJustificatis,
                'montantValide' => $montantValide,
                'mois' => $mois,
                'libelle' => $libelle
            );
        }
        return $lesFichesFrais;
    }
    
         /**
     * Retourne la liste des fiches de frais pour un état et un nom
     *  avec les informations
     *
     * @param String $etat l'état des fiches retournées
     * @param String $nom nom du visiteur de la fiche a retourner
     *
     * @return un tableau avec des champs des jointure des tables fichefrais, 
     * visteur, utilisateur et etat.
     */
    public function getFichesFraisNom($etat, $nom)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif, '
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'fichefrais.mois as mois, '
            . 'fichefrais.idvisiteur as idVisiteur, '
            . 'utilisateur.nom as nom, '
            . 'utilisateur.prenom as prenom, '
            . 'etat.libelle as libelle '
            . 'FROM fichefrais '
            . 'INNER JOIN visiteur ON fichefrais.idvisiteur = visiteur.id '
            . 'INNER JOIN utilisateur ON visiteur.id = utilisateur.id '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idetat = :unEtat '
            . 'AND utilisateur.nom = :unNom '
            . 'ORDER BY utilisateur.nom ASC '
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unNom', $nom, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesFichesFrais = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $nom = $laLigne['nom'];
            $prenom = $laLigne['prenom'];
            $idVisiteur = $laLigne['idVisiteur'];
            $idEtat = $laLigne['idEtat'];
            $dateModif = $laLigne['dateModif'];
            $nbJustificatis = $laLigne['nbJustificatifs'];
            $montantValide = $laLigne['montantValide'];
            $mois = $laLigne['mois'];
            $libelle = $laLigne['libelle'];
            $lesFichesFrais[] = array(
                'id' => $idVisiteur,
                'nom' => $nom,
                'prenom' => $prenom,
                'idEtat' => $idEtat,
                'dateModif' => $dateModif,
                'nbJustificatifs' => $nbJustificatis,
                'montantValide' => $montantValide,
                'mois' => $mois,
                'libelle' => $libelle
            );
        }
        return $lesFichesFrais;
    }
    
          /**
     * Retourne la liste des fiches de frais pour un état et un nom
     *  avec les informations
     *
     * @param String $etat l'état des fiches retournées
     * @param String $lettre nom du visiteur de la fiche a retourner
     *
     * @return un tableau avec des champs des jointure des tables fichefrais, 
     * visteur, utilisateur et etat.
     */
    public function getFichesFraisLettre($etat, $lettre)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif, '
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'fichefrais.mois as mois, '
            . 'fichefrais.idvisiteur as idVisiteur, '
            . 'utilisateur.nom as nom, '
            . 'utilisateur.prenom as prenom, '
            . 'etat.libelle as libelle '
            . 'FROM fichefrais '
            . 'INNER JOIN visiteur ON fichefrais.idvisiteur = visiteur.id '
            . 'INNER JOIN utilisateur ON visiteur.id = utilisateur.id '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idetat = :unEtat '
            . 'AND utilisateur.nom LIKE :uneLettre '
            . 'ORDER BY utilisateur.nom ASC '
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneLettre', $lettre, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesFichesFrais = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $nom = $laLigne['nom'];
            $prenom = $laLigne['prenom'];
            $idVisiteur = $laLigne['idVisiteur'];
            $idEtat = $laLigne['idEtat'];
            $dateModif = $laLigne['dateModif'];
            $nbJustificatis = $laLigne['nbJustificatifs'];
            $montantValide = $laLigne['montantValide'];
            $mois = $laLigne['mois'];
            $libelle = $laLigne['libelle'];
            $lesFichesFrais[] = array(
                'id' => $idVisiteur,
                'nom' => $nom,
                'prenom' => $prenom,
                'idEtat' => $idEtat,
                'dateModif' => $dateModif,
                'nbJustificatifs' => $nbJustificatis,
                'montantValide' => $montantValide,
                'mois' => $mois,
                'libelle' => $libelle
            );
        }
        return $lesFichesFrais;
    }
    
     /**
     * Retourne un frais hors forfait
     *
     * @param String $idVisiteur ID Visiteur
     * 
     * @return un tableau nom et prenom du visiteur
     */
    public function getNomPrenomVisiteur($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT utilisateur.nom as nom, '
            . 'utilisateur.prenom as prenom '
            . 'FROM utilisateur '
            . 'WHERE id = :unIdVisiteur '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }
    
    /**
     * Retourne le montant total des frais forfaitisés pour un visiteur 
     * et un mois
     *
     * @param String $idVisiteur ID Visiteur
     * @param String $mois mois
     * 
     * @return un tableau avec le montant total
     */
    public function getMontantTotalFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT SUM(lignefraisforfait.quantite*fraisforfait.montant) as montantTotal '
            . 'FROM lignefraisforfait INNER JOIN fraisforfait '
            . 'ON lignefraisforfait.idfraisforfait = fraisforfait.id '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND mois = :unMois '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }
    
    /**
     * Retourne le montant total des frais hors forfaitisés pour un visiteur 
     * et un mois sans compter les refusés
     *
     * @param String $idVisiteur ID Visiteur
     * @param String $mois mois
     * 
     * @return un tableau avec le montant total
     */
    public function getMontantTotalFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT SUM(lignefraishorsforfait.montant) as montantTotal  '
            . 'FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois '
            . 'AND not lignefraishorsforfait.libelle like "REFUSE%"'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }
    
    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois et un état donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFraisEtat($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif,'
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois '
            . 'AND fichefrais.idetat = :unEtat '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }
    
     /**
     * Retourne la liste des types de véhicules
     *
     * @return un tableau avec des champs de puissancevehicule
     */
    public function getListePuissanceVehicule()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT puissancevehicule.libelle as puissanceVehicule, '
            . 'puissancevehicule.idpuissancevehicule as idPuissanceVehicule '
            . 'FROM puissancevehicule '
        );
        $requetePrepare->execute();
        $lesPuissances = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $puissanceVehicule = $laLigne['puissanceVehicule'];
            $idPuissance = $laLigne['idPuissanceVehicule'];
            $lesPuissances[] = array(
                'puissanceVehicule' => $puissanceVehicule,
                'idPuissanceVehicule' => $idPuissance
            );
        }
        return $lesPuissances;
    }
    
     /**
     * Retourne le puissance du vehicule enregistré
     *
     * @param String $idVisiteur ID Visiteur
     * 
     * @return un tableau avec la puissance véhicule
     */
    public function getPuissanceVehicule($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT puissancevehicule.libelle as puissanceVehicule, '
            . 'puissancevehicule.idpuissancevehicule as idPuissanceVehicule, '
            . 'puissancevehicule.montant as montant '
            . 'FROM puissancevehicule INNER JOIN visiteur '
            . 'ON puissancevehicule.idpuissancevehicule = visiteur.idpuissancevehicule '
            . 'WHERE visiteur.id = :unIdVisiteur '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }
    
    /**
     * Modifie la puissance véhicule d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $idPuissanceVehicule     ID puissance vehicule
     *
     * @return null
     */
    public function majIdPuissanceVehicule($idVisiteur, $idPuissanceVehicule)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE visiteur '
            . 'SET idpuissancevehicule = :unIdPuissanceVehicule '
            . 'WHERE id = :unIdVisiteur '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdPuissanceVehicule', $idPuissanceVehicule, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
     /**
     * Modifie le montant du frais kilométrique
     *
     * @param String $id ID frais forfait
     * @param String $montant le montant
     *
     * @return null
     */
    public function majMontantFraisForfait($id, $montant)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fraisforfait '
            . 'SET montant = :unMontant '
            . 'WHERE id = :unId '
        );
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Retourne la liste des Visiteurs (partie admin)
     *
     * @return un tableau avec des champs de visiteur
     */
    public function getListeVisiteur()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT visiteur.id as id '
            . 'FROM visiteur '
        );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $id = $laLigne['id'];
            $lesVisiteurs[] = array(
                'id' => $id
            );
        }
        return $lesVisiteurs;
    }
    
     /**
     * Retourne la liste des fiches de frais par rapport à un mois pour l'administrateur
     *
     * @param String $mois l'état des fiches retournées
     *
     * @return un tableau avec des champs des jointure des tables fichefrais, 
     * visteur, utilisateur et etat.
     */
    public function getFichesFraisAdmin($mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif, '
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'fichefrais.mois as mois, '
            . 'fichefrais.idvisiteur as idVisiteur, '
            . 'utilisateur.nom as nom, '
            . 'utilisateur.prenom as prenom, '
            . 'etat.libelle as libelle '
            . 'FROM fichefrais '
            . 'INNER JOIN visiteur ON fichefrais.idvisiteur = visiteur.id '
            . 'INNER JOIN utilisateur ON visiteur.id = utilisateur.id '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.mois = :unMois '
            . 'ORDER BY utilisateur.nom ASC '
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesFichesFrais = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $nom = $laLigne['nom'];
            $prenom = $laLigne['prenom'];
            $idVisiteur = $laLigne['idVisiteur'];
            $idEtat = $laLigne['idEtat'];
            $dateModif = $laLigne['dateModif'];
            $nbJustificatis = $laLigne['nbJustificatifs'];
            $montantValide = $laLigne['montantValide'];
            $mois = $laLigne['mois'];
            $libelle = $laLigne['libelle'];
            $lesFichesFrais[] = array(
                'id' => $idVisiteur,
                'nom' => $nom,
                'prenom' => $prenom,
                'idEtat' => $idEtat,
                'dateModif' => $dateModif,
                'nbJustificatifs' => $nbJustificatis,
                'montantValide' => $montantValide,
                'mois' => $mois,
                'libelle' => $libelle
            );
        }
        return $lesFichesFrais;
    }
    
    /**
     * Modifie l'etat de toutes les fiches de frais d'un mois (admin)
     *
     * @param String $mois       Mois 
     * @param String $etat etat de la fiche de frais
     *
     * @return null
     */
    public function majFicheMois($mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET idetat = :unEtat '
            . 'WHERE mois = :unMois '
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités non null
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFraisAdmin($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $quantiteAleatoire = rand(0,25);
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, :uneQuantite )'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':uneQuantite', $quantiteAleatoire, PDO::PARAM_INT);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

}



