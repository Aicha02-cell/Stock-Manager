<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Supposons que ces variables viennent d'un formulaire ou d'une autre source
    $id_article = $_POST['id_article'];
    $id_fournisseur = $_POST['id_fournisseur'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $quantite = $_POST['quantite'];
    $prix = $_POST['prix'];
    $date_commande = date('Y-m-d H:i:s'); // Date actuelle
    $date_livraison = date('Y-m-d H:i:s', strtotime('+48 hours')); // Livraison prévue dans 48 heures

    // Vérifier la quantité en stock
    $stmt = $pdo->prepare("SELECT quantite FROM article WHERE id = :id_article");
    $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
    $stmt->execute();
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        throw new Exception("Article non trouvé.");
    }

    $quantite_en_stock = $article['quantite'];

    // Déterminer le statut
    if ($quantite > $quantite_en_stock) {
        $statut = 'rupture';
    } else {
        $statut = 'en attente';
    }

    // Démarrer une transaction
    $pdo->beginTransaction();

    // Insérer la commande
    $stmt = $pdo->prepare("INSERT INTO commande (id_article, id_fournisseur, nom, prenom, adresse, telephone, quantite, prix, date_commande, statut, date_livraison) 
                           VALUES (:id_article, :id_fournisseur, :nom, :prenom, :adresse, :telephone, :quantite, :prix, :date_commande, :statut, :date_livraison)");

    $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
    $stmt->bindParam(':id_fournisseur', $id_fournisseur, PDO::PARAM_INT);
    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $stmt->bindParam(':adresse', $adresse, PDO::PARAM_STR);
    $stmt->bindParam(':telephone', $telephone, PDO::PARAM_STR);
    $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);
    $stmt->bindParam(':prix', $prix, PDO::PARAM_STR);
    $stmt->bindParam(':date_commande', $date_commande, PDO::PARAM_STR);
    $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
    $stmt->bindParam(':date_livraison', $date_livraison, PDO::PARAM_STR);

    $stmt->execute();

    // Mettre à jour la quantité en stock
    $nouvelle_quantite = $quantite_en_stock - $quantite;
    $stmt = $pdo->prepare("UPDATE article SET quantite = :quantite WHERE id = :id_article");
    $stmt->bindParam(':quantite', $nouvelle_quantite, PDO::PARAM_INT);
    $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
    $stmt->execute();

    // Committer la transaction
    $pdo->commit();

    echo "Commande ajoutée avec succès.";

} catch (PDOException $e) {
    // Annuler la transaction en cas d'erreur
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erreur : " . $e->getMessage();
} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erreur : " . $e->getMessage();
}
header('Location: commande.php');
$pdo = null;
?>

 