<?php
// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $fournisseurId = $_POST['fournisseur'];
    $articleId = $_POST['nom_article'];
    $quantite = $_POST['quantite'];
    $dateReception = $_POST['date_reception'];
    $statut = $_POST['statut'];

    // Validation des données (ajoutez des validations supplémentaires selon vos besoins)

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestion_stock";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Début de la transaction
    $conn->begin_transaction();

    try {
        // Préparer et exécuter la requête d'insertion sécurisée pour la table `reception`
        $sqlReception = "INSERT INTO reception (fournisseur_id, date_reception, statut) VALUES (?, ?, ?)";
        $stmtReception = $conn->prepare($sqlReception);
        $stmtReception->bind_param("iss", $fournisseurId, $dateReception, $statut);

        // Exécuter la requête pour la table `reception`
        $stmtReception->execute();
        $receptionId = $stmtReception->insert_id; // Récupérer l'ID de la réception insérée
        $stmtReception->close();

        // Préparer et exécuter la requête d'insertion sécurisée pour la table `detail_reception`
        $sqlDetail = "INSERT INTO detail_reception (reception_id, article_id, quantite) VALUES (?, ?, ?)";
        $stmtDetail = $conn->prepare($sqlDetail);
        $stmtDetail->bind_param("iii", $receptionId, $articleId, $quantite);

        // Exécuter la requête pour la table `detail_reception`
        $stmtDetail->execute();
        $stmtDetail->close();

        // Mettre à jour la quantité du produit dans la table `article`
        $sqlUpdateArticle = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
        $stmtUpdateArticle = $conn->prepare($sqlUpdateArticle);
        $stmtUpdateArticle->bind_param("ii", $quantite, $articleId);

        // Exécuter la mise à jour de la quantité
        $stmtUpdateArticle->execute();
        $stmtUpdateArticle->close();

        // Valider la transaction
        $conn->commit();

        // Redirection vers la page d'historique après enregistrement réussi
        header("Location: historique.php");
        exit;
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction et afficher l'erreur
        $conn->rollback();
        echo "Erreur lors de l'ajout des détails de réception: " . $e->getMessage();
    }

    // Fermer la connexion
    $conn->close();
}
?>
