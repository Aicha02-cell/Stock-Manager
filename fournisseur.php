<?php
// Fonction pour récupérer la liste des articles depuis la base de données
function getArticles($conn) {
    $stmt = $conn->query("SELECT id, nom_article FROM article");

    if ($stmt === false) {
        die('Erreur de récupération des articles: ' . $conn->error);
    }

    $articles = [];
    while ($row = $stmt->fetch_assoc()) {
        $articles[] = $row;
    }

    $stmt->close();

    return $articles;
}

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

// Traiter les soumissions de formulaires
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... Votre code de traitement des formulaires ici ...
}

// Charger les données du fournisseur à modifier
$fournisseur = null;
$fournisseur_articles = [];
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Préparer la requête pour récupérer les données du fournisseur
    $stmt = $conn->prepare("SELECT * FROM fournisseurs WHERE id=?");
    if ($stmt === false) {
        die('Erreur de préparation de la requête SQL: ' . $conn->error);
    }
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $fournisseur = $result->fetch_assoc();
    } else {
        die('Erreur lors de l\'exécution de la requête SQL: ' . $stmt->error);
    }
    $stmt->close();

    // Récupérer les articles que le fournisseur peut livrer
    $stmt_fournisseur_articles = $conn->prepare("SELECT id_article FROM fournisseur_article WHERE id_fournisseur=?");
    if ($stmt_fournisseur_articles === false) {
        die('Erreur de préparation de la requête SQL: ' . $conn->error);
    }
    $stmt_fournisseur_articles->bind_param("i", $id);
    if ($stmt_fournisseur_articles->execute()) {
        $result_fournisseur_articles = $stmt_fournisseur_articles->get_result();
        while ($row = $result_fournisseur_articles->fetch_assoc()) {
            $fournisseur_articles[] = $row['id_article'];
        }
    } else {
        die('Erreur lors de l\'exécution de la requête SQL: ' . $stmt_fournisseur_articles->error);
    }
    $stmt_fournisseur_articles->close();
}

// Récupérer la liste des articles via la fonction définie
$articles = getArticles($conn);

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livreurs</title>
    <link rel="stylesheet" href="custom.css">
</head>
<body>
    <h1>Gestion des Livreurs</h1>
    <a href="admin_dashboard.php" class="redirect-button">RETOUR</a>
    <form action="ajouterfournissuer.php" method="post">
        <input type="hidden" name="id" id="id" value="<?php echo $fournisseur ? $fournisseur['id'] : ''; ?>">
        <label for="nom">Nom:</label>
        <input type="text" name="nom" id="nom" value="<?php echo $fournisseur ? $fournisseur['nom'] : ''; ?>" required><br>
        <label for="contact">Contact:</label>
        <input type="text" name="contact" id="contact" value="<?php echo $fournisseur ? $fournisseur['contact'] : ''; ?>"><br>
        <label for="adresse">Adresse:</label>
        <textarea name="adresse" id="adresse"><?php echo $fournisseur ? $fournisseur['adresse'] : ''; ?></textarea><br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $fournisseur ? $fournisseur['email'] : ''; ?>"><br>

        <button type="submit" name="action" value="add">Ajouter</button>
      
    </form>

    <h2><a href="affichagefournisseur.php">Voir la liste des Livreurs</a></h2>
</body>
</html>
