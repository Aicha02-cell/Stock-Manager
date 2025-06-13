<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur - Gestion de Stock</title>
    <link rel="stylesheet" href="adm.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Bienvenue, Administrateur <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <nav>
            <ul>
                <li><a href="Produit.php"><i class="fas fa-box"></i> Articles</a></li>
                <li><a href="categorie.php"><i class="fas fa-truck"></i> Categorie</a></li>
                <li><a href="fournisseur.php"><i class="fas fa-truck"></i> Livreur</a></li>
                <li><a href="liste_commadmin.php"><i class="fas fa-receipt"></i> Commande</a></li>
                <li><a href="mouvement.php"><i class="fas fa-exchange-alt"></i> Gestion des mouvements de stocks</a></li>
                <li><a href="rapport.php"><i class="fas fa-chart-line"></i> Génération de rapports</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
