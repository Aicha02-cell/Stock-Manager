<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background: #f4f4f9;
            background-image: url('entrepot-de-stockage.jpg');
            background-size: cover;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h3 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .fa-box-open {
            color: #FFC107;
        }
        .fa-box {
            color: #4CAF50;
        }
        .highlight {
            font-weight: bold;
            color: red;
        }
    </style>
</head>
<body>
<a href="admin_dashboard.php" class="btn btn-secondary mb-3">Retour</a>
<div class="container">
     
    <!-- Section des stocks en cours -->
    <h3>Les stocks en cours (basé sur les articles)</h3>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th><i class="fas fa-box-open"></i> Article ID</th>
                <th><i class="fas fa-pills"></i> Nom de l'Article</th>
                <th><i class="fas fa-box-open"></i> Quantité Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Connexion à la base de données
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "gestion_stock";

            // Création de la connexion
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Vérifier la connexion
            if ($conn->connect_error) {
                die("La connexion a échoué : " . $conn->connect_error);
            }

            // Requête SQL pour récupérer les informations sur les articles
            $sqlArticles = "SELECT id, nom_article, quantite FROM article";
            $resultArticles = $conn->query($sqlArticles);

            if ($resultArticles) {
                // Afficher les données de chaque ligne dans le tableau
                while ($row = $resultArticles->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nom_article"] . "</td>";
                    echo "<td>" . $row["quantite"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Aucune donnée d'article trouvée.</td></tr>";
            }

            // Requête SQL pour obtenir les sorties de stock
            $sqlCommandes = "SELECT article.nom_article AS nom_article, SUM(commande_article.quantite) AS quantite_commande
                             FROM commande_article
                             INNER JOIN article ON commande_article.id_article = article.id 
                             INNER JOIN commande ON commande_article.id_commande = commande.id
                             WHERE commande.statut = 'acceptée'
                             GROUP BY article.nom_article";
            $resultCommandes = $conn->query($sqlCommandes);

            // Requête SQL pour obtenir l'article le plus commandé
            $sqlArticlePlusCommandé = "SELECT article.nom_article AS nom_article, SUM(commande_article.quantite) AS quantite_commande
                                       FROM commande_article
                                       INNER JOIN article ON commande_article.id_article = article.id 
                                       INNER JOIN commande ON commande_article.id_commande = commande.id
                                       WHERE commande.statut = 'acceptée'
                                       GROUP BY article.nom_article
                                       ORDER BY quantite_commande DESC
                                       LIMIT 1";
            $resultArticlePlusCommandé = $conn->query($sqlArticlePlusCommandé);
            $articlePlusCommandé = '';
            $quantiteArticlePlusCommandé = 0;

        
            // Requête SQL pour obtenir le nombre total de commandes acceptées
            $sqlCommandesAcceptées = "SELECT COUNT(*) AS total_acceptées FROM commande WHERE statut = 'acceptée'";
            $resultCommandesAcceptées = $conn->query($sqlCommandesAcceptées);
            
            if ($resultCommandesAcceptées) {
                $rowCommandesAcceptées = $resultCommandesAcceptées->fetch_assoc();
                $totalCommandesAcceptées = $rowCommandesAcceptées['total_acceptées'];
            } else {
                $totalCommandesAcceptées = 0;
            }

            $conn->close();
            ?>

    <!-- Section des mouvements des articles -->
    <table class="table table-striped table-bordered">
    <h3>Les sorties des produits (basé sur les commandes acceptées)</h3>e
        <thead>
            <tr>
                <th><i class="fas fa-box-open"></i> Article</th>
                <th><i class="fas fa-box"></i> Quantité Commandée</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Connexion à la base de données
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "gestion_stock";

            // Création de la connexion
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Vérifier la connexion
            if ($conn->connect_error) {
                die("La connexion a échoué : " . $conn->connect_error);
            }

            // Requête SQL pour obtenir les sorties de stock basées sur les commandes acceptées
            $sqlCommandes = "
                SELECT a.nom_article, SUM(ca.quantite) AS quantite_commande
                FROM commande_article ca
                INNER JOIN commande c ON ca.id_commande = c.id
                INNER JOIN article a ON ca.id_article = a.id
                WHERE c.statut = 'acceptee'
                GROUP BY a.nom_article
            ";
            $resultCommandes = $conn->query($sqlCommandes);

            // Requête SQL pour obtenir l'article le plus commandé
            $sqlArticlePlusCommandé = "
                SELECT a.nom_article, SUM(ca.quantite) AS quantite_commande
                FROM commande_article ca
                INNER JOIN commande c ON ca.id_commande = c.id
                INNER JOIN article a ON ca.id_article = a.id
                WHERE c.statut = 'acceptee'
                GROUP BY a.nom_article
                ORDER BY quantite_commande DESC
                LIMIT 1
            ";
            $resultArticlePlusCommandé = $conn->query($sqlArticlePlusCommandé);
            $articlePlusCommandé = '';
            $quantiteArticlePlusCommandé = 0;

            if ($resultArticlePlusCommandé) {
                $rowArticlePlusCommandé = $resultArticlePlusCommandé->fetch_assoc();
                $articlePlusCommandé = $rowArticlePlusCommandé['nom_article'];
                $quantiteArticlePlusCommandé = $rowArticlePlusCommandé['quantite_commande'];
            }

            if ($resultCommandes) {
                while ($rowCommande = $resultCommandes->fetch_assoc()) {
                    $class = ($rowCommande['nom_article'] === $articlePlusCommandé) ? 'highlight' : '';
                    echo "<tr class='{$class}'>";
                    echo "<td>" . htmlspecialchars($rowCommande['nom_article']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowCommande['quantite_commande']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Aucune donnée de commande trouvée.</td></tr>";
            }

            // Afficher l'article le plus commandé
            if ($articlePlusCommandé) {
                echo "<tr class='highlight'>
                        <td>Article le plus commandé</td>
                        <td><strong>{$articlePlusCommandé} avec {$quantiteArticlePlusCommandé} unités</strong></td>
                      </tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>