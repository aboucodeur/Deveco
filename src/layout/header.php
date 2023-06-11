<?php 
echo '<!DOCTYPE html>';
echo '<html lang="fr">';
echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<title>Revenus</title>';
echo '</head>';

echo '<body>';
    echo '<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-primary mb-2">';
        echo '<a class="navbar-brand" href="/index.php">Barry Cash</a>';
        // ** Hamburger menu
        echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
            echo '<span class="navbar-toggler-icon"></span>';
        echo '</button>';
        // ** Gerer par le menu hamburger
        echo '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
            echo '<ul class="navbar-nav mr-auto">';
                echo '<li class="nav-item active">';
                    echo '<a class="nav-link" href="/depenes.php">Graphiques<span class="sr-only">(current)</span></a>';
                echo '</li>';
                echo '<li class="nav-item active">';
                    echo '<a class="nav-link" href="/dev.php">Developpeur<span class="sr-only">(current)</span></a>';
                echo '</li>';
            echo '</ul>';
        echo '</div>';
        $solde = get_compte($connexion,1);
        $calcul_banques = get_calcule_banques($connexion);
        // ** AFFICHAGE DU BOUTON AVEC MODALE
        echo '<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#soldeModal">';
            echo 'Afficher le solde';
        echo '</button>';
    echo '</nav>';
    echo '<div class="container-fluid">';
    // HEADER
    // Modale pour afficher le solde
        echo '<div class="modal fade" id="soldeModal" tabindex="-1" role="dialog" aria-labelledby="soldeModalLabel" aria-hidden="true">';
            // ** 1 MODAL > 2 MODAL DIALOG > 3 MODAL CONTENT > 4 (MODAL HEADER,MODAL BODY)
            echo '<div class="modal-dialog" role="document">';
                echo '<div class="modal-content">';
                    echo '<div class="modal-header">';
                            echo '<h5 class="modal-title" id="soldeModalLabel">Solde du compte</h5>';
                            echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                                echo '<span aria-hidden="true">&times;</span>';
                            echo '</button>';
                    echo '</div>';
                    echo '<div class="modal-body">';
                        echo '<h3>Le solde actuel est de : '.$solde.' F</h3>';
                        echo '<p>Le montant total des depots : '.number_format($calcul_banques['depots'],0,' ',' ').' F</p>';
                        echo '<p>Le montant total des retraits : '.number_format($calcul_banques['retraits'],0,' ',' ').' F</p>';
                    echo '</div>';
                    echo '<div class="modal-footer">';
                        echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>';
                    echo '</div>';
                echo '</div>';
        echo '</div>';
    echo '</div>';
?>
