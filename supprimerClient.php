<?php

include 'config2.php'; // Fichier de configuration de la base de données

// Vérification si l'ID du client est passé en paramètre GET
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Requête pour supprimer le client spécifié par son ID
    $sql = "DELETE FROM clients WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = array('text' => 'Client supprimé avec succès.', 'type' => 'success');
    } else {
        $_SESSION['message'] = array('text' => 'Erreur lors de la suppression du client.', 'type' => 'error');
    }
} else {
    // Si l'ID du client n'est pas spécifié dans les paramètres GET, rediriger vers la liste des clients
    $_SESSION['message'] = array('text' => 'ID du client non spécifié.', 'type' => 'error');
}

// Redirection vers la liste des clients après suppression
header("Location: Client.php");
exit();
?>
