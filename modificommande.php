<?php
// Inclusion de la connexion à la base de données et des fonctions nécessaires
include 'config2.php'; // Assurez-vous que ce fichier contienne votre logique de connexion à la base de données

// Vérification des données postées
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $id = $_POST['id'];
    $id_article = $_POST['id_article'];
    $id_fournisseur = $_POST['id_fournisseur'];
    $quantite = $_POST['quantite'];
    $prix = $_POST['prix'];

    // Validation éventuelle des données (à implémenter selon vos besoins)

    // Mise à jour dans la base de données
    $sql = "UPDATE commande SET id_article=?, id_fournisseur=?, quantite=?, prix=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_article, $id_fournisseur, $quantite, $prix, $id]);

    // Redirection avec un message de succès
    $_SESSION['message'] = [
        'text' => 'La commande a été modifiée avec succès.',
        'type' => 'success'
    ];
    header('Location: ../liste_commande.php');
    exit();
} else {
    // Redirection en cas d'accès direct à ce script sans méthode POST
    header('Location: ../liste_commande.php');
    exit();
}
?>
