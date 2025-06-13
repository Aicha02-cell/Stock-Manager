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

// Traitement de l'annulation de commande si un ID est passé en paramètre GET
if (isset($_GET['action']) && $_GET['action'] == 'annuler' && isset($_GET['id'])) {
    $id_commande = $_GET['id'];
    try {
        // Début de la transaction pour assurer l'intégrité des données
        $pdo->beginTransaction();

        // Récupération des détails de la commande avant annulation pour ajuster les quantités
        $stmt_commande = $pdo->prepare("SELECT * FROM commande WHERE id = :id");
        $stmt_commande->execute(['id' => $id_commande]);
        $commande = $stmt_commande->fetch(PDO::FETCH_ASSOC);

        if (!$commande) {
            throw new Exception("La commande avec l'ID $id_commande n'existe pas.");
        }

        // Récupération de la quantité commandée pour ajuster l'inventaire
        $quantite_commandee = $commande['quantite'];
        $id_article = $commande['id_article'];

        // Suppression de la commande de la table commande
        $stmt_supprimer_commande = $pdo->prepare("DELETE FROM commande WHERE id = :id");
        $stmt_supprimer_commande->execute(['id' => $id_commande]);

        // Récupération de la quantité actuelle de l'article
        $stmt_article = $pdo->prepare("SELECT quantite FROM article WHERE id = :id FOR UPDATE");
        $stmt_article->execute(['id' => $id_article]);
        $article = $stmt_article->fetch(PDO::FETCH_ASSOC);
        if (!$article) {
            throw new Exception("L'article avec l'ID $id_article n'existe pas.");
        }

        $quantite_actuelle = $article['quantite'];

        // Mise à jour de la quantité disponible de l'article
        $nouvelle_quantite = $quantite_actuelle + $quantite_commandee;
        $stmt_update_article = $pdo->prepare("UPDATE article SET quantite = :quantite WHERE id = :id");
        $stmt_update_article->execute(['quantite' => $nouvelle_quantite, 'id' => $id_article]);

        // Validation de la transaction
        $pdo->commit();

        // Redirection vers la page actuelle pour actualiser la liste des commandes
        header("Location: confirmation.php");
        exit();
    } catch (PDOException $e) {
        // En cas d'erreur, annulation de la transaction pour éviter des modifications invalides
        $pdo->rollback();
        echo "Erreur : " . $e->getMessage();
    } catch (Exception $e) {
        // Gestion des exceptions
        echo "Erreur : " . $e->getMessage();
    }
}

// Récupération des commandes depuis la base de données (après annulation si nécessaire)
$stmt = $pdo->query("SELECT c.*, a.nom_article
                    FROM commande c 
                    INNER JOIN article a ON c.id_article = a.id");
                   
$commandes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Confirmation de Commande</title>
    <style>
        /* Vos styles CSS ici */
    </style>
</head>
<body>
<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            <h2>Confirmation de Commande</h2>
            <?php if (!empty($commandes)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Adresse</th>
                            <th>Téléphone</th>
                            <th>Date de Commande</th>
                            <th>Article</th>
                            
                            <th>Quantité</th>
                            <th>Prix</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $commande): ?>
                            <tr>
                                <td><?= $commande['id'] ?></td>
                                <td><?= $commande['prenom'] ?></td>
                                <td><?= $commande['nom'] ?></td>
                                <td><?= $commande['adresse'] ?></td>
                                <td><?= $commande['telephone'] ?></td>
                                <td><?= $commande['date_commande'] ?></td>
                                <td><?= $commande['nom_article'] ?></td>
                                <td><?= $commande['quantite'] ?></td>
                                <td><?= $commande['prix'] ?></td>
                                <td>
                                    <a href="modifier_commande.php?id=<?= $commande['id'] ?>" class="button">Modifier</a>
                                    <a href="confirmation_commande.php?action=annuler&id=<?= $commande['id'] ?>" class="button">Annuler</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune commande n'a été trouvée.</p>
            <?php endif; ?>
            <a href="commande.php" class="button">Retour au formulaire de commande</a>
        </div>
    </div>
</div>

</body>
</html>
