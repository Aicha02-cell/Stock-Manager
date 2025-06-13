<?php
include 'database.php';

$action = $_GET['action'];

if ($action == 'create') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $identifiant = $_POST['identifiant'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (nom, prenom, identifiant, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $identifiant, $mot_de_passe, $role]);
    echo "Utilisateur ajouté avec succès";
} elseif ($action == 'read') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
        $stmt = $conn->query("SELECT * FROM users");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} elseif ($action == 'update') {
    $id = $_POST['userId'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $identifiant = $_POST['identifiant'];
    $role = $_POST['role'];

    if (!empty($_POST['mot_de_passe'])) {
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET nom = ?, prenom = ?, identifiant = ?, mot_de_passe = ?, role = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $identifiant, $mot_de_passe, $role, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nom = ?, prenom = ?, identifiant = ?, role = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $identifiant, $role, $id]);
    }
    echo "Utilisateur modifié avec succès";
} elseif ($action == 'delete') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    echo "Utilisateur supprimé avec succès";
}
?>
