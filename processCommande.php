<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'] ?? null;
        $id_article = $_POST['id_article'];
        $quantite = $_POST['quantite'];
        $prix = $_POST['prix'];
        $date_commande = $_POST['date_commande'];
       
        
        // Informations client
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse = $_POST['adresse'];
        $telephone= $_POST['telephone'];

        // Récupérer la quantité en stock de l'article
        $stmt_article = $pdo->prepare("SELECT quantite FROM article WHERE id = :id");
        $stmt_article->execute(['id' => $id_article]);
        $article = $stmt_article->fetch(PDO::FETCH_ASSOC);
        $quantite_en_stock = $article['quantite'];

        // Déterminer le statut de la commande
        if ($quantite > $quantite_en_stock) {
            $statut = 'rupture'; // Rupture de stock
        } else {
            $statut = 'en attente'; // En attente
        }

        if (!empty($id)) {
            // Mise à jour de la commande existante
            $stmt = $pdo->prepare("UPDATE commande SET id_article = :id_article, quantite = :quantite, prix = :prix, date_commande = :date_commande, statut = :statut,  nom = :nom, prenom = :prenom, adresse = :adresse, telephone = :telephone WHERE id = :id");
            $stmt->execute([
                'id_article' => $id_article,
                'quantite' => $quantite,
                'prix' => $prix,
                'date_commande' => $date_commande,
                'statut' => $statut,
                
                'nom' => $nom,
                'prenom' => $prenom,
                'adresse' => $adresse,
                'telephone' => $telephone,
                'id' => $id
            ]);
        } else {
            // Insertion d'une nouvelle commande
            $stmt = $pdo->prepare("INSERT INTO commande (id_article, quantite, prix, date_commande, statut, nom, prenom, adresse, telephone) VALUES (:id_article, :quantite, :prix, :date_commande, :statut, :nom, :prenom, :adresse, :telephone)");
            $stmt->execute([
                'id_article' => $id_article,
                'quantite' => $quantite,
                'prix' => $prix,
                'date_commande' => $date_commande,
                'statut' => $statut,
                'nom' => $nom,
                'prenom' => $prenom,
                'adresse' => $adresse,
                'telephone' => $telephone
            ]);
        }

        // Réduire la quantité en stock si la commande est confirmée
        if ($statut != 'rupture') {
            $nouveau_quantite_en_stock = $quantite_en_stock - $quantite;
            $stmt_update_stock = $pdo->prepare("UPDATE article SET quantite = :quantite WHERE id = :id");
            $stmt_update_stock->execute([
                'quantite' => $nouveau_quantite_en_stock,
                'id' => $id_article
            ]);
        } elseif ($statut == 'rupture') {
            // Vous pouvez ajouter une gestion spécifique pour les ruptures de stock si nécessaire
            $nouveau_quantite_en_stock = $quantite_en_stock - $quantite;
            $stmt_update_stock = $pdo->prepare("UPDATE article SET quantite = :quantite WHERE id = :id");
            $stmt_update_stock->execute([
                'quantite' => $nouveau_quantite_en_stock,
                'id' => $id_article]);
        }

        // Rediriger vers une page de confirmation ou de liste des commandes
        header("Location: confirmation.php");
        exit();
    }

    // Code pour annuler une commande
    if (isset($_GET['annuler_id'])) {
        $id = $_GET['annuler_id'];

        // Récupérer la commande pour obtenir la quantité et l'article
        $stmt = $pdo->prepare("SELECT id_article, quantite FROM commande WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($commande) {
            $id_article = $commande['id_article'];
            $quantite = $commande['quantite'];

            // Rétablir la quantité en stock
            $stmt_article = $pdo->prepare("UPDATE article SET quantite = quantite - :quantite WHERE id = :id");
            $stmt_article->execute([
                'quantite' => $quantite,
                'id' => $id_article
            ]);

            // Mettre à jour le statut de la commande
            $stmt_update = $pdo->prepare("UPDATE commande SET statut = 'annulee' WHERE id = :id");
            $stmt_update->execute(['id' => $id]);

            // Rediriger vers la liste des commandes
            header("Location: commande.php");
            exit();
        }
    }

} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
