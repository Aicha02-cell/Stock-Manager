<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Styles personnalisés pour les classes de ligne */
        .actions a {
            margin-right: 10px;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .notification-rupture {
            color: red;
            font-weight: bold;
        }
        .table-success {
            background-color: #d4edda !important; /* Couleur pour commande acceptée */
        }
        .table-danger {
            background-color: #f8d7da !important; /* Couleur pour rupture de stock */
        }
        .table-warning {
            background-color: #fff3cd !important; /* Couleur pour en attente */
        }
        .table-dark {
            background-color: #343a40 !important; /* Couleur pour commande annulée */
        }
        .print-button {
            display: inline-block;
            margin-left: 10px;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .print-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Liste des Commandes</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">RETOUR</a>
        <form method="get" action="" class="mb-3">
            <div class="form-group">
                <label for="annee">Filtrer par année:</label>
                <input type="number" class="form-control" id="annee" name="annee" placeholder="Entrez l'année" value="<?= isset($_GET['annee']) ? htmlspecialchars($_GET['annee']) : '' ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
        <div id="commandes-section">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Nom Client</th>
                        <th>Prénom Client</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Quantité Commandée</th>
                        <th>Prix Total</th>
                        <th>Date Commande</th>
                        <th>Notification</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "gestion_stock";

                    // Créer une connexion
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Vérifier la connexion
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Traiter la recherche par année
                    $annee = isset($_GET['annee']) ? $_GET['annee'] : '';

                    // Requête SQL pour récupérer les commandes avec les détails requis
                    $sql = "SELECT c.id AS commande_id, a.id AS article_id, a.nom_article, c.nom AS client_nom, c.prenom AS client_prenom, c.adresse AS client_adresse, c.telephone AS client_telephone, ca.quantite AS quantite_commandee, a.prix_unitaire, c.date_commande, c.statut,
                            (ca.quantite * a.prix_unitaire) AS prix_total, a.quantite AS quantite_en_stock
                            FROM commande c
                            INNER JOIN commande_article ca ON c.id = ca.id_commande
                            INNER JOIN article a ON ca.id_article = a.id";

                    if (!empty($annee)) {
                        $sql .= " WHERE YEAR(c.date_commande) = ?";
                    }

                    $stmt = $conn->prepare($sql);

                    if (!empty($annee)) {
                        $stmt->bind_param("i", $annee);
                    }

                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($commande = $result->fetch_assoc()) {
                            // Déterminer la classe CSS en fonction du statut de la commande
                            $ligneClass = '';
                            $notification = '';

                            // Déterminer la classe CSS pour le statut
                            switch ($commande['statut']) {
                                case 'acceptee':
                                    $ligneClass = 'table-success'; // Commande acceptée
                                    break;
                                case 'rupture':
                                    $ligneClass = 'table-danger'; // Rupture de stock
                                    break;
                                case 'en attente':
                                    $ligneClass = 'table-warning'; // En attente de confirmation
                                    break;
                                case 'annulee':
                                    $ligneClass = 'table-dark'; // Commande annulée
                                    break;
                                default:
                                    $ligneClass = 'table-warning'; // Commande en attente
                                    break;
                            }

                            // Affichage de la ligne dans le tableau HTML
                            echo "<tr class='$ligneClass'>
                                    <td>{$commande['nom_article']}</td>
                                    <td>{$commande['client_nom']}</td>
                                    <td>{$commande['client_prenom']}</td>
                                    <td>{$commande['client_adresse']}</td>
                                    <td>{$commande['client_telephone']}</td>
                                    <td>{$commande['quantite_commandee']}</td>
                                    <td>{$commande['prix_total']}</td>
                                    <td>{$commande['date_commande']}</td>
                                    <td class='notification-rupture'>";

                            // Gérer les notifications spécifiques
                            if ($commande['statut'] == 'rupture') {
                                $notification = 'Commande disponible dans 48h';
                            } else if ($commande['statut'] == 'en attente') {
                                $notification = 'Réapprovisionné';
                            }

                            echo "$notification</td>
                                    <td class='actions'>";

                            // Afficher les actions en fonction du statut de la commande
                            if ($commande['statut'] == 'en attente') {
                                echo "<a href='accepter_commande.php?id={$commande['commande_id']}&action=accept' class='btn btn-success btn-sm' onclick='return confirm(\"Voulez-vous vraiment accepter cette commande ?\");'>Accepter</a>  ";
                            } else if ($commande['statut'] == 'rupture') {
                                echo "<a href='accepter_commande.php?id={$commande['commande_id']}&action=accept' class='btn btn-success btn-sm' onclick='return confirm(\"Voulez-vous vraiment accepter cette commande malgré la rupture de stock ?\");'>Accepter</a>  ";
                            } else if ($commande['statut'] != 'acceptee') {
                                echo "<a href='accepter_commande.php?id={$commande['commande_id']}&action=accept' class='btn btn-success btn-sm' onclick='return confirm(\"Voulez-vous vraiment accepter cette commande ?\");'>Accepter</a>  ";
                            }

                            // Lien pour imprimer la commande
                            echo "<a href='imprimer_commande.php?id={$commande['commande_id']}' class='btn btn-primary btn-sm' onclick='return confirm(\"Voulez-vous imprimer cette commande ?\");'>Imprimer</a>";

                            echo "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>Aucune commande trouvée.</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Bootstrap JS CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Script pour imprimer les commandes -->
    <script>
        function printCommandes() {
            var printContents = document.getElementById('commandes-section').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
</body>
</html>
