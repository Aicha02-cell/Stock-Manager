<?php
// Vérifier si le formulaire de réapprovisionnement a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['article_id'])) {
    // Récupérer l'identifiant de l'article à réapprovisionner depuis les données POST
    $article_id = $_POST['article_id'];

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestion_stock"; // Remplacez par le nom de votre base de données

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Début de la transaction
        $pdo->beginTransaction();

        // Récupérer la quantité actuelle en stock pour l'article
        $stmt = $pdo->prepare("SELECT quantite FROM article WHERE id = :article_id FOR UPDATE");
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $quantiteActuelle = $row['quantite'];

        // Mettre à jour la quantité en stock (exemple : ajouter 100 unités)
        $nouvelleQuantite = $quantiteActuelle + 100; // Exemple, à adapter selon votre logique de réapprovisionnement
        $stmt = $pdo->prepare("UPDATE article SET quantite = :nouvelle_quantite WHERE id = :article_id");
        $stmt->bindParam(':nouvelle_quantite', $nouvelleQuantite, PDO::PARAM_INT);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute();

        // Mettre à jour le statut des commandes en attente pour cet article
        $stmt = $pdo->prepare("UPDATE commande SET statut = 'acceptee' WHERE id_article = :article_id AND statut = 'attente'");
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute();

        // Commit de la transaction
        $pdo->commit();

        // Redirection vers la page principale des commandes avec un message de succès
        header("Location: commande.php?message=Stock réapprovisionné avec succès");
        exit();

    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction et afficher un message d'erreur
        $pdo->rollBack();
        echo "Erreur de réapprovisionnement : " . $e->getMessage();
    }

    // Fermeture de la connexion PDO
    $pdo = null;
} else {
    // Redirection si la page est accédée directement sans données POST
    header("Location: commande.php");
    exit();
}
?>
