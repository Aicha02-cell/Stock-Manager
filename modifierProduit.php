<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit</title>
    <style>
        body {
            font-family: cursive;
            color: #fff;
            margin: 0;
            padding: 0;
            background: rgb(2, 0, 36);
            background: linear-gradient(117deg, rgba(2, 0, 36, 1) 0%, rgba(125, 129, 150, 0.9757503221014968) 96%, rgba(222, 199, 45, 1) 98%, rgba(142, 49, 242, 0.5859143339953169) 98%);
            text-align: center;
        }
        .container {
            margin: 2em auto;
            padding: 2em;
            border-radius: 1.25rem;
            max-width: 800px;
            background-color: rgba(0, 0, 0, 0.5);
        }
        h1 {
            margin-bottom: 1em;
        }
        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 1em;
        }
        label {
            margin-top: 1em;
            margin-bottom: 0.5em;
            text-align: left;
            color: #fff;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 0.75em;
            margin-bottom: 1em;
            border: none;
            border-radius: 8px;
            box-sizing: border-box;
        }
        button {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background: rgb(2, 0, 36);
            color: inherit;
            border-radius: 15px;
            cursor: pointer;
            border: none;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background: rgba(2, 0, 36, 0.8);
        }
        .link {
            display: block;
            margin-top: 1.5em;
            text-decoration: none;
            color: #FFD700;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modifier Produit</h1>
        
        <?php
        session_start();

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "gestion_stock";

        // Créer une connexion à la base de données
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Vérifier la connexion
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Vérifier si l'identifiant de l'article est passé via l'URL
        if (isset($_GET['id_article'])) {
            $id_article = $_GET['id_article'];

            // Récupérer les détails de l'article à partir de la base de données
            $sql = "SELECT * FROM article WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_article);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Pré-remplir le formulaire de modification avec les données de l'article
                ?>
                <form action="modifierArticle.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id_article" value="<?php echo $row['id']; ?>">
                    
                    <label for="nom_article">Nom de l'article</label>
                    <input type="text" name="nom_article" id="nom_article" value="<?php echo $row['nom_article']; ?>" required>
                    
                    <label for="id_categorie">Catégorie</label>
                    <select name="id_categorie" id="id_categorie" required>
                        <option value="">-- Choisissez une catégorie --</option>
                        <?php
                        // Récupérer la liste des catégories
                        $sql_categories = "SELECT id, libelle_categorie FROM categories";
                        $result_categories = $conn->query($sql_categories);
                        if ($result_categories->num_rows > 0) {
                            while ($categorie = $result_categories->fetch_assoc()) {
                                $selected = ($row['id_categorie'] == $categorie['id']) ? "selected" : "";
                                echo "<option value='{$categorie['id']}' $selected>{$categorie['libelle_categorie']}</option>";
                            }
                        }
                        ?>
                    </select>
                    
                    <label for="quantite">Quantité</label>
                    <input type="number" name="quantite" id="quantite" value="<?php echo $row['quantite']; ?>" required>
                    
                    <label for="prix_unitaire">Prix unitaire</label>
                    <input type="number" name="prix_unitaire" id="prix_unitaire" value="<?php echo $row['prix_unitaire']; ?>" required>
                    
                    <label for="date_fabrication">Date de fabrication</label>
                    <input type="datetime-local" name="date_fabrication" id="date_fabrication" value="<?php echo date('Y-m-d\TH:i', strtotime($row['date_fabrication'])); ?>" required>
                    
                    <label for="date_expiration">Date d'expiration</label>
                    <input type="datetime-local" name="date_expiration" id="date_expiration" value="<?php echo date('Y-m-d\TH:i', strtotime($row['date_expiration'])); ?>" required>
                    
                    <label for="images">Image actuelle</label><br>
                    <?php if (!empty($row['images'])): ?>
                        <img src="<?php echo $row['images']; ?>" alt="Image actuelle" style="max-width: 200px;"><br><br>
                    <?php else: ?>
                        <p>Aucune image disponible.</p><br>
                    <?php endif; ?>
                    
                    <label for="new_image">Nouvelle image (optionnel)</label>
                    <input type="file" name="images" id="images">
                    
                    <button type="submit">Modifier</button>
                </form>
                <?php
            } else {
                echo "<p class='error'>Aucun article trouvé avec cet identifiant.</p>";
            }

            $stmt->close();
        } else {
            echo "<p class='error'>Identifiant de l'article manquant.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
