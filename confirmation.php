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

// Récupération des commandes depuis la base de données
$stmt_commandes = $pdo->query("SELECT * FROM commande");
$commandes = $stmt_commandes->fetchAll(PDO::FETCH_ASSOC);

// Traitement de l'acceptation d'une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accepter'])) {
    $id_commande = $_POST['id_commande'];

    // Vérification des quantités en stock
    $stmt_commande_articles = $pdo->prepare("
        SELECT a.id_article, a.quantite_stock, ca.quantite 
        FROM commande_article ca 
        JOIN article a ON ca.id_article = a.id 
        WHERE ca.id_commande = :id_commande
    ");
    $stmt_commande_articles->execute(['id_commande' => $id_commande]);
    $commande_articles = $stmt_commande_articles->fetchAll(PDO::FETCH_ASSOC);

    $update_articles = [];
    $commande_validée = true;

    foreach ($commande_articles as $commande_article) {
        $article_id = $commande_article['id_article'];
        $quantite_stock = $commande_article['quantite_stock'];
        $quantite_commande = $commande_article['quantite'];

        // Si la quantité commandée dépasse le stock disponible
        if ($quantite_commande > $quantite_stock) {
            $update_articles[] = [
                'id_article' => $article_id,
                'statut' => 'Rupture'
            ];
            $commande_validée = false;
        } else {
            $update_articles[] = [
                'id_article' => $article_id,
                'statut' => 'Disponible'
            ];
        }
    }

    if ($commande_validée) {
        // Mise à jour du statut de la commande
        $stmt = $pdo->prepare("UPDATE commande SET statut = 'Acceptée', date_acceptation = NOW() WHERE id = :id_commande");
        $stmt->execute(['id_commande' => $id_commande]);

        // Mise à jour du statut des articles et diminution des quantités en stock
        foreach ($update_articles as $update_article) {
            $stmt_article_update = $pdo->prepare("
                UPDATE article 
                SET statut = :statut, quantite_stock = quantite_stock - (
                    SELECT quantite FROM commande_article 
                    WHERE id_commande = :id_commande AND id_article = :id_article
                )
                WHERE id = :id_article
            ");
            $stmt_article_update->execute([
                'statut' => $update_article['statut'],
                'id_commande' => $id_commande,
                'id_article' => $update_article['id_article']
            ]);
        }

        echo "<script>alert('Commande acceptée et mise à jour des articles !');</script>";
    } else {
        echo "<script>alert('Commande non acceptée : certains articles sont en rupture de stock.');</script>";
    }
}

// Traitement de la livraison d'une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livrer'])) {
    $id_commande = $_POST['id_commande'];
    $stmt = $pdo->prepare("UPDATE commande SET statut = 'Livrée', date_livraison = NOW() WHERE id = :id_commande");
    $stmt->execute(['id_commande' => $id_commande]);

    echo "<script>alert('Commande livrée !');</script>";
}
?>
