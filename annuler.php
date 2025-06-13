<?php
// Récupérer l'ID de la commande depuis l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de commande non valide.");
}

$commandeId = $_GET['id'];

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock"; // Remplacez par le nom de votre base de données

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mettre à jour le statut de la commande à "annulee"
    $sql = "UPDATE commandes SET statut = 'annulee' WHERE id = :commande_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':commande_id', $commandeId, PDO::PARAM_INT);
    $stmt->execute();

    // Redirection vers la liste des commandes après annulation
    header("Location: index.php");
    exit();

} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

// Fermeture de la connexion PDO
$pdo = null;
?>
