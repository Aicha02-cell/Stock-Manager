<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=gestion_stock', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

session_start();

// Fonction pour récupérer toutes les ventes depuis la base de données
function getVentes() {
    global $pdo;
    $stmt = $pdo->query("SELECT v.*, a.nom_article, c.nom, c.prenom FROM ventes v
                         INNER JOIN article a ON v.id_article = a.id
                         INNER JOIN clients c ON v.id_client = c.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Annuler une vente
if (isset($_GET['action']) && $_GET['action'] === 'annuler' && isset($_GET['idVente'])) {
    $idVente = $_GET['idVente'];
    // Code pour annuler la vente (à implémenter selon vos besoins)
    // Exemple : mettre à jour le statut de la vente dans la base de données
    $_SESSION['message'] = [
        'type' => 'info',
        'text' => 'Vente annulée avec succès.'
    ];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Filtrage des ventes par recherche
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $pdo->prepare("SELECT v.*, a.nom_article, c.nom, c.prenom FROM ventes v
                           INNER JOIN article a ON v.id_article = a.id
                           INNER JOIN clients c ON v.id_client = c.id
                           WHERE a.nom_article LIKE :search OR c.nom LIKE :search OR c.prenom LIKE :search");
    $stmt->execute(['search' => "%$search%"]);
    $ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $ventes = getVentes();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des ventes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Liste des ventes</h1>

        <!-- Barre de recherche -->
        <form action="ventes.php" method="get" class="search-form">
            <input type="text" name="search" placeholder="Rechercher par article ou client...">
            <button type="submit">Rechercher</button>
        </form>

        <!-- Liste des ventes -->
        <div class="ventes-list">
            <table>
                <tr>
                    <th>Article</th>
                    <th>Client</th>
                    <th>Quantité</th>
                    <th>Prix</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($ventes as $vente): ?>
                    <tr>
                        <td><?= $vente['nom_article'] ?></td>
                        <td><?= $vente['nom'] . " " . $vente['prenom'] ?></td>
                        <td><?= $vente['quantite'] ?></td>
                        <td><?= $vente['prix'] ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($vente['date_vente'])) ?></td>
                        <td>
                        <a href='recu_vente.php?id={$ventes['id']}' target='_blank'>Imprimer</a> | 
                            <a href="?action=annuler&idVente=<?= $vente['id'] ?>" onclick="return confirm('Voulez-vous vraiment annuler cette vente ?');">Annuler</a>
                        
                    </tr>
                    </td>
                        <a href="ventes.php">Retour</a>
                        </div>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
