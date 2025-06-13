<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérification de la requête POST et récupération de l'ID de la réception
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $receptionId = $_POST['id'];

    // Requête SQL pour mettre à jour le statut de la réception
    $sql = "UPDATE reception SET statut = 'reçu' WHERE id = $receptionId";

    if ($conn->query($sql) === TRUE) {
        echo "Le statut de la réception a été mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du statut de la réception : " . $conn->error;
    }
} else {
    echo "Erreur : Aucun ID de réception fourni.";
}

$conn->close();
?>
