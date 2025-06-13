<?php
session_start();

// Vérifier si l'identifiant du produit à supprimer est passé en paramètre
if(isset($_GET['id']) && !empty($_GET['id'])) {
    // Récupérer l'identifiant du produit à supprimer
    $id_produit = $_GET['id'];

    // Connexion à la base de données
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=gestion_stock', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bdd->exec('SET NAMES utf8');
    } catch (PDOException $e) {
        echo 'Erreur SQL : ' . $e->getMessage();
    }

    // Préparation de la requête de suppression
    $stmt = $bdd->prepare("DELETE FROM produit WHERE id_produit = ?");
    
    // Exécution de la requête avec l'identifiant du produit à supprimer
    $stmt->execute([$id_produit]);

    // Affichage d'un message JavaScript pour indiquer que le produit a été supprimé
    echo "<script>alert('Le produit a été supprimé avec succès');</script>";
}

// Redirection vers la page d'origine (index.php) après 1 seconde
header("refresh:1;url=liste_Produit.php");
exit();
?>
