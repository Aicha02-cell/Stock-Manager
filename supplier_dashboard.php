<?php
session_start();

// Vérification si l'utilisateur est bien un administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supplier') {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Fournisseurs - Gestion de Stock</title>
    <link rel="stylesheet" href="styl.css"> <!-- Assurez-vous d'ajouter le bon lien vers votre fichier CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
body {
    background-image: url("https://st3.depositphotos.com/1103339/13209/v/1600/depositphotos_132095056-stock-illustration-abstract-seamless-pattern-in-postmodern.jpg");
    margin: 0; /* Ajout pour supprimer la marge par défaut */
}

div {
    margin: 2em 5em 3em 5em;
    padding: 2em 6em 2em 6em;
    background: rgb(2, 0, 36);
    background: linear-gradient(117deg, rgba(2, 0, 36, 1) 0%, rgba(125, 129, 150, 0.9757503221014968) 96%, rgba(222, 199, 45, 1) 98%, rgba(222, 199, 45, 1) 98%, rgba(142, 49, 242, 0.5859143339953169) 98%);
    color: red;
    font-family: cursive;
    border-radius: 1.25rem;
}

#container_1 {
    text-align: center;
}

#container_2 {
    display: block;
}

.form {
    width: 100%;
    margin-bottom: 1em;
}

#submit {
    display: block;
    width: 100%;
    padding: 0.75rem;
    background: rgb(2, 0, 36);
    color: inherit;
    border-radius: 15px;
    cursor: pointer;
}

textarea {
    width: 100%;
}

h1{
    color:#ddd;
}
a {
    color:#ddd;
}
.main {
    list-style: none;
    padding: 0;
}

.main a {
    display: block;
    padding: 15px;
    text-decoration: none;
    color: #ddd;
    transition: background-color 0.3s ease;
}

.main a:hover {
    background-color: #007bff;
}

.main a i {
    margin-right: 10px;
}

.main a span {
    vertical-align: middle;
}

</style>
<body>
    <div class="dashboard-container">
        <header>
            <div class="welcome">
                <h1>Bienvenue, Fournisseur <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        
            
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </header>
        <main>
    <nav>
        <ul class="main">
           
            <a href="commande.php"><i class="fas fa-clipboard-check"></i> <span>Suivi de Commandes</span></a>
            <a href="vente.php"><i class="fas fa-shipping-fast"></i> <span>Suivi de ventes</span></a>
        </ul>
    </nav>
</main>

    </div>
</body>
</html>
