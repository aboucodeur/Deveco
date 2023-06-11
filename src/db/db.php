<?php
    $connexion = pg_connect("host=localhost user=abou password=abou@89 dbname=dbdepenses");
    // ** REQUETES SELECT
    function get_compte($connexion,$co_id=1) {
        $query = "SELECT solde from comptes WHERE co_id=$co_id";
        $result = pg_query($connexion,$query);
        $row = pg_fetch_assoc($result);
        $solde = $row['solde'];
        return number_format($solde,0,' ',' ');
    }
    function get_categories ($connexion) {
        $requete = "SELECT * from categories order by cat_nom";
        $resultat = pg_query($connexion,$requete);
        return $resultat;
    };
    // TODO : [*] Appliquer la recherche par date 
    function get_transactions ($connexion) {
        $query = "SELECT tra_id,co_id,cat_id,t.createdat,tra_type,cat_nom,tra_mte
                    FROM transactions t 
                    JOIN comptes c using(co_id) 
                    LEFT JOIN categories using(cat_id) 
                    ORDER BY tra_type desc,tra_mte desc";
        $result = pg_query($connexion,$query);
        return $result;
    }
    // TODO : [*] : Afficher les sommes depots et retraits
    function get_calcule_banques($connexion,$d1 = null,$d2 =null) {
        $query= "SELECT ";
        $query.="COALESCE(SUM(CASE WHEN t.tra_type = 'd' THEN t.tra_mte ELSE 0 END)::int, 0) AS depots,";
        $query.="COALESCE(SUM(CASE WHEN t.tra_type in ('r','e') THEN t.tra_mte ELSE 0 END)::int, 0) AS retraits ";
        $query.="FROM comptes ";
        $query.="LEFT JOIN transactions t using(co_id) WHERE co_id = 1";
        $result = pg_query($connexion,$query);
        $row = pg_fetch_assoc($result);
        return $row;
    }
    // TODO : Sommes part catgories pour savoir la ou je depense le plus
    /**
     * Ici deux langages de programmation doit se marier javascript et php
     * Ma solution
     *  Utiliser comme du jsx
     *  Utiliser les data-set que nous allons recuperer dans fichier javascript 
     * Mais technique pour coupler les deux langages
     */
    function sum_par_categories ($connexion,$month = null, $year = null) {
        $query = "SELECT 
        UPPER(cat_nom) as cat_nom,
        SUM(tra_mte) sum_cat
        FROM transactions t
        LEFT JOIN categories USING(cat_id)
        WHERE tra_type = 'r'";
        if ($month && $year) {
        // Utiliser EXTRACT pour extraire le mois et l'année de la date de la transaction
            $query .= " AND EXTRACT(MONTH FROM t.createdat) = " . pg_escape_string($month);
            $query .= " AND EXTRACT(YEAR FROM t.createdat) = " . pg_escape_string($year);
        }
        $query .= " GROUP BY cat_nom
        ORDER BY sum_cat DESC";
        $result = pg_query($connexion, $query);
        return $result;
    }

    // ** LOGIQUE METIER DE L'APPLICATION
    function rollback($connexion,$msg='') {
        pg_query($connexion,'ROLLBACK');
        return $msg;
        // return false;
    }
    function depots($connexion,$co_id,$mte,$cat_id) {
        pg_query($connexion,"BEGIN"); // ** DEBUT DE LA TRANSACTION
        $query = "INSERT into transactions (tra_type,tra_mte,co_id,cat_id) values ('d',$mte,$co_id,$cat_id)";
        $result = pg_query($connexion,$query);
        if (!$result) return rollback($connexion,"Échec du dépôt. Veuillez réessayer.");
        $query = "update comptes set solde = solde + $mte where co_id=$co_id";
        $result = pg_query($connexion,$query);
        if (!$result) return rollback($connexion,"Échec de la mise à jour du solde. Veuillez réessayer.");
        pg_query($connexion, "COMMIT");
        pg_close($connexion);
        return true;
    }
    function retraits ($connexion,$co_id,$mte,$cat_id) {
        pg_query($connexion, "BEGIN"); // ** DEBUT DE LA TRANSACTION
        $query = "INSERT INTO transactions (tra_mte,co_id,cat_id,tra_type) VALUES ($mte,$co_id,$cat_id,'r')";
        $result = pg_query($connexion, $query);
        if (!$result) return rollback($connexion,"Échec du transaction de retrait. Veuillez réessayer.");

        $query = "SELECT solde FROM comptes WHERE co_id=$co_id";
        $result = pg_query($connexion, $query);
        if (!$result) return rollback($connexion,"Erreur lors de la récupération du solde. Veuillez réessayer.");

        $row = pg_fetch_assoc($result);
        $solde = $row['solde'];
        if ($mte > $solde) return rollback($connexion,"Solde insuffisant. Le retrait ne peut pas être effectué.");

        $query = "UPDATE comptes SET solde = solde - $mte WHERE co_id = $co_id";
        $result = pg_query($connexion, $query);
        if (!$result) return rollback($connexion,"Échec de la mise à jour du solde. Veuillez réessayer.");

        pg_query($connexion, "COMMIT");
        pg_close($connexion);
        return true;
    };
    function annuler_transactions($connexion,$tra_id) {
        pg_query($connexion,"BEGIN");
        // ** Chercher la transaction
        $query = "SELECT tra_mte,tra_type,co_id FROM transactions WHERE tra_id=$tra_id";
        $result = pg_query($connexion,$query);
        if(!$result) return rollback($connexion,"Echec lors de la recuperation du transaction");
        $row = pg_fetch_assoc($result);
        $co_id = $row['co_id'];
        $tra_mte = $row['tra_mte'];
        $tra_type = $row['tra_type'];
        // ** SUPPRIMER LA TRANSACTION => ICI NOUS UTILISATION LES TRANS POSTGRESQL POUR REVENIR EN ARRIERE
        $query = "DELETE FROM transactions WHERE tra_id=$tra_id";
        $result = pg_query($connexion,$query);
        if(!$result) return rollback($connexion,'Erreur lors de la suppression de la transaction');
        // ** L"OPERATION INVERSE DE LA TRANSACTION
        if($tra_type == 'd') { // inverse (sortie)
            $query = "SELECT solde FROM comptes WHERE co_id=$co_id";
            $result = pg_query($connexion,$query);
            $row = pg_fetch_assoc($result);
            $solde = $row['solde'];
            if($solde >= $tra_mte) { // Operations inverse du depots (sortie)
                $query = "UPDATE comptes set solde = solde - $tra_mte WHERE co_id=$co_id";
                $result = pg_query($connexion,$query); 
            }
        }
        else if ($tra_type == 'r') { // inverse (entree)
            $query = "UPDATE comptes set solde = solde + $tra_mte WHERE co_id = $co_id";
            $result = pg_query($connexion,$query);
            if(!$result) return rollback($connexion,"Echec de la mise a jour du solde");
        }
        pg_query($connexion,"COMMIT");
        pg_close($connexion);
        return true;
    };
?>