<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traiter les soumissions de formulaires pour la suppression et le réapprovisionnement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id_article = isset($_POST['id']) ? $_POST['id'] : '';

    if ($_POST['action'] == 'replenish') {
        $replenish_quantity = isset($_POST['replenish_quantity']) ? intval($_POST['replenish_quantity']) : 0;

        if ($id_article && $replenish_quantity > 0) {
            $stmt = $conn->prepare("UPDATE article SET quantite = quantite + ? WHERE id=?");
            $stmt->bind_param("ii", $replenish_quantity, $id_article);

            if ($stmt->execute()) {
                echo "<p>Article réapprovisionné avec succès.</p>";
            } else {
                echo "<p>Erreur: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Veuillez spécifier une quantité valide à réapprovisionner.</p>";
        }
    } elseif ($_POST['action'] == 'delete') {
        if ($id_article) {
            $stmt = $conn->prepare("DELETE FROM article WHERE id=?");
            $stmt->bind_param("i", $id_article);

            if ($stmt->execute()) {
                echo "<p>Article supprimé avec succès.</p>";
            } else {
                echo "<p>Erreur lors de la suppression: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Identifiant d'article manquant pour la suppression.</p>";
        }
    }
}

// Traiter la recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Articles</title>
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
        .action-buttons {
            text-align: center;
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
    </style>
</head>
<body>
    <h1>Liste des Articles</h1>
    <a href="Produit.php" class="redirect-button">RETOUR</a>

    <h2><a href="Produit.php">Ajouter un Article</a></h2>

    <h2>Rechercher un Article</h2>
    <form method="get" action="">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Rechercher...">
        <button type="submit">Rechercher</button>
    </form>

    <table>
        <tr>
            <th>Code article</th>
            <th>Nom</th>
            <th>Catégorie</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Date fabrication</th>
            <th>Date expiration</th>
            <th>Images</th>
            <th>Nom Fournisseur</th> <!-- Nouvelle colonne pour le nom du fournisseur -->
            <th>Action</th>
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
                      <td>{$row['libelle_categorie']}</td>
                      <td>{$row['quantite']}</td>
                      <td>{$row['prix_unitaire']}</td>
                      <td>{$row['date_fabrication']}</td>
                      <td>{$row['date_expiration']}</td>
                      <td><img src='{$row['images']}' alt='Image du produit' class='product-image'></td>
                      <td>{$row['nom_fournisseur']}</td> <!-- Affichage du nom du fournisseur -->
                      <td class='action-buttons'>";
                      
                if ($row['quantite'] < 1) {
                    echo "<span>Rupture de stock</span>";
                } else {
                    echo "<a href='modifierProduit.php?id_article={$row['id']}' class='edit'>Modifier</a>";
                }
                
                echo "<form action='liste_Produit.php' method='post' style='display:inline;'>
                          <input type='hidden' name='id' value='{$row['id']}'>
                          <input type='number' name='replenish_quantity' value='1' min='1' required>
                          <button type='submit' name='action' value='replenish'>Réapprovisionner</button>
                          <button type='submit' name='action' value='delete'>Supprimer</button>
                      </form>
                  </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>Aucun article trouvé.</td></tr>"; // Mise à jour du colspan
        }

        $conn->close();
        ?>
    </table>
</body>
</html>
