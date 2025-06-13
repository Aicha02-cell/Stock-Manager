<?php
session_start(); // Démarrer la session pour utiliser $_SESSION

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

// Créer une connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("<p class='error'>Connection failed: " . $conn->connect_error . "</p>");
}

// Fonction pour obtenir la liste des catégories depuis la base de données
function getCategorie() {
    global $conn;
    $categories = array();
    $sql = "SELECT id, libelle_categorie FROM categories";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}

// Fonction pour obtenir les détails d'un article par son ID
function getArticle($id) {
    global $conn;
    $article = array();
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM article WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $article = $result->fetch_assoc();
    }
    return $article;
}

// Validation et traitement du formulaire d'ajout/modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nom_article']) && !empty($_POST['id_categorie']) && !empty($_POST['quantite']) && !empty($_POST['prix_unitaire']) && !empty($_POST['date_fabrication']) && !empty($_POST['date_expiration']) && !empty($_POST['nom_fournisseur'])) {
        // Récupération des données du formulaire
        $nom_article = $conn->real_escape_string($_POST['nom_article']);
        $id_categorie = $conn->real_escape_string($_POST['id_categorie']);
        $quantite = $conn->real_escape_string($_POST['quantite']);
        $prix_unitaire = $conn->real_escape_string($_POST['prix_unitaire']);
        $date_fabrication = $conn->real_escape_string($_POST['date_fabrication']);
        $date_expiration = $conn->real_escape_string($_POST['date_expiration']);
        $nom_fournisseur = $conn->real_escape_string($_POST['nom_fournisseur']);

        // Gestion de l'image
        $images = ''; // Variable pour stocker le chemin de l'image

        // Vérifier si un fichier d'image a été téléchargé
        if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['images']['tmp_name'];
            $name = basename($_FILES['images']['name']);
            $destination = 'public/images/' . $name; // Chemin relatif à stocker dans la DB

            // Déplacer le fichier téléchargé vers le dossier de destination
            if (move_uploaded_file($tmp_name, $destination)) {
                $images = $destination;
            } else {
                $_SESSION['message'] = array('text' => 'Erreur lors de l\'upload de l\'image.', 'type' => 'error');
                header("Location: ajoutArticle.php");
                exit();
            }
        }

        // Vérification de l'existence d'un article avec le même nom et la même catégorie
        $sql_check = "SELECT * FROM article WHERE nom_article = '$nom_article' AND id_categorie = '$id_categorie'";
        $result_check = $conn->query($sql_check);

        if ($result_check && $result_check->num_rows > 0) {
            // Article existant, mettre à jour la quantité
            $row = $result_check->fetch_assoc();
            $new_quantite = $row['quantite'] + $quantite;
            $sql_update = "UPDATE article SET quantite = $new_quantite, prix_unitaire = '$prix_unitaire', date_fabrication = '$date_fabrication', date_expiration = '$date_expiration', images = '$images', nom_fournisseur = '$nom_fournisseur' WHERE id = {$row['id']}";

            if ($conn->query($sql_update) === TRUE) {
                $_SESSION['message'] = array('text' => 'Quantité mise à jour pour l\'article existant.', 'type' => 'success');
            } else {
                $_SESSION['message'] = array('text' => 'Erreur lors de la mise à jour de l\'article: ' . $conn->error, 'type' => 'error');
            }
        } else {
            // Ajout d'un nouvel article car aucun article existant avec le même nom et la même catégorie
            $sql_insert = "INSERT INTO article (nom_article, id_categorie, quantite, prix_unitaire, date_fabrication, date_expiration, images, nom_fournisseur) VALUES ('$nom_article', '$id_categorie', '$quantite', '$prix_unitaire', '$date_fabrication', '$date_expiration', '$images', '$nom_fournisseur')";

            if ($conn->query($sql_insert) === TRUE) {
                $_SESSION['message'] = array('text' => 'Article ajouté avec succès.', 'type' => 'success');
            } else {
                $_SESSION['message'] = array('text' => 'Erreur lors de l\'ajout de l\'article: ' . $conn->error, 'type' => 'error');
            }
        }
    } else {
        $_SESSION['message'] = array('text' => 'Tous les champs sont obligatoires.', 'type' => 'error');
    }

    // Redirection vers la liste des produits après traitement du formulaire
    header("Location: liste_Produit.php");
    exit();
}

// Récupérer les détails de l'article pour édition si l'ID est spécifié dans l'URL
if (!empty($_GET['id'])) {
    $article = getArticle($_GET['id']);
} else {
    $article = array(
        'nom_article' => '',
        'id_categorie' => '',
        'quantite' => '',
        'prix_unitaire' => '',
        'date_fabrication' => '',
        'date_expiration' => '',
        'images' => '',
        'nom_fournisseur' => ''
    );
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Articles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background: #f4f4f9;
            background-image: url('art.jpg'); /* Remplacez par le chemin de votre image de fond */
            background-size: cover;
            background-attachment: fixed; /* L'image de fond reste fixe */
            background-position: center; /* Centrer l'image */
        }
        .container {
            margin: 2em auto;
            padding: 2em;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 800px;
            text-align: center;
        }
        h1 {
            margin-bottom: 1em;
            color: #333;
            font-size: 2em;
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
            font-weight: bold;
            color: #444;
        }
        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 0.75em;
            margin-bottom: 1em;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1em;
            color: #333;
        }
        input[type="file"] {
            padding: 0;
            font-size: 1em;
        }
        button {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background: #007bff;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background: #0056b3;
        }
        .link {
            display: block;
            margin-top: 1.5em;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .link:hover {
            color: #0056b3;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .preview-container {
            margin-top: 1em;
            text-align: center;
        }
        .preview-container img {
            max-width: 100%;
            height: auto;
            display: none;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 5px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Articles</h1>
        <?php
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            echo "<p class='{$message['type']}'>{$message['text']}</p>";
            unset($_SESSION['message']);
        }
        ?>
        <a href="admin_dashboard.php" class="link">RETOUR</a>
        <form action="<?= !empty($_GET['id']) ? "ajoutArticle.php?id={$article['id']}" : "ajoutArticle.php" ?>" method="post" enctype="multipart/form-data">
            <label for="nom_article">Nom de l'article</label>
            <input value="<?= htmlspecialchars($article['nom_article']) ?>" type="text" name="nom_article" id="nom_article" placeholder="Veuillez saisir le nom" required>
            
            <label for="id_categorie">Catégorie</label>
            <select name="id_categorie" id="id_categorie" required>
                <?php
                $categories = getCategorie();
                foreach ($categories as $categorie) {
                    $selected = $categorie['id'] == $article['id_categorie'] ? 'selected' : '';
                    echo "<option value='{$categorie['id']}' $selected>{$categorie['libelle_categorie']}</option>";
                }
                ?>
            </select>
            
            <label for="quantite">Quantité</label>
            <input value="<?= htmlspecialchars($article['quantite']) ?>" type="number" name="quantite" id="quantite" placeholder="Veuillez saisir la quantité" required>
            
            <label for="prix_unitaire">Prix Unitaire</label>
            <input value="<?= htmlspecialchars($article['prix_unitaire']) ?>" type="number" step="0.01" name="prix_unitaire" id="prix_unitaire" placeholder="Veuillez saisir le prix unitaire" required>
            
            <label for="date_fabrication">Date de Fabrication</label>
            <input value="<?= htmlspecialchars($article['date_fabrication']) ?>" type="datetime-local" name="date_fabrication" id="date_fabrication" required>
            
            <label for="date_expiration">Date d'Expiration</label>
            <input value="<?= htmlspecialchars($article['date_expiration']) ?>" type="datetime-local" name="date_expiration" id="date_expiration" required>
            
            <label for="images">Image de l'article</label>
            <input type="file" name="images" id="images" accept="image/*">
            
            <!-- Zone de prévisualisation de l'image -->
            <div class="preview-container">
                <img id="preview" src="" alt="Prévisualisation de l'image">
            </div>
            
            <label for="nom_fournisseur">Nom du Fournisseur</label>
            <input value="<?= htmlspecialchars($article['nom_fournisseur']) ?>" type="text" name="nom_fournisseur" id="nom_fournisseur" placeholder="Veuillez saisir le nom du fournisseur" required>

            <button type="submit">Enregistrer</button>
        </form>
        <a href="liste_Produit.php" class="link">Voir la liste des produits</a>
        <a href="LISTE_FOUR.html" class="link">Voir la liste des fournisseurs</a>
    </div>
    
    <script>
        document.getElementById('images').addEventListener('change', function(event) {
            var file = event.target.files[0];
            var preview = document.getElementById('preview');

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>
