<?php
require_once('./src/db/db.php');
require_once('./src/layout/header.php');
$searchMonth = isset($_GET['month']) ? $_GET['month'] : null;
$searchYear = isset($_GET['year']) ? $_GET['year'] : null;
$sc = sum_par_categories($connexion, $searchMonth, $searchYear);

$labels = array();
$data = array();

while ($l = pg_fetch_assoc($sc)) {
    $labels[] = $l['cat_nom'];
    $data[] = $l['sum_cat'];
}

// Obtenir l'index de la valeur maximale dans le tableau des données
// l'index de la valeur puis verfier dans le code javascript
$maxValueIndex = array_search(max($data), $data);

$labels_json = json_encode($labels);
$data_json = json_encode($data);

$mois = array (
    '' => 'Mois',
    '1' => 'Janvier',
    '2' => 'Février',
    '3' => 'Mars',
    '4' => 'Avril',
    '5' => 'Mai',
    '6' => 'Juin',
    '7' => 'Juillet',
    '8' => 'Août',
    '9' => 'Septembre',
    '10' => 'Octobre',
    '11' => 'Novembre',
    '12' => 'Décembre'
);
?>

<div class="container">
    <h5>Graphiques des dépenses</h5>
    <form method="GET">
        <select name="month">
            <?php foreach($mois as $num => $moi) : ?>
                <option <?=($num == $searchMonth ? 'selected' : '')?> value="<?= $num ?>"><?= $moi ?></option>
            <?php endforeach;?>
        </select>
        <select name="year">
            <!-- Gérez les années ici -->
            <option value="">Année</option>
            <option value="2023">2023</option>
            <option value="2023">2024</option>
            <option value="2023">2025</option>
            <option value="2023">2026</option>
        </select>
        <button type="submit">Rechercher</button>
    </form>
    <canvas id="myChart" style="width: 100%;height:500px;"></canvas>
</div>

<script>
    const labels = <?php echo $labels_json; ?>;
    const data = <?php echo $data_json; ?>;
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Dépenses par catégories',
                data: data,
                backgroundColor: data.map((value, index) => index === <?php echo $maxValueIndex; ?> ? 'rgba(255, 99, 132, 0.6)' : 'rgba(75, 192, 192, 0.6)'),
            }]
        },
        options: {
            responsive: true,
        }
    });
</script>

<?php require_once('./src/layout/footer.php'); ?>
