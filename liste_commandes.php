<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des commandes avec détails des articles depuis la base de données
$sql = "SELECT c.id, c.prenom, c.nom, c.adresse, c.telephone, c.date_commande, c.total AS total_commande, c.statut, 
               ca.id_article, a.nom_article, ca.quantite, a.prix_unitaire, (ca.quantite * a.prix_unitaire) AS total_article
        FROM commande c
        JOIN commande_article ca ON c.id = ca.id_commande
        JOIN article a ON ca.id_article = a.id";
$stmt = $pdo->query($sql);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de l'acceptation d'une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accepter'])) {
    $id_commande = $_POST['id_commande'];
    $stmt = $pdo->prepare("UPDATE commande SET statut = 'Acceptée', date_acceptation = NOW() WHERE id = :id_commande");
    $stmt->execute(['id_commande' => $id_commande]);

    // Mise à jour du statut des articles
    $stmt_articles = $pdo->prepare("UPDATE article 
                                    SET statut = CASE WHEN quantite <= 0 THEN 'Rupture' ELSE 'Disponible' END 
                                    WHERE id IN (SELECT id_article FROM commande_article WHERE id_commande = :id_commande)");
    $stmt_articles->execute(['id_commande' => $id_commande]);

    echo "<script>alert('Commande acceptée et mise à jour des articles !');</script>";
}

// Traitement de la livraison d'une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livrer'])) {
    $id_commande = $_POST['id_commande'];
    $stmt = $pdo->prepare("UPDATE commande SET statut = 'Livrée', date_livraison = NOW() WHERE id = :id_commande");
    $stmt->execute(['id_commande' => $id_commande]);

    echo "<script>alert('Commande livrée !');</script>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Commandes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .table-custom {
            background-color: #ffffff;
            border-radius: 8px;
        }
        .table-custom thead th {
            background-color: #e9ecef;
        }
        .btn-custom {
            background-color: #17a2b8;
            color: white;
            border-radius: 20px;
            padding: 10px 20px;
        }
        .btn-custom:hover {
            background-color: #138496;
        }
        .btn-secondary-custom {
            background-color: #28a745;
            color: white;
            border-radius: 20px;
            padding: 10px 20px;
        }
        .btn-secondary-custom:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center mb-4">Liste des Commandes</h1>
    <table class="table table-bordered table-custom">
        <thead>
            <tr>
                <th>ID Commande</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Date Commande</th>
                <th>Article</th>
                <th>Quantité</th>
                <th>Prix Unitaire (FCFA)</th>
                <th>Total Article (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $currentCommandeId = null;
            $totalCommande = 0;

            foreach ($commandes as $commande):
                if ($commande['id'] !== $currentCommandeId):
                    if ($currentCommandeId !== null):
                        echo "<tr>
                                <td colspan='9' class='text-right font-weight-bold'>Total Commande</td>
                                <td>".number_format($totalCommande, 0, '', ' ')." FCFA</td>
                              </tr>";
                    endif;
                    $currentCommandeId = $commande['id'];
                    $totalCommande = 0; // Reset the total for the new commande
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['id']) ?></td>
                        <td><?= htmlspecialchars($commande['prenom']) ?></td>
                        <td><?= htmlspecialchars($commande['nom']) ?></td>
                        <td><?= htmlspecialchars($commande['adresse']) ?></td>
                        <td><?= htmlspecialchars($commande['telephone']) ?></td>
                        <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                        <td><?= htmlspecialchars($commande['nom_article']) ?></td>
                        <td><?= htmlspecialchars($commande['quantite']) ?></td>
                        <td><?= number_format($commande['prix_unitaire'], 0, '', ' ') ?> FCFA</td>
                        <td><?= number_format($commande['total_article'], 0, '', ' ') ?> FCFA</td>
                    <?php
                    $totalCommande += $commande['total_article'];
                else:
                    ?>
                        <tr>
                            <td></td> <!-- Cellule vide pour aligner les autres colonnes -->
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><?= htmlspecialchars($commande['nom_article']) ?></td>
                            <td><?= htmlspecialchars($commande['quantite']) ?></td>
                            <td><?= number_format($commande['prix_unitaire'], 0, '', ' ') ?> FCFA</td>
                            <td><?= number_format($commande['total_article'], 0, '', ' ') ?> FCFA</td>
                        </tr>
                    <?php
                    $totalCommande += $commande['total_article'];
                endif;
            endforeach;

            // Print the total for the last commande
            if ($currentCommandeId !== null):
                echo "<tr>
                        <td colspan='9' class='text-right font-weight-bold'>Total Commande</td>
                        <td>".number_format($totalCommande, 0, '', ' ')." FCFA</td>
                      </tr>";
            endif;
            ?>
        </tbody>
    </table>
    <a href="index.php" class="btn btn-secondary-custom">Retour à la Page d'Accueil</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
