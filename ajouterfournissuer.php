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

// Traitement du formulaire d'ajout ou de modification du fournisseur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action == 'add') {
        $nom = $_POST['nom'];
        $contact = $_POST['contact'];
        $adresse = $_POST['adresse'];
        $email = $_POST['email'];
    }

        // Insertion du fournisseur dans la table `fournisseurs`
        $stmt = $conn->prepare("INSERT INTO fournisseurs (nom, contact, adresse, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nom, $contact, $adresse, $email);

        if ($stmt->execute()) {
            $fournisseur_id = $stmt->insert_id;

            // Préparation de la requête d'insertion des associations fournisseur-article
            $stmt_association = $conn->prepare("INSERT INTO fournisseur_article (id_fournisseur, id_article) VALUES (?, ?)");

            if ($stmt_association === false) {
                die('Erreur de préparation de la requête SQL: ' . $conn->error);
            }

            // Insertion des associations fournisseur-article pour chaque article sélectionné
            foreach ($articles as $article_id) {
                // Assurez-vous que $fournisseur_id et $article_id sont des entiers
                $fournisseur_id = intval($fournisseur_id);
                $article_id = intval($article_id);

                $stmt_association->bind_param("ii", $fournisseur_id, $article_id);

                if ($stmt_association->execute() === false) {
                    die('Erreur lors de l\'exécution de la requête SQL: ' . $stmt_association->error);
                }
            }

            // Fermeture du statement d'insertion des associations
            $stmt_association->close();
        } else {
            die('Erreur lors de l\'exécution de la requête SQL: ' . $stmt->error);
        }

        // Fermeture du statement d'insertion du fournisseur
        $stmt->close();
    }

header("Location: fournisseur.php");
// Fermeture de la connexion
$conn->close();
?>
