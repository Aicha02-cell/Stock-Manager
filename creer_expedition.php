<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $client = $_POST['client'];
    $produit = $_POST['produit'];
    $quantite = $_POST['quantite'];
    $date = $_POST['date'];
    $statut = $_POST['statut']; // Ajout du champ 'statut' récupéré du formulaire

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestion_stock";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Préparer et exécuter la requête SQL pour insérer les données d'expédition dans la table d'expédition
    $sql = "INSERT INTO expedition (client, produit, quantite, date_exp, statut) 
            VALUES ('$client', '$produit', '$quantite', '$date', '$statut')";

    if ($conn->query($sql) === TRUE) {
        echo "Expédition enregistrée avec succès.";
    } else {
        echo "Erreur lors de l'enregistrement de l'expédition : " . $conn->error;
    }

    // Fermer la connexion à la base de données
    $conn->close();
}
?>
