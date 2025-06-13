<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traiter les soumissions de formulaires pour la suppression (sur la même page)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    if ($id) {
        // Suppression du fournisseur
        $stmt_delete_fournisseur = $conn->prepare("DELETE FROM fournisseurs WHERE id=?");
        $stmt_delete_fournisseur->bind_param("i", $id);

        if ($stmt_delete_fournisseur->execute()) {
            // Suppression des associations fournisseur-article (s'il y en a)
            $stmt_delete_associations = $conn->prepare("DELETE FROM fournisseur_article WHERE id_fournisseur=?");
            $stmt_delete_associations->bind_param("i", $id);
            $stmt_delete_associations->execute();

            // Rafraîchissement de la page après la suppression (optionnel)
            // header("Location: affichagefournisseur.php");
            // exit();
        } else {
            echo "<p>Erreur lors de la suppression du fournisseur: " . $stmt_delete_fournisseur->error . "</p>";
        }

        $stmt_delete_fournisseur->close();
        $stmt_delete_associations->close();
    } else {
        echo "<p>L'identifiant du fournisseur est manquant.</p>";
    }
}

// Traiter la recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Livreurs</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function confirmDelete(event, form) {
            event.preventDefault(); // Empêche la soumission du formulaire
            if (confirm('Voulez-vous vraiment supprimer ce fournisseur ?')) {
                form.submit(); // Soumet le formulaire si l'utilisateur confirme
            }
        }
    </script>
</head>
<body>
    <h1>Liste des livreurs </h1>

    <h2><a href="fournisseur.php">Retour</a></h2>

    <h2>Rechercher un livreurs</h2>
    <form method="get" action="">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Rechercher...">
        <button type="submit">Rechercher</button>
    </form>

    <table border="1">
        <tr>
            <th>Nom</th>
            <th>Contact</th>
            <th>Adresse</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        // Afficher la liste des fournisseurs
        $sql = "SELECT f.id, f.nom, f.contact, f.adresse, f.email, GROUP_CONCAT(a.nom_article SEPARATOR ', ') AS articles_livres
                FROM fournisseurs f
                LEFT JOIN fournisseur_article fa ON f.id = fa.id_fournisseur
                LEFT JOIN article a ON fa.id_article = a.id";

        if (!empty($search)) {
            $search = $conn->real_escape_string($search);
            $sql .= " WHERE f.nom LIKE '%$search%' OR f.contact LIKE '%$search%' OR f.adresse LIKE '%$search%' OR f.email LIKE '%$search%' OR a.nom_article LIKE '%$search%'";
        }

        $sql .= " GROUP BY f.id";

        $result = $conn->query($sql);

        if ($result === false) {
            echo "<tr><td colspan='6'>Erreur lors de l'exécution de la requête : " . $conn->error . "</td></tr>";
        } elseif ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['nom']}</td>
                        <td>{$row['contact']}</td>
                        <td>{$row['adresse']}</td>
                        <td>{$row['email']}</td>
                       
                        <td>
                            <a href='modifierfournisseur.php?id={$row['id']}'>Modifier</a>
                            <form method='post' action='' onsubmit='confirmDelete(event, this)'>
                                <input type='hidden' name='action' value='delete'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <button type='submit'>Supprimer</button>
                            </form>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Aucun fournisseur trouvé.</td></tr>";
        }

        $conn->close();
        ?>
    </table>
</body>
</html>
