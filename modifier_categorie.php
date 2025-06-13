<?php
session_start(); // Démarrer la session pour gérer les messages d'alerte

$servername = "localhost"; // Nom du serveur MySQL
$username = "root"; // Nom d'utilisateur MySQL
$password = ""; // Mot de passe MySQL
$dbname = "gestion_stock"; // Nom de la base de données

// Créer une connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fonction pour récupérer une catégorie par son ID depuis la base de données
function getCategorie($conn, $id) {
    $sql = "SELECT * FROM categories WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Vérifier si un ID de catégorie est passé en paramètre GET
if (!empty($_GET['id'])) {
    $categorie = getCategorie($conn, $_GET['id']);
    if (!$categorie) {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'Catégorie non trouvée.'
        ];
        header('Location: categorie.php');
        exit;
    }
} else {
    $_SESSION['message'] = [
        'type' => 'error',
        'text' => 'ID de catégorie non spécifié.'
    ];
    header('Location: categorie.php');
    exit;
}

// Gestion de la soumission du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['libelle_categorie'])) {
        $id = $_POST['id'];
        $libelle_categorie = $conn->real_escape_string($_POST['libelle_categorie']);
        $sql = "UPDATE categories SET libelle_categorie = '$libelle_categorie' WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => 'Catégorie mise à jour avec succès.'
            ];
        } else {
            $_SESSION['message'] = [
                'type' => 'error',
                'text' => 'Erreur lors de la mise à jour de la catégorie : ' . $conn->error
            ];
        }

        // Redirection vers la page des catégories après modification
        header('Location: categorie.php');
        exit;
    } else {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'Veuillez saisir un libellé pour la catégorie.'
        ];
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une catégorie</title>
    <style>
        /* Styles CSS pour l'exemple */
        form {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            margin: 20px auto;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #4CAF50;
            color: white;
        }
        .error {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <label for="libelle_categorie">Libellé</label>
        <input value="<?= !empty($categorie) ? htmlspecialchars($categorie['libelle_categorie']) : "" ?>" type="text" name="libelle_categorie" id="libelle_categorie" placeholder="Veuillez saisir le libellé">
        <input value="<?= !empty($categorie) ? $categorie['id'] : "" ?>" type="hidden" name="id" id="id">
        <button type="submit">Valider</button>
    </form>
    <?php
    // Affichage du message d'alerte s'il y en a un en session
    if (!empty($_SESSION['message']['text'])) {
        $messageClass = $_SESSION['message']['type'] == 'error' ? 'error' : 'success';
        echo "<div class='message $messageClass'>{$_SESSION['message']['text']}</div>";
        unset($_SESSION['message']); // Effacer le message après l'avoir affiché
    }
    ?>
</body>
</html>
