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

// Vérification si un ID de commande est passé en paramètre GET
if (isset($_GET['id'])) {
    $id_commande = $_GET['id'];

    try {
        // Récupération des détails de la commande à modifier
        $stmt = $pdo->prepare("SELECT * FROM commande WHERE id = :id");
        $stmt->execute(['id' => $id_commande]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);

        // Récupération de tous les articles pour le formulaire de sélection
        $stmt_articles = $pdo->query("SELECT id, nom_article FROM article");
        $articles = $stmt_articles->fetchAll();

        // Récupération de tous les fournisseurs pour le formulaire de sélection
        $stmt_fournisseurs = $pdo->query("SELECT id, nom FROM fournisseurs");
        $fournisseurs = $stmt_fournisseurs->fetchAll();

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Traitement du formulaire si POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $id_commande = $_POST['id_commande'];
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $date_commande = $_POST['date_commande'];
    $id_article = $_POST['id_article'];
    $id_fournisseur = $_POST['id_fournisseur'];
    $quantite = $_POST['quantite'];
    $prix = $_POST['prix'];

    try {
        // Mise à jour des données dans la table commande
        $stmt_update = $pdo->prepare("UPDATE commande SET prenom = :prenom, nom = :nom, adresse = :adresse, 
                                      telephone = :telephone, date_commande = :date_commande, 
                                      id_article = :id_article, id_fournisseur = :id_fournisseur, 
                                      quantite = :quantite, prix = :prix WHERE id = :id_commande");
        $stmt_update->execute([
            'prenom' => $prenom,
            'nom' => $nom,
            'adresse' => $adresse,
            'telephone' => $telephone,
            'date_commande' => $date_commande,
            'id_article' => $id_article,
            'id_fournisseur' => $id_fournisseur,
            'quantite' => $quantite,
            'prix' => $prix,
            'id_commande' => $id_commande
        ]);

        // Redirection vers la page de confirmation après modification
        header("Location: confirmation_commande.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Modifier Commande</title>
    <style>
        /* Vos styles CSS ici */
    </style>
</head>
<body>
<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            <h2>Modifier Commande</h2>
            <form action="modifier_commande.php" method="post">
                <input type="hidden" name="id_commande" value="<?= $commande['id'] ?>">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" value="<?= $commande['prenom'] ?>" required>
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?= $commande['nom'] ?>" required>
                <label for="adresse">Adresse :</label>
                <input type="text" id="adresse" name="adresse" value="<?= $commande['adresse'] ?>" required>
                <label for="telephone">Téléphone :</label>
                <input type="text" id="telephone" name="telephone" value="<?= $commande['telephone'] ?>" required>
                <label for="date_commande">Date de Commande :</label>
                <input type="date" id="date_commande" name="date_commande" value="<?= $commande['date_commande'] ?>" required>
                <label for="id_article">Article :</label>
                <select id="id_article" name="id_article" required>
                    <?php foreach ($articles as $article): ?>
                        <option value="<?= $article['id'] ?>" <?= ($article['id'] == $commande['id_article']) ? 'selected' : '' ?>>
                            <?= $article['nom_article'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="id_fournisseur">Fournisseur :</label>
                <select id="id_fournisseur" name="id_fournisseur" required>
                    <?php foreach ($fournisseurs as $fournisseur): ?>
                        <option value="<?= $fournisseur['id'] ?>" <?= ($fournisseur['id'] == $commande['id_fournisseur']) ? 'selected' : '' ?>>
                            <?= $fournisseur['nom'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="quantite">Quantité :</label>
                <input type="number" id="quantite" name="quantite" value="<?= $commande['quantite'] ?>" required>
                <label for="prix">Prix :</label>
                <input type="text" id="prix" name="prix" value="<?= $commande['prix'] ?>" required>
                <button type="submit" class="button">Modifier Commande</button>
            </form>
            <a href="confirmation_commande.php" class="button">Retour à la liste des commandes</a>
        </div>
    </div>
</div>

</body>
</html>
