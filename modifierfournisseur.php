<?php
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

// Initialiser les variables
$id = '';
$nom = '';
$contact = '';
$adresse = '';
$email = '';

// Vérifier si l'ID du fournisseur est passé en paramètre GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer les informations du fournisseur depuis la base de données
    $stmt = $conn->prepare("SELECT nom, contact, adresse, email FROM fournisseurs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Récupérer les données du fournisseur
        $row = $result->fetch_assoc();
        $nom = $row['nom'];
        $contact = $row['contact'];
        $adresse = $row['adresse'];
        $email = $row['email'];
    } else {
        echo "Aucun fournisseur trouvé avec cet identifiant.";
        exit();
    }

    $stmt->close();
}

// Traiter les soumissions de formulaire pour la mise à jour
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
    $contact = isset($_POST['contact']) ? $_POST['contact'] : '';
    $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    // Mise à jour du fournisseur dans la base de données
    $stmt_update = $conn->prepare("UPDATE fournisseurs SET nom=?, contact=?, adresse=?, email=? WHERE id=?");
    $stmt_update->bind_param("ssssi", $nom, $contact, $adresse, $email, $id);

    if ($stmt_update->execute()) {
        echo "<p>Fournisseur mis à jour avec succès.</p>";
        // Rediriger vers la page de liste des fournisseurs après la mise à jour (optionnel)
        // header("Location: affichagefournisseur.php");
        // exit();
    } else {
        echo "<p>Erreur lors de la mise à jour du fournisseur: " . $stmt_update->error . "</p>";
    }

    $stmt_update->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Fournisseur</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Modifier Fournisseur</h1>

    <h2><a href="affichagefournisseur.php">Retour</a></h2>

    <form method="post" action="">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label for="nom">Nom :</label><br>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>"><br>

        <label for="contact">Contact :</label><br>
        <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($contact) ?>"><br>

        <label for="adresse">Adresse :</label><br>
        <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($adresse) ?>"><br>

        <label for="email">Email :</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>"><br><br>

        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>
