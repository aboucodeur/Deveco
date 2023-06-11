<?php
    require_once('./src/db/db.php');
    require_once('./src/layout/header.php');
    echo '<h2>Calculateur de dépenses</h2>';
    // l''affchage
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo '<div class="container mt-4">';
        $budget = $_POST['budget'];
        $nbMois = $_POST['nbMois'];
        $conversion = $_POST['conversion']; // Prix du dollars en CFA
        $ajouterDomaine = $_POST['ajouterDomaine']; // Ajouter le nom de domaine au calcule
        $ajouterCommission = $_POST['ajouterCommission']; // Ajouter la comission au calcule
    
        $totalCFA = 0;
        $montantDomaineCFA = 0;
        $commission = 0;
        $prixServeurLinode = 10 * $nbMois;
        $montantServeurLinodeCFA = $prixServeurLinode * $conversion;
        $totalCFA += $montantServeurLinodeCFA;
    
        if ($ajouterDomaine === 'oui') {
            $prixDomaine = 14.58;
            $montantDomaineCFA = $prixDomaine * $conversion;
            $totalCFA += $montantDomaineCFA;
        }
    
        if ($ajouterCommission === 'oui') {
            $commission = 1170;
            $totalCFA += $commission;
        }
    
        // Calculating the remaining budget after expenses
        $budgetRestantCFA = $budget - $totalCFA;
    
        echo '<h3>Résultats :</h3>';
    
        echo '<table class="table">';
        echo '<tr><th>Paramètres</th><th>Montant en CFA</th></tr>';
        echo '<tr><td>MONTANT SERVEUR LINODE</td><td>' . $montantServeurLinodeCFA . '</td></tr>';
        echo '<tr><td>MONTANT DOMAINE</td><td>' . $montantDomaineCFA . '</td></tr>';
        echo '<tr><td>MONTANT COMMISSION UBA</td><td>' . $commission . '</td></tr>';
        echo '</table>';
    
        echo '<h4>Total des dépenses : ' . $totalCFA . ' CFA</h4>';
        echo '<h4>Budget après dépenses : ' . $budgetRestantCFA . ' CFA</h4>';
    
    }
    else {
        echo '<form method="POST" action="dev.php">';
            echo '<div class="form-group">';
                echo '<label for="budget">Quel est votre budget actuel en CFA ?</label>';
                echo '<input type="number" class="form-control" id="budget" name="budget" required>';
            echo '</div>';
            echo '<div class="form-group">';
                echo '<label for="nbMois">Combien de mois voulez-vous utiliser le serveur Linode ?</label>';
                echo '<input type="number" class="form-control" id="nbMois" name="nbMois" required>';
            echo '</div>';
            echo '<div class="form-group">';
                echo '<label for="conversion">Quel est le prix actuel du dollar en CFA ?</label>';
                echo '<input type="number" class="form-control" id="conversion" name="conversion" required>';
            echo '</div>';
            echo '<div class="form-group">';
                echo '<label for="ajouterDomaine">Voulez-vous ajouter le montant du domaine ?</label>';
                echo '<select class="form-control" id="ajouterDomaine" name="ajouterDomaine">';
                    echo '<option value="oui">Oui</option>';
                    echo '<option value="non">Non</option>';
                echo '</select>';
            echo '</div>';
            echo '<div class="form-group">';
                echo '<label for="ajouterCommission">Ajouter le montant de la commission banque (1 170) ?</label>';
                echo '<select class="form-control" id="ajouterCommission" name="ajouterCommission">';
                    echo '<option value="oui">Oui</option>';
                    echo '<option value="non">Non</option>';
                echo '</select>';
            echo '</div>';
            echo '<button type="submit" class="btn btn-primary">Calculer</button>';
        echo '</form>';
    }
    require_once('./src/layout/footer.php');
?>