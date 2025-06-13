<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    die(); // Arrête l'exécution si la connexion échoue
}

// Vérification de l'ID de la commande à imprimer depuis $_GET
$commandeIdToPrint = isset($_GET['id']) ? $_GET['id'] : null;

// Fonction pour récupérer une commande spécifique par son ID
function getCommandeById($pdo, $commandeId) {
    $stmt = $pdo->prepare("SELECT c.id, c.date_commande, c.nom AS client_nom, c.prenom, c.telephone, c.adresse, c.statut,
                                  a.nom_article,
                                  ca.quantite,
                                  a.prix_unitaire,
                                  ca.quantite * a.prix_unitaire AS prix_total_article
                           FROM commande c
                           INNER JOIN commande_article ca ON c.id = ca.id_commande
                           INNER JOIN article a ON ca.id_article = a.id
                           WHERE c.id = :commandeId");
    $stmt->bindParam(':commandeId', $commandeId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer la commande spécifique à imprimer
if ($commandeIdToPrint) {
    $commandes = getCommandeById($pdo, $commandeIdToPrint);
} else {
    // Redirection vers une page d'erreur si aucun ID n'est spécifié
    header("Location: erreur.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <style>
        /* Styles CSS pour la mise en page */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .commande {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        td {
            background-color: #ffffff;
            border-bottom: 1px solid #dddddd;
        }
        h2 {
            color: #333;
        }
        h3 {
            color: #4CAF50;
            margin-bottom: 10px;
        }
        p {
            margin: 5px 0;
        }
        .print-button {
            display: block;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            cursor: pointer;
            margin: 20px auto;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .print-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Facture</h2>
    </div>

    <?php foreach ($commandes as $commande): ?>
        <div class="commande" id="commande<?= $commande['id'] ?>">
            <h3>Facture #<?= htmlspecialchars($commande['id']) ?></h3>
            <p>Date: <?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($commande['date_commande']))) ?></p>
            <p><strong>Nom du client :</strong> <?= htmlspecialchars($commande['client_nom'] . ' ' . $commande['prenom']) ?></p>
            <p><strong>Tel :</strong> <?= htmlspecialchars($commande['telephone']) ?></p>
            <p><strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse']) ?></p>

            <!-- Tableau des articles commandés -->
            <table>
                <thead>
                    <tr>
                        <th>Designation</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Prix total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_commande = 0; // Initialisation du montant total de la commande

                    // Afficher toutes les lignes de commande pour cette facture spécifique
                    foreach ($commandes as $cmd):
                        $prix_total_article = $cmd['quantite'] * $cmd['prix_unitaire'];
                        $total_commande += $prix_total_article;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($cmd['nom_article']) ?></td>
                            <td><?= htmlspecialchars($cmd['quantite']) ?></td>
                            <td><?= htmlspecialchars($cmd['prix_unitaire']) ?></td>
                            <td><?= htmlspecialchars($prix_total_article) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total :</strong></td>
                        <td><strong><?= htmlspecialchars($total_commande) ?></strong></td>
                    </tr>
                </tbody>
            </table>
            <button class="print-button" onclick="printCommande(<?= $commande['id'] ?>);"><i class='bx bx-printer'></i> Imprimer cette facture</button>
        </div>
    <?php endforeach; ?>

    <script>
        function printCommande(commandeId) {
            var printContents = document.getElementById('commande' + commandeId).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
</div>

</body>
</html>
