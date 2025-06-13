<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Utilisateur</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Ajouter un Utilisateur</h1>
    <form id="userForm">
        <input type="hidden" id="userId" name="userId">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" required><br>
        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" required><br>
        <label for="identifiant">Identifiant:</label>
        <input type="text" id="identifiant" name="identifiant" required><br>
        <label for="mot_de_passe">Mot de passe:</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required><br>
        <label for="role">Rôle:</label>
        <select id="role" name="role" required><br>
            <option value="administrateur">Administrateur</option>
            <option value="opérateur">Opérateur</option>
        </select><br>
        <button type="submit">Enregistrer</button>
    </form>
    <a href="liste.php">Voir la liste des utilisateurs</a>
    <script src="scripts.js"></script>
</body>

</html>