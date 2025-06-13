<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport - Statistiques des commandes</title>
    <!-- Inclure Chart.js depuis CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .year-selector {
            text-align: center;
            margin-bottom: 20px;
        }
        .year-selector a {
            margin: 0 10px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .year-selector a:hover {
            color: #555;
        }
        .monthly-products {
            margin-top: 30px;
        }
        .monthly-products h2 {
            margin-bottom: 10px;
        }
        .monthly-products ul {
            padding-left: 20px;
        }
        .monthly-products ul li {
            margin-bottom: 5px;
        }
        .chart-container {
            margin-top: 30px;
        }
        .product-details {
            margin-top: 30px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Statistiques des commandes</h1>

        <?php
        // Connexion à la base de données
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "gestion_stock"; // Remplacez par le nom de votre base de données

        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Récupérer l'année actuelle par défaut
            $currentYear = isset($_GET['annee']) ? intval($_GET['annee']) : date('Y');

            // Limiter l'année entre 2000 et 2050
            $currentYear = max(2000, min(2050, $currentYear));

            // Requête SQL pour récupérer les commandes par mois pour l'année spécifiée
            $sql_commandes_par_mois = "
                SELECT MONTH(date_commande) AS mois, COALESCE(COUNT(*), 0) AS nombre_commandes
                FROM commande
                WHERE YEAR(date_commande) = :year
                GROUP BY mois
                ORDER BY mois";

            $stmt = $pdo->prepare($sql_commandes_par_mois);
            $stmt->bindParam(':year', $currentYear, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Initialiser les tableaux pour les mois et les commandes
            $months = [];
            $commandCounts = [];

            // Tableau associatif pour mapper les numéros de mois aux noms en français
            $frMonthNames = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];

            // Créer un tableau pour tous les mois de l'année avec 0 commandes
            for ($i = 1; $i <= 12; $i++) {
                $months[] = $frMonthNames[$i];
                $commandCounts[] = 0;
            }

            // Remplir le tableau avec les résultats de la requête SQL
            foreach ($results as $row) {
                $mois = intval($row['mois']);
                $commandCounts[$mois - 1] = intval($row['nombre_commandes']);
            }

            // Requête SQL pour récupérer le produit le plus vendu de l'année
            $sql_produit_plus_vendu = "
                SELECT article.nom_article AS nom_article, SUM(commande_article.quantite) AS quantite_totale
                FROM commande
                INNER JOIN commande_article ON commande.id = commande_article.id_commande
                INNER JOIN article ON commande_article.id_article = article.id
                WHERE YEAR(commande.date_commande) = :year
                GROUP BY article.nom_article
                ORDER BY SUM(commande_article.quantite) DESC
                LIMIT 1";

            $stmt_produit_plus_vendu = $pdo->prepare($sql_produit_plus_vendu);
            $stmt_produit_plus_vendu->bindParam(':year', $currentYear, PDO::PARAM_INT);
            $stmt_produit_plus_vendu->execute();
            $produitPlusVendu = $stmt_produit_plus_vendu->fetch(PDO::FETCH_ASSOC);

            // Requête SQL pour récupérer les produits les plus commandés (top 5)
            $sql_plus_commandes = "
                SELECT article.nom_article AS nom_article, SUM(commande_article.quantite) AS quantite_totale
                FROM commande
                INNER JOIN commande_article ON commande.id = commande_article.id_commande
                INNER JOIN article ON commande_article.id_article = article.id
                WHERE YEAR(commande.date_commande) = :year
                GROUP BY article.nom_article
                ORDER BY SUM(commande_article.quantite) DESC
                LIMIT 5";

            $stmt_plus_commandes = $pdo->prepare($sql_plus_commandes);
            $stmt_plus_commandes->bindParam(':year', $currentYear, PDO::PARAM_INT);
            $stmt_plus_commandes->execute();
            $produitsPlusCommandes = $stmt_plus_commandes->fetchAll(PDO::FETCH_ASSOC);

            // Requête SQL pour calculer la moyenne des commandes
            $sql_moyenne_commandes = "
                SELECT AVG(commande_article.quantite) AS moyenne_commandes
                FROM commande
                INNER JOIN commande_article ON commande.id = commande_article.id_commande
                WHERE YEAR(commande.date_commande) = :year";

            $stmt_moyenne_commandes = $pdo->prepare($sql_moyenne_commandes);
            $stmt_moyenne_commandes->bindParam(':year', $currentYear, PDO::PARAM_INT);
            $stmt_moyenne_commandes->execute();
            $result_moyenne = $stmt_moyenne_commandes->fetch(PDO::FETCH_ASSOC);
            $moyenneCommandes = $result_moyenne['moyenne_commandes'];

            // Requête SQL pour récupérer les détails des produits commandés
            $sql_details_produits = "
                SELECT article.nom_article AS nom_article, SUM(commande_article.quantite) AS quantite_totale
                FROM commande
                INNER JOIN commande_article ON commande.id = commande_article.id_commande
                INNER JOIN article ON commande_article.id_article = article.id
                WHERE YEAR(commande.date_commande) = :year
                GROUP BY article.nom_article
                ORDER BY article.nom_article";

            $stmt_details_produits = $pdo->prepare($sql_details_produits);
            $stmt_details_produits->bindParam(':year', $currentYear, PDO::PARAM_INT);
            $stmt_details_produits->execute();
            $detailsProduits = $stmt_details_produits->fetchAll(PDO::FETCH_ASSOC);

        } catch(PDOException $e) {
            $error = "Erreur de connexion : " . $e->getMessage();
            $commandes = []; // Assurer que $commandes est défini même en cas d'erreur
            echo $error; // Afficher l'erreur pour le débogage
        }
        ?>

        <!-- Sélecteur d'année -->
        <div class="year-selector">
            <a href="?annee=<?= $currentYear - 1 ?>">Année précédente</a>
            <span><?= $currentYear ?></span>
            <a href="?annee=<?= $currentYear + 1 ?>">Année suivante</a>
        </div>

        <!-- Graphique des commandes par mois (barres) -->
        <div class="chart-container">
            <canvas id="commandesParMoisChart"></canvas>
        </div>

        <!-- Produits les plus commandés de l'année (top 5) -->
        <div class="monthly-products">
            <h2>Produits les plus commandés de <?= $currentYear ?></h2>
            <table>
                <tr>
                    <th>Nom du produit</th>
                    <th>Quantité commandée</th>
                </tr>
                <?php foreach ($produitsPlusCommandes as $data): ?>
                    <tr>
                        <td><?= $data['nom_article'] ?></td>
                        <td><?= $data['quantite_totale'] ?> unités</td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Détails des produits -->
        <div class="product-details">
            <h2>Détails des produits commandés en <?= $currentYear ?></h2>
            <table>
                <tr>
                    <th>Nom du produit</th>
                    <th>Quantité totale commandée</th>
                </tr>
                <?php foreach ($detailsProduits as $produit): ?>
                    <tr>
                        <td><?= $produit['nom_article'] ?></td>
                        <td><?= $produit['quantite_totale'] ?> unités</td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

    </div>

    <!-- Script pour le graphique -->
    <script>
        var ctx = document.getElementById('commandesParMoisChart').getContext('2d');
        var commandesParMoisChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: 'Nombre de commandes',
                    data: <?= json_encode($commandCounts) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>

</body>
</html>
