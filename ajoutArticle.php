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
        $upload_dir = 'public/images/'; // Répertoire de destination

        // Vérifier si un fichier d'image a été téléchargé
        if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['images']['tmp_name'];
            $name = basename($_FILES['images']['name']);
            $destination = $upload_dir . $name; // Répertoire accessible en écriture

            // Créer le répertoire si il n'existe pas
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    $_SESSION['message'] = array('text' => 'Erreur lors de la création du répertoire.', 'type' => 'error');
                    header("Location: ajoutArticle.php");
                    exit();
                }
            }

            // Déplacer le fichier téléchargé vers le dossier de destination
            if (move_uploaded_file($tmp_name, $destination)) {
                $images = $destination;
            } else {
                $_SESSION['message'] = array('text' => 'Erreur lors du déplacement du fichier.', 'type' => 'error');
                header("Location: ajoutArticle.php");
                exit();
            }
        } elseif (isset($_FILES['images']) && $_FILES['images']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Gestion des erreurs spécifiques
            switch ($_FILES['images']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $message = 'Le fichier est trop grand.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message = 'Le fichier n\'a été que partiellement téléchargé.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message = 'Le dossier temporaire est manquant.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message = 'Échec de l\'écriture du fichier sur le disque.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message = 'Une extension PHP a arrêté l\'envoi du fichier.';
                    break;
                default:
                    $message = 'Erreur inconnue lors de l\'envoi du fichier.';
                    break;
            }
            $_SESSION['message'] = array('text' => $message, 'type' => 'error');
            header("Location: ajoutArticle.php");
            exit();
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
    header("Location: produit.php");
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
            font-family: cursive;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 2em auto;
            padding: 2em;
            background: rgb(2, 0, 36);
            background: linear-gradient(117deg, rgba(2, 0, 36, 1) 0%, rgba(125, 129, 150, 0.9757503221014968) 96%, rgba(222, 199, 45, 1) 98%, rgba(142, 49, 242, 0.5859143339953169) 98%);
            border-radius: 1.25rem;
            max-width: 800px;
        }
        .message {
            padding: 1em;
            margin-bottom: 1em;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 0.5em;
            font-weight: bold;
        }
        input, select {
            margin-bottom: 1em;
            padding: 0.5em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 0.75em;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .preview-container {
            margin-bottom: 1em;
        }
        .preview-container img {
            max-width: 100%;
            height: auto;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Affichage des messages de session
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            echo "<div class='message {$message['type']}'>{$message['text']}</div>";
            unset($_SESSION['message']);
        }
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="nom_article">Nom de l'article:</label>
            <input type="text" id="nom_article" name="nom_article" value="<?php echo htmlspecialchars($article['nom_article']); ?>" required>

            <label for="id_categorie">Catégorie:</label>
            <select id="id_categorie" name="id_categorie" required>
                <?php
                $categories = getCategorie();
                foreach ($categories as $categorie) {
                    $selected = ($categorie['id'] == $article['id_categorie']) ? 'selected' : '';
                    echo "<option value='{$categorie['id']}' $selected>{$categorie['libelle_categorie']}</option>";
                }
                ?>
            </select>

            <label for="quantite">Quantité:</label>
            <input type="number" id="quantite" name="quantite" value="<?php echo htmlspecialchars($article['quantite']); ?>" required>

            <label for="prix_unitaire">Prix Unitaire:</label>
            <input type="number" step="0.01" id="prix_unitaire" name="prix_unitaire" value="<?php echo htmlspecialchars($article['prix_unitaire']); ?>" required>

            <label for="date_fabrication">Date de Fabrication:</label>
            <input type="date" id="date_fabrication" name="date_fabrication" value="<?php echo htmlspecialchars($article['date_fabrication']); ?>" required>

            <label for="date_expiration">Date d'Expiration:</label>
            <input type="date" id="date_expiration" name="date_expiration" value="<?php echo htmlspecialchars($article['date_expiration']); ?>" required>

            <label for="nom_fournisseur">Nom du Fournisseur:</label>
            <input type="text" id="nom_fournisseur" name="nom_fournisseur" value="<?php echo htmlspecialchars($article['nom_fournisseur']); ?>" required>

            <label for="images">Image:</label>
            <input type="file" id="images" name="images" accept="image/*">

            <!-- Zone de prévisualisation de l'image -->
            <div class="preview-container">
                <img id="preview" src="" alt="Prévisualisation de l'image">
            </div>

            <button type="submit">Enregistrer</button>
        </form>
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
