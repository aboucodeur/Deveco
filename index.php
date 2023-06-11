<?php 
    require_once('./src/db/db.php'); // contient les elements de la connexion a la base de donnee mieux le place en haut
    require_once('./src/layout/header.php');

    $categories = get_categories($connexion);
    $categories2 = get_categories($connexion);
    $transactions = get_transactions($connexion);
    $calcul_banques = get_calcule_banques($connexion);

    $succes = false;
    $errors = null;

    // ** FORMULAIRE
    function action_input ($value) { echo '<input type="hidden" name="action" value="'.$value.'" />';}
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        switch ($_POST['action']) {
            case 'd' :
                $mte = $_POST['mte'];
                $cat_id = $_POST['cat_id'];
                $co_id = 1;
                $effectuer_depots = depots($connexion,$co_id,$mte,$cat_id);
                if($effectuer_depots === true) {
                    $succes = true;
                    $_POST['mte'] = null;
                    $_POST['cat_id'] = null;
                    $co_id = null;
                    header('Location: index.php');
                }
                else $errors = $effectuer_depots;
                
            break;
            case 'r' :
                $mte = $_POST['mte'];
                $cat_id = $_POST['cat_id'];
                $co_id = 1;
                $effectuer_retraits = retraits($connexion,$co_id,$mte,$cat_id);
                if($effectuer_retraits === true) {
                    $succes = true;
                    $_POST['mte'] = null;
                    $_POST['cat_id'] = null;
                    $co_id = null;
                    header('Location: index.php');
                }
                else $errors = $effectuer_retraits;
            break;
            default :
            break;
        }
    }
    if($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['tra_id']) {
        $tra_id = $_GET['tra_id'];
        $effectuer_annulation = annuler_transactions($connexion,$tra_id);
        echo $effectuer_annulation;
        if($effectuer_annulation) {
            header('Location: index.php');
        }
    }

    // ** MESSAGES
    if($succes) {
        echo '<div class="alert alert-primary" role="alert">';
            echo 'Opertation effectuer avec success';
        echo '</div>';
    }
    if($errors) {
        echo '<div class="alert alert-danger" role="alert">';
            echo $errors;
        echo '</div>';
    }
        echo '<div class="row">';
            // ** zones de depots
            echo '<div class="col-md-6">';
                echo '<form method="POST" action="index.php">';
                    action_input('d');
                    echo '<div class="row">';
                        echo '<div class="col-md-6">';
                            echo '<div class="form-group">';
                                echo '<input required type="text" class="form-control" id="depotMontant" name="mte" placeholder="Montant du dépôt">';
                            echo '</div>';
                        echo '</div class="col-md-6">';
                        echo '<div>';
                            echo '<div class="input-group mb-3">';
                                echo '<div class="input-group-prepend">';
                                    echo '<label class="input-group-text" for="select-categories1">Categories</label>';
                                echo '</div>';
                                    $i=1;
                                    echo '<select required name="cat_id" class="custom-select" id="select-categories1">';
                                        while($data = pg_fetch_assoc($categories)) :
                                            echo '<option value="'.$data['cat_id'].'" '.($i++ == 2 ? 'selected' : '').' >'.$data['cat_nom'].'</option>';
                                        endwhile;
                                    echo '</select>';
                                echo '</div>';
                            echo '</div>';
                    echo '</div>';
                    echo '<button type="submit" class="btn btn-primary w-100">Dépôt</button>';
                echo '</form>';
            echo '</div>';
            // ** zones de retraits
            echo '<div class="col-md-6">';
                echo '<form method="POST" action="index.php">';
                    action_input('r');
                    echo '<div class="row">';
                        echo '<div class="col-md-6">';
                            echo '<div class="form-group">';
                                echo '<input required type="text" class="form-control" id="depotMontant" name="mte" placeholder="Montant du retrait">';
                            echo '</div>';
                        echo '</div>';
                        echo '<div>';
                            echo '<div class="input-group mb-3">';
                                echo '<div class="input-group-prepend">';
                                    echo '<label class="input-group-text" for="select-categories2">Categories</label>';
                                echo '</div>';
                                    $i=1;
                                    echo '<select required name="cat_id" class="custom-select" id="select-categories2">';
                                        while($data = pg_fetch_assoc($categories2)) :
                                            echo '<option value="'.$data['cat_id'].'" '.($i++ == 2 ? 'selected' : '').' >'.$data['cat_nom'].'</option>';
                                        endwhile;
                                    echo '</select>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '<button type="submit" class="btn btn-warning w-100">Retrait</button>';
                echo '</form>';
            echo '</div>'; 
        echo '</div>';
        echo '<hr>'; // DIVIDER
        // echo '<h4>Liste des operations</h4>';
        echo '<table class="table table-sm table-striped table-bordered table-responsive-lg">';
            echo '<thead>';
                echo '<tr>';
                    echo '<th scope="col">#</th>';
                    echo '<th scope="col">Date</th>';
                    echo '<th scope="col">Categorie</th>';
                    echo '<th scope="col">Type</th>';
                    echo '<th scope="col">Montant</th>';
                    echo '<th scope="col">Action</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                    $i=1;
                    while ($datas = pg_fetch_assoc($transactions)) :
                        echo '<tr>';
                            echo '<td>'.($i++).'</td>';
                            echo '<td scope="row">'.$datas['createdat'].'</td>';
                            echo '<td>'.$datas['cat_nom'].'</td>';
                            echo '<td>'.($datas['tra_type'] == 'd' ? 'Depots' : 'Retrait').'</td>';
                            echo '<td>'.number_format($datas['tra_mte'],0,' ',' ').'</td>';
                            echo '<td>';
                                echo '<a href="/index.php?tra_id='.$datas['tra_id'].'">';
                                    echo '<button class="btn btn-danger">Annuler</button>';
                                echo '</a>';
                            echo '</td>';
                        echo '</tr>';
                    endwhile;
            echo '</tbody>';
        echo '</table>';
    require_once('./src/layout/footer.php');
?>