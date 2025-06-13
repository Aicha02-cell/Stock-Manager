<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des réceptions</title>
</head>
<body>
    <h1>Liste des réceptions</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID Réception</th>
                <th>Fournisseur</th>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Date de réception</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Connexion à la base de données
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "reception";
            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Échec de la connexion : " . $conn->connect_error);
            }

            // Récupérer les réceptions et leurs détails
            $sql = "SELECT r.id AS reception_id, f.nom AS fournisseur_nom, p.nom AS produits_nom, dr.quantite, r.date_reception, r.statut
                    FROM reception r
                    JOIN fournisseur f ON r.fournisseur_id = f.id
                    JOIN detail_reception dr ON r.id = dr.reception_id
                    JOIN produits p ON dr.produit_id = p.id_produits";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['reception_id']}</td>
                        <td>{$row['fournisseur_nom']}</td>
                        <td>{$row['produits_nom']}</td>
                        <td>{$row['quantite']}</td>
                        <td>{$row['date_reception']}</td>
                        <td>{$row['statut']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Aucune réception trouvée</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
