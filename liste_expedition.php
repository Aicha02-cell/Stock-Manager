<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des expéditions</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Historique des expéditions</h1>

    <!-- Formulaire de recherche -->
    <form method="GET">
        <label for="searchClient">Rechercher un client :</label>
        <input type="text" id="searchClient" name="searchClient">
        <button type="submit">Rechercher</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID Expédition</th>
                <th>Client</th>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Date d'Expédition</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Connexion à la base de données
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "gestion_stock";
            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Échec de la connexion : " . $conn->connect_error);
            }

            // Recherche de clients si un paramètre de recherche est fourni
            $searchClient = isset($_GET['searchClient']) ? $_GET['searchClient'] : "";

            // Requête SQL pour récupérer les données d'expédition depuis la base de données avec filtres de recherche
            $sql = "SELECT id_exp, client, produit, quantite, date_exp, statut FROM expedition";

            // Ajout de conditions de recherche si un terme de recherche client est fourni
            if (!empty($searchClient)) {
                $sql .= " WHERE client LIKE '%$searchClient%'";
            }

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id_exp']}</td>
                        <td>{$row['client']}</td>
                        <td>{$row['produit']}</td>
                        <td>{$row['quantite']}</td>
                        <td>{$row['date_exp']}</td>
                        <td>{$row['statut']}</td>
                        <td>"; // Ouvrir la cellule pour le bouton d'action
                    if ($row['statut'] === 'En Attente') {
                        echo "<button onclick=\"expedierExpedition({$row['id_exp']})\">Expédier</button>";
                    } else {
                        echo "Expédiée";
                    }
                    echo "</td></tr>"; // Fermer la cellule des boutons d'action et la ligne
                }
            } else {
                echo "<tr><td colspan='7'>Aucune expédition trouvée</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <script>
        function expedierExpedition(expeditionId) {
            if (confirm("Êtes-vous sûr de vouloir expédier cette expédition ?")) {
                // Envoyer une requête AJAX pour mettre à jour le statut de l'expédition
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "expedier_expedition.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Actualiser la page après l'expédition
                        window.location.reload();
                    }
                };
                xhr.send("id=" + expeditionId);
            }
        }
    </script>
</body>

</html>
