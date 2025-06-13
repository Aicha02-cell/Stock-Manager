<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des réceptions</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Historique des réceptions</h1>

    <!-- Formulaire de recherche par produit -->
    <form method="GET">
        <label for="searchProduct">Rechercher un produit :</label>
        <input type="text" id="searchProduct" name="searchProduct">
        <button type="submit">Rechercher</button>
    </form>

    <!-- Formulaire de recherche par fournisseur -->
    <form method="GET">
        <label for="searchSupplier">Rechercher un fournisseur :</label>
        <input type="text" id="searchSupplier" name="searchSupplier">
        <button type="submit">Rechercher</button>
    </form>

    <!-- Tableau pour afficher les réceptions -->
    <table border="1">
        <thead>
            <tr>
                <th>ID Réception</th>
                <th>Fournisseur</th>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Date de réception</th>
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

            // Initialisation des variables de recherche
            $searchProduct = isset($_GET['searchProduct']) ? $_GET['searchProduct'] : "";
            $searchSupplier = isset($_GET['searchSupplier']) ? $_GET['searchSupplier'] : "";

            // Construction de la requête SQL en fonction des filtres de recherche
            $sql = "SELECT r.id AS id_reception, f.nom AS nom_fournisseur, p.nom_article AS nom_produit, dr.quantite, r.date_reception, r.statut
                    FROM reception r
                    JOIN fournisseurs f ON r.fournisseur_id = f.id
                    JOIN detail_reception dr ON r.id = dr.reception_id
                    JOIN article p ON dr.article_id = p.id";

            // Ajout des conditions de recherche si des termes sont spécifiés
            if (!empty($searchProduct)) {
                $sql .= " WHERE p.nom_article LIKE '%$searchProduct%'";
            } elseif (!empty($searchSupplier)) {
                $sql .= " WHERE f.nom LIKE '%$searchSupplier%'";
            }

            // Exécution de la requête SQL
            $result = $conn->query($sql);

            // Vérification et affichage des résultats
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id_reception']}</td>
                            <td>{$row['nom_fournisseur']}</td>
                            <td>{$row['nom_produit']}</td>
                            <td>{$row['quantite']}</td>
                            <td>{$row['date_reception']}</td>
                            <td>{$row['statut']}</td>
                            <td>"; // Ouvrir la cellule pour les actions

                    // Bouton pour confirmer la réception si le statut est "en attente"
                    if ($row['statut'] === 'en attente') {
                        echo "<button onclick=\"confirmReception({$row['id_reception']})\">Confirmer réception</button>";
                    } else {
                        echo "Produit reçu"; // Sinon, afficher "Produit reçu"
                    }

                    echo "</td></tr>"; // Fermer la cellule des actions et la ligne
                }
            } else {
                echo "<tr><td colspan='7'>Aucune réception trouvée</td></tr>";
            }

            // Fermeture de la connexion
            $conn->close();
            ?>
        </tbody>
    </table>

    <!-- Script JavaScript pour la confirmation de réception -->
    <script>
        function confirmReception(receptionId) {
            if (confirm("Êtes-vous sûr de vouloir confirmer la réception de ce produit ?")) {
                // Envoyer une requête AJAX pour confirmer la réception
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "confirm_reception.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Actualiser la page après la confirmation
                        window.location.reload();
                    }
                };
                xhr.send("id=" + receptionId);
            }
        }
    </script>

</body>

</html>
