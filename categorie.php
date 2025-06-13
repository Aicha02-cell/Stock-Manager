<?php
session_start(); // Démarrer la session pour gérer les messages d'alerte

$servername = "localhost"; // Nom du serveur MySQL
$username = "root"; // Nom d'utilisateur MySQL
$password = ""; // Mot de passe MySQL
$dbname = "gestion_stock"; // Nom de la base de données

// Créer une connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fonction pour récupérer une catégorie par son ID depuis la base de données
function getCategorie($conn, $id) {
    $sql = "SELECT * FROM categories WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Fonction pour ajouter ou mettre à jour une catégorie dans la base de données
function saveCategorie($conn, $data) {
    $libelle_categorie = $conn->real_escape_string($data['libelle_categorie']); // Échapper les caractères spéciaux
    if (!empty($data['id'])) {
        // Mettre à jour une catégorie existante
        $id = $data['id'];
        $sql = "UPDATE categories SET libelle_categorie = '$libelle_categorie' WHERE id = $id";
    } else {
        // Ajouter une nouvelle catégorie
        $sql = "INSERT INTO categories (libelle_categorie) VALUES ('$libelle_categorie')";
    }

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => !empty($data['id']) ? 'Catégorie mise à jour avec succès.' : 'Nouvelle catégorie ajoutée avec succès.'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'Erreur lors de la sauvegarde de la catégorie : ' . $conn->error
        ];
    }
}

// Fonction pour supprimer une catégorie de la base de données
function deleteCategorie($conn, $id) {
    $sql = "DELETE FROM categories WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Catégorie supprimée avec succès.'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'Erreur lors de la suppression de la catégorie : ' . $conn->error
        ];
    }
}

// Gestion de la soumission du formulaire de suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    deleteCategorie($conn, $id);
    header('Location: categorie.php');
    exit;
}

// Gestion de la soumission du formulaire d'ajout/modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['libelle_categorie'])) {
        // Appel à la fonction pour sauvegarder la catégorie
        saveCategorie($conn, $_POST);

        // Redirection vers la même page après l'action
        header('Location: categorie.php');
        exit;
    } else {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'Veuillez saisir un libellé pour la catégorie.'
        ];
    }
}

// Récupérer une catégorie si un ID est passé en paramètre GET
$categorie = null;
if (!empty($_GET['id'])) {
    $categorie = getCategorie($conn, $_GET['id']);
}

// Récupérer toutes les catégories depuis la base de données
$categories = [];
$sql = "SELECT id, libelle_categorie FROM categories";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des catégories</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <!-- Bouton retour -->
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Retour</a>

        <!-- Formulaire d'ajout/modification de catégorie -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><?= !empty($categorie) ? 'Modifier la catégorie' : 'Ajouter une catégorie' ?></h4>
                    </div>
                    <div class="card-body">
                        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                            <div class="form-group">
                                <label for="libelle_categorie">Libellé</label>
                                <input type="text" name="libelle_categorie" id="libelle_categorie" class="form-control" placeholder="Veuillez saisir le libellé" value="<?= !empty($categorie) ? htmlspecialchars($categorie['libelle_categorie']) : "" ?>">
                                <input type="hidden" name="id" value="<?= !empty($categorie) ? $categorie['id'] : "" ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Valider</button>
                        </form>

                        <?php
                        // Affichage du message d'alerte s'il y en a un en session
                        if (!empty($_SESSION['message']['text'])) {
                            $messageClass = $_SESSION['message']['type'] == 'error' ? 'alert-danger' : 'alert-success';
                            echo "<div class='alert $messageClass mt-3'>{$_SESSION['message']['text']}</div>";
                            unset($_SESSION['message']); // Effacer le message après l'avoir affiché
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table des catégories -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Liste des catégories</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Libellé catégorie</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $value): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($value['libelle_categorie']) ?></td>
                                        <td>
                                            <a href="modifier_categorie.php?id=<?= $value['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                            <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                                <input type="hidden" name="id" value="<?= $value['id'] ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Aucune catégorie trouvée.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
