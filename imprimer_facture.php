<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock"; // Remplacez par le nom de votre base de données

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérification de l'ID de commande à imprimer
    $commandeIdToPrint = isset($_GET['id']) ? $_GET['id'] : null;

    // Requête SQL pour récupérer la commande spécifique
    if ($commandeIdToPrint) {
        $sql = "SELECT c.id AS commande_id, a.nom_article, f.nom AS fournisseur_nom, cl.nom AS client_nom, cl.prenom AS client_prenom, cl.adresse AS client_adresse, cl.telephone AS client_telephone, c.quantite, c.prix_total, c.statut
                FROM commandes c
                LEFT JOIN article a ON c.id_article = a.id
                LEFT JOIN fournisseurs f ON c.id_fournisseur = f.id
                LEFT JOIN clients cl ON c.id_client = cl.id
                WHERE c.id = :commandeId";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':commandeId', $commandeIdToPrint, PDO::PARAM_INT);
        $stmt->execute();

        // Vérification si la commande spécifiée existe
        if ($stmt->rowCount() === 0) {
            die("Commande non trouvée.");
        }

        // Début du contenu HTML pour l'impression de la commande spécifique
        $content = "
            <!DOCTYPE html>
            <html lang='fr'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Impression de Commande</title>
                <!-- Bootstrap CSS CDN -->
                <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
                <style>
                    .hidden-print {
                        display: inline-block;
                        background-color: #007bff;
                        color: #fff;
                        padding: 8px 16px;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        margin-top: 20px;
                    }
    
                    .hidden-print:hover {
                        background-color: #0056b3;
                    }
                </style>
            </head>
            <body>
                <div class='container mt-4'>
        ";

        // Affichage des détails de la commande spécifique
        while ($commande = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $commandeId = $commande['commande_id'];
            $nom_article = $commande['nom_article'];
            $fournisseur_nom = $commande['fournisseur_nom'];
            $client_nom = $commande['client_nom'];
            $client_prenom = $commande['client_prenom'];
            $client_adresse = $commande['client_adresse'];
            $client_telephone = $commande['client_telephone'];
            $quantite = $commande['quantite'];
            $prix_total = $commande['prix_total'];
            $statut = $commande['statut'];

            // Contenu de la commande à imprimer
            $content .= "
                <div class='card'>
                    <div class='card-header'>
                        <h2>Commande #{$commandeId}</h2>
                    </div>
                    <div class='card-body'>
                        <p><strong>Nom de l'Article:</strong> {$nom_article}</p>
                        <p><strong>Fournisseur:</strong> {$fournisseur_nom}</p>
                        <p><strong>Nom du Client:</strong> {$client_nom} {$client_prenom}</p>
                        <p><strong>Adresse:</strong> {$client_adresse}</p>
                        <p><strong>Numéro de Téléphone:</strong> {$client_telephone}</p>
                        <p><strong>Quantité:</strong> {$quantite}</p>
                        <p><strong>Prix Total:</strong> {$prix_total}</p>
                        <p><strong>Statut:</strong> {$statut}</p>
                        <hr>
                        <button class='btn btn-primary hidden-print' onclick='printCommande();'>Imprimer cette commande</button>
                    </div>
                    <div class='card-footer'>
                        <p><em>Signature Automatique</em></p>
                        <button class='btn btn-primary hidden-print' onclick='window.print();'>Imprimer la Commande</button>
                    </div>
                </div>
            ";
        }

        // Fin du contenu HTML
        $content .= "
                </div>
                <!-- Bootstrap JS CDN -->
                <script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
                <script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js'></script>
                <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
                <script>
                    function printCommande() {
                        window.print();
                    }
                </script>
            </body>
            </html>
        ";

        // Affichage du contenu de la commande spécifique
        echo $content;

    } else {
        // Redirection vers une page d'erreur si aucun ID n'est spécifié
        header("Location: erreur.php");
        exit();
    }

} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

// Fermeture de la connexion PDO
$pdo = null;
?>
