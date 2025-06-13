<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

$message = "";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérification si un ID de commande est passé en paramètre GET
if (!empty($_GET['id'])) {
    // Suppression de la commande de la base de données
    $stmt = $pdo->prepare("DELETE FROM commande WHERE id = :id");
    $stmt->execute(['id' => $_GET['id']]);
    $message = "Commande annulée avec succès.";
} else {
    $message = "ID de commande non spécifié.";
}

// Redirection vers la liste des commandes après l'annulation
header("Location: confirmation.php");
exit();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuler commande</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Fonction pour confirmer l'annulation de la commande
        function confirmAnnulation() {
            if (confirm("Voulez-vous vraiment annuler cette commande ?")) {
                return true;
            } else {
                // Annulation de la redirection si l'utilisateur annule
                window.location.href = "confirmation.php";
                return false;
            }
        }
    </script>
</head>
<body>
    <div class="confirmation-content">
        <h2>Annuler commande</h2>
        <p><?= htmlspecialchars($message) ?></p>
        <!-- Bouton de confirmation avec appel à la fonction JavaScript -->
        <button onclick="return confirmAnnulation()">Confirmer l'annulation</button>
        <!-- Lien pour revenir à la liste des commandes -->
        <a href="confirmation.php">Retour à la liste des commandes</a>
    </div>
</body>
</html>
