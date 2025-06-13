<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une réception</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assurez-vous d'avoir un fichier style.css pour vos styles -->
</head>
<body>
    <h1>Créer une réception</h1>
    <form id="formReception" action="enregistrer_reception.php" method="post">
        <label for="fournisseur">Fournisseur :</label>
        <select id="fournisseur" name="fournisseur" required>
            <!-- Les options de fournisseurs seront chargées ici -->
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

            // Récupérer les fournisseurs depuis la base de données
            $sql = "SELECT id, nom FROM fournisseurs";
            $result = $conn->query($sql);

            // Vérifier s'il y a des fournisseurs
            if ($result->num_rows > 0) {
                // Afficher chaque fournisseur comme une option dans le formulaire
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["id"] . "'>" . $row["nom"] . "</option>";
                }
            } else {
                echo "<option value=''>Aucun fournisseur disponible</option>";
            }

            // Fermer la connexion
            $conn->close();
            ?>
        </select><br>

        <label for="nom_article">Article :</label>
        <select id="nom_article" name="nom_article" required>
            <!-- Les options d'articles seront chargées ici -->
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

            // Récupérer les articles depuis la base de données
            $sql = "SELECT id, nom_article FROM article";
            $result = $conn->query($sql);

            // Vérifier s'il y a des articles
            if ($result->num_rows > 0) {
                // Afficher chaque article comme une option dans le formulaire
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["id"] . "'>" . $row["nom_article"] . "</option>";
                }
            } else {
                echo "<option value=''>Aucun article disponible</option>";
            }

            // Fermer la connexion
            $conn->close();
            ?>
        </select><br>

        <label for="quantite">Quantité :</label>
        <input type="number" id="quantite" name="quantite" min="1" required><br>

        <label for="date_reception">Date de réception :</label>
        <input type="date" id="date_reception" name="date_reception" required><br>

        <label for="statut">Statut :</label>
        <select id="statut" name="statut" required>
            <option value="en attente">En attente</option>
            <option value="reçue">Reçue</option>
        </select><br>

        <button type="submit">Enregistrer Réception</button>
    </form>

    <a href="historique.php">Voir l'historique des réceptions</a>

</body>
</html>
