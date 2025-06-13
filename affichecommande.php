<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des données de la commande
if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT c.*, a.nom_article FROM commandes c LEFT JOIN article a ON c.id_article = a.id WHERE c.id = :id");
    $stmt->execute(['id' => $_GET['id']]);
    $commande = $stmt->fetch();
} else {
    die("Aucune commande spécifiée.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Détails de la commande</title>
</head>
<body>

<div class="commande-details">
    <h2>Détails de la commande</h2>
    <div>
        <p><strong>Prénom:</strong> <?= $commande['prenom'] ?></p>
        <p><strong>Nom:</strong> <?= $commande['nom'] ?></p>
        <p><strong>Adresse:</strong> <?= $commande['adresse'] ?></p>
        <p><strong>Téléphone:</strong> <?= $commande['telephone'] ?></p>
        <p><strong>Date de commande:</strong> <?= $commande['date_commande'] ?></p>
        <p><strong>Article:</strong> <?= $commande['nom_article'] ?></p>
        <p><strong>Quantité:</strong> <?= $commande['quantite'] ?></p>
        <p><strong>Prix:</strong> <?= $commande['prix'] ?></p>
    </div>
    <div>
        <a href="commande.php?id=<?= $commande['id'] ?>">Modifier</a>
        <a href="annuleCommande.php?id=<?= $commande['id'] ?>" onclick="return confirm('Voulez-vous vraiment annuler cette commande ?')">Annuler</a>
    </div>
</div>

</body>
</html>
