<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi d'Expédition</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Ajouter la feuille de style de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

.expedition-section {
    margin-bottom: 40px;
}

h2 {
    font-size: 1.5em;
    margin-bottom: 10px;
}

form label {
    display: block;
    margin-bottom: 5px;
}

form input,
form select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button[type="submit"] {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table th,
table td {
    padding: 10px;
    border: 1px solid #ccc;
}

table th {
    background-color: #f0f0f0;
    font-weight: bold;
    text-align: left;
}

table td {
    text-align: left;
}

</style>
<body>
<div class="container">
    <h1>Suivi d'Expédition</h1>
    <main>
        <!-- Section pour créer une Expédition -->
        <div class="expedition-section">
            <h2>Créer une Expédition</h2>
            <form id="expeditionForm" action="#" method="POST">
                <label for="client"><i class="fas fa-user"></i> Sélection du Client:</label>
                <select name="client" id="client">
                    <!-- Options de clients à insérer ici -->
                    <option value="client1">Client 1</option>
                    <option value="client2">Client 2</option>
                </select>
                <br>
                <label for="produit"><i class="fas fa-box"></i> Produits Expédiés:</label>
                <?php
                // Connexion à la base de données
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "gestion_stock";
                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Échec de la connexion : " . $conn->connect_error);
                }

                // Requête SQL pour récupérer les produits depuis la base de données
                $sql = "SELECT nom FROM produit";
                $result = $conn->query($sql);

                // Vérifier s'il y a des produits disponibles
                if ($result && $result->num_rows > 0) {
                    echo "<select name='produit' id='produit'>";
                    // Afficher chaque produit comme une option dans le menu déroulant
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['nom'] . "'>" . $row['nom'] . "</option>";
                    }
                    echo "</select>";
                } else {
                    echo "Aucun produit trouvé";
                }

                $conn->close();
                ?>
                <br>
                <label for="quantite"><i class="fas fa-sort-numeric-up"></i> Quantité:</label>
                <input type="number" name="quantite" id="quantite" placeholder="Quantité">
                <br>
                <label for="date"><i class="fas fa-calendar-alt"></i> Date d'Expédition:</label>
                <input type="date" name="date" id="date">
                <br>
                <div class="expedition-section">
                    <label for="statut">Statut :</label>
                    <select name="statut" id="statut">
                        <option value="En cours">En cours</option>
                        <option value="Expédiée">Expédiée</option>
                    </select>
                </div>
                <button type="button" onclick="submitForm()"><i class="fas fa-check"></i> Expédier</button>
            </form>
            <div id="successMessage" style="display: none; color: green; margin-top: 10px;">Expédition enregistrée avec succès.</div>
        </div>

        <!-- Liens -->
        <a href="liste_expedition.php">Voir l'historique des expéditions</a>

    </main>
</div>

<script>
    function submitForm() {
        // Envoyer une requête AJAX pour soumettre le formulaire sans recharger la page
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "creer_expedition.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Afficher le message de succès
                document.getElementById("successMessage").style.display = "block";
                // Réinitialiser le formulaire
                document.getElementById("expeditionForm").reset();
                // Cacher le message de succès après 3 secondes
                setTimeout(function() {
                    document.getElementById("successMessage").style.display = "none";
                }, 3000);
            }
        };
        // Récupérer les données du formulaire
        var formData = new FormData(document.getElementById("expeditionForm"));
        // Envoyer les données du formulaire
        xhr.send(formData);
    }
</script>
</body>
</html>
