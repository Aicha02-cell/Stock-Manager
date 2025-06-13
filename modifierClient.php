<?php

include 'config2.php'; // Fichier de configuration de la base de données

// Vérification si l'ID du client est passé en paramètre GET
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Requête pour récupérer les informations du client spécifié par son ID
    $sql = "SELECT * FROM clients WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        // Si aucun client correspondant à l'ID n'est trouvé, rediriger vers la liste des clients
        $_SESSION['message'] = array('text' => 'Client non trouvé.', 'type' => 'error');
        header("Location: client.php");
        exit();
    }
} else {
    // Si l'ID du client n'est pas spécifié dans les paramètres GET, rediriger vers la liste des clients
    $_SESSION['message'] = array('text' => 'ID du client non spécifié.', 'type' => 'error');
    header("Location: listeClients.php");
    exit();
}

// Traitement du formulaire de modification du client
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
  

    // Requête SQL pour mettre à jour les informations du client
    $sqlUpdate = "UPDATE clients SET nom = :nom, prenom = :prenom, telephone = :telephone, adresse = :adresse WHERE id= :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindValue(':nom', $nom);
    $stmtUpdate->bindValue(':prenom', $prenom);
    $stmtUpdate->bindValue(':telephone', $telephone);
    $stmtUpdate->bindValue(':adresse', $adresse);

    $stmtUpdate->bindValue(':id', $id);

    if ($stmtUpdate->execute()) {
        $_SESSION['message'] = array('text' => 'Client modifié avec succès.', 'type' => 'success');
    } else {
        $_SESSION['message'] = array('text' => 'Erreur lors de la modification du client.', 'type' => 'error');
    }

    // Redirection vers la liste des clients après traitement du formulaire
    header("Location: client.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Client</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Modifier Client</h1>

        <!-- Formulaire de modification du client pré-rempli avec les informations actuelles -->
        <form method="post" action="">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($client['nom']) ?>" required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($client['prenom']) ?>" required>

            <label for="telephone">Téléphone</label>
            <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($client['telephone']) ?>" required>

            <label for="adresse">Adresse</label>
            <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($client['adresse']) ?>" required>

            

            <button type="submit">Modifier</button>
        </form>

        <!-- Lien de retour vers la liste des clients -->
        <div>
            <a href="client.php">Retour à la liste des clients</a>
        </div>
    </div>
</body>
</html>
