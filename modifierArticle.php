<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_article = $_POST['id_article'];
    $nom_article = $_POST['nom_article'];
    $id_categorie = $_POST['id_categorie'];
    $quantite = $_POST['quantite'];
    $prix_unitaire = $_POST['prix_unitaire'];
    $date_fabrication = $_POST['date_fabrication'];
    $date_expiration = $_POST['date_expiration'];

    // Récupération du chemin de l'image actuelle
    $sql_old_image = "SELECT images FROM article WHERE id=?";
    $stmt_old_image = $conn->prepare($sql_old_image);
    $stmt_old_image->bind_param("i", $id_article);
    $stmt_old_image->execute();
    $stmt_old_image->bind_result($old_image);
    $stmt_old_image->fetch();
    $stmt_old_image->close();

    // Gestion de la nouvelle image
    $new_image_uploaded = false;
    $target_file = $old_image; // Utiliser l'image actuelle par défaut

    // Vérification si une nouvelle image a été téléchargée
    if ($_FILES["images"]["error"] === UPLOAD_ERR_OK) {
        $target_dir = "public/images/";
        $target_file = $target_dir . basename($_FILES["images"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérification si le fichier est une image réelle
        $check = getimagesize($_FILES["images"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['message'] = array(
                'type' => 'error',
                'text' => 'Le fichier téléchargé n\'est pas une image valide.'
            );
            $uploadOk = 0;
        }

        // Vérification de la taille du fichier
        if ($_FILES["images"]["size"] > 500000) {
            $_SESSION['message'] = array(
                'type' => 'error',
                'text' => 'Désolé, votre fichier est trop volumineux.'
            );
            $uploadOk = 0;
        }

        // Autorisation des extensions de fichier
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_extensions)) {
            $_SESSION['message'] = array(
                'type' => 'error',
                'text' => 'Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.'
            );
            $uploadOk = 0;
        }

        // Si tout est correct, déplacer le fichier téléchargé
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["images"]["tmp_name"], $target_file)) {
                $new_image_uploaded = true;
            } else {
                $_SESSION['message'] = array(
                    'type' => 'error',
                    'text' => 'Une erreur s\'est produite lors du téléchargement du fichier.'
                );
            }
        }
    } else {
        // Aucune nouvelle image téléchargée
        $new_image_uploaded = false;
    }

    // Préparer et exécuter la mise à jour de l'article
    if ($new_image_uploaded) {
        $sql_update = "UPDATE article SET nom_article=?, id_categorie=?, quantite=?, prix_unitaire=?, date_fabrication=?, date_expiration=?, images=? WHERE id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("siiisssi", $nom_article, $id_categorie, $quantite, $prix_unitaire, $date_fabrication, $date_expiration, $target_file, $id_article);
    } else {
        $sql_update = "UPDATE article SET nom_article=?, id_categorie=?, quantite=?, prix_unitaire=?, date_fabrication=?, date_expiration=? WHERE id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("siiissi", $nom_article, $id_categorie, $quantite, $prix_unitaire, $date_fabrication, $date_expiration, $id_article);
    }

    if ($stmt->execute()) {
        // Supprimer l'ancienne image si une nouvelle image a été téléchargée et mise à jour
        if ($new_image_uploaded && !empty($old_image) && $old_image != $target_file) {
            if (file_exists($old_image)) {
                unlink($old_image); // Suppression de l'ancien fichier image
            }
        }

        $_SESSION['message'] = array(
            'type' => 'success',
            'text' => 'Article modifié avec succès.'
        );
    } else {
        $_SESSION['message'] = array(
            'type' => 'error',
            'text' => 'Erreur lors de la modification de l\'article : ' . $conn->error
        );
    }

    $stmt->close();
    $conn->close();

    // Redirection vers la liste des produits
    header("Location: liste_Produit.php");
    exit();
}

// Récupérer l'identifiant de l'article depuis l'URL
if (!isset($_GET['id_article']) || !is_numeric($_GET['id_article'])) {
    $_SESSION['message'] = array(
        'type' => 'error',
        'text' => 'Identifiant de l\'article non valide.'
    );
    header("Location: liste_Produit.php");
    exit();
}

$id_article = $_GET['id_article'];

// Récupérer les détails de l'article depuis la base de données
$sql_select = "SELECT * FROM article WHERE id=?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $id_article);
$stmt_select->execute();
$result_select = $stmt_select->get_result();

if ($result_select->num_rows > 0) {
    $row = $result_select->fetch_assoc();
} else {
    $_SESSION['message'] = array(
        'type' => 'error',
        'text' => 'Aucun article trouvé avec cet identifiant.'
    );
    header("Location: liste_Produit.php");
    exit();
}

$stmt_select->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Article</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
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
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
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
        <h1>Modifier Article</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_article" value="<?php echo htmlspecialchars($row['id']); ?>">
            <label for="nom_article">Nom de l'article:</label>
            <input type="text" id="nom_article" name="nom_article" value="<?php echo htmlspecialchars($row['nom_article']); ?>" required>

            <label for="id_categorie">Catégorie:</label>
            <select id="id_categorie" name="id_categorie" required>
                <?php
                $categories = getCategorie();
                foreach ($categories as $categorie) {
                    $selected = ($categorie['id'] == $row['id_categorie']) ? 'selected' : '';
                    echo "<option value='{$categorie['id']}' $selected>{$categorie['libelle_categorie']}</option>";
                }
                ?>
            </select>

            <label for="quantite">Quantité:</label>
            <input type="number" id="quantite" name="quantite" value="<?php echo htmlspecialchars($row['quantite']); ?>" required>

            <label for="prix_unitaire">Prix Unitaire:</label>
            <input type="number" step="0.01" id="prix_unitaire" name="prix_unitaire" value="<?php echo htmlspecialchars($row['prix_unitaire']); ?>" required>

            <label for="date_fabrication">Date de Fabrication:</label>
            <input type="date" id="date_fabrication" name="date_fabrication" value="<?php echo htmlspecialchars($row['date_fabrication']); ?>" required>

            <label for="date_expiration">Date d'Expiration:</label>
            <input type="date" id="date_expiration" name="date_expiration" value="<?php echo htmlspecialchars($row['date_expiration']); ?>" required>

            <label for="images">Image:</label>
            <input type="file" id="images" name="images">
            <?php if (!empty($row['images'])): ?>
                <p>Image actuelle : <img src="<?php echo htmlspecialchars($row['images']); ?>" alt="Image de l'article" style="max-width: 200px; max-height: 200px;"></p>
            <?php endif; ?>

            <button type="submit">Enregistrer</button>
        </form>
    </div>
</body>
</html>
