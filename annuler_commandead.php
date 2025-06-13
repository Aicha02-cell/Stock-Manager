<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock"; // Remplacez par le nom de votre base de données

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérification du paramètre ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        die("Identifiant de commande non spécifié.");
    }

    $commandeId = $_GET['id'];

    // Préparation de la requête SQL pour supprimer la commande dans la table commande
    $sql = "DELETE FROM commande WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $commandeId, PDO::PARAM_INT);
    $stmt->execute();

    // Redirection vers la page précédente après suppression
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();

} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// Fermeture de la connexion PDO
$pdo = null;
?>
