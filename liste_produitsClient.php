<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Articles</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assurez-vous que le chemin du fichier CSS est correct -->
    <style>
        /* Votre CSS personnalisé ici */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .product-name {
            font-weight: bold;
            color: blue;
        }
        .product-image {
            max-width: 100px;
            height: auto;
            display: block;
            margin: auto;
        }
        .out-of-stock {
            color: red;
            font-weight: bold;
        }
        .button {
            display: block;
            width: 200px;
            margin: 20px auto;
            text-align: center;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Nos Articles</h1>
    
    <?php
    session_start();
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

    // Traiter la recherche
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    ?>

    <h2>Rechercher un Article </h2>
    <form method="get" action="">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Rechercher...">
        <button type="submit">Rechercher</button>
    </form>

    <table>
        <tr>
            <th>Code article</th>
            <th>Nom</th>
            <th>Prix unitaire</th>
            <th>Date fabrication</th>
            <th>Date expiration</th>
            <th>Images</th>
        </tr>
        <?php
        // Afficher la liste des articles
        $sql = "SELECT a.*, c.libelle_categorie 
                FROM article a
                LEFT JOIN categories c ON a.id_categorie = c.id";
        if ($search) {
            $search = $conn->real_escape_string($search);
            $sql .= " WHERE a.nom_article LIKE '%$search%'";
        }
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Vérifier si la quantité est inférieure à 1 pour indiquer la rupture de stock totale
                if ($row['quantite'] < 1) {
                    echo "<tr class='out-of-stock'>";
                } else {
                    echo "<tr>";
                }
                echo "<td>{$row['id']}</td>
                        <td class='product-name'>{$row['nom_article']}</td>
                       
                        <td>{$row['prix_unitaire']}</td>
                        <td>{$row['date_fabrication']}</td>
                        <td>{$row['date_expiration']}</td>
                        <td><img src='{$row['images']}' alt='Image du produit' class='product-image'></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Aucun article trouvé.</td></tr>";
        }

        $conn->close();
        ?>
    </table>
    
    <a href="commande.php" class="button">Passer une commande</a>
</body>
</html>
