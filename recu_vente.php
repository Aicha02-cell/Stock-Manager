<?php
// Inclusion de l'en-tête commun (entete.php)


// Fonction pour récupérer les détails d'une vente
function getVente($id_vente, $pdo) {
    try {
        // Préparation de la requête SQL pour récupérer les détails de la vente
        $stmt = $pdo->prepare("SELECT v.*, a.nom_article, cl.nom AS client_nom, cl.prenom, cl.telephone, cl.adresse
                               FROM ventes v
                               LEFT JOIN article a ON v.id_article = a.id
                               LEFT JOIN clients cl ON v.id_client = cl.id
                               WHERE v.id = ?");
        $stmt->execute([$id_vente]);
        $vente = $stmt->fetch(PDO::FETCH_ASSOC);

        return $vente; // Retourner les détails de la vente
    } catch (PDOException $e) {
        echo "Erreur SQL : " . $e->getMessage(); // Afficher l'erreur SQL pour débogage
    }

    return null; // Retourner null si la vente n'est pas trouvée
}

// Vérifier si l'ID de la vente est présent dans l'URL
if (!empty($_GET['id'])) {
    $id_vente = $_GET['id'];

    // Connexion à la base de données (à adapter selon votre configuration)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestion_stock"; // Remplacez par le nom de votre base de données

    try {
        // Connexion à la base de données avec PDO
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les détails de la vente
        $vente = getVente($id_vente, $pdo);

        if (!$vente) {
            die("Vente non trouvée.");
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage(); // Afficher toute autre erreur PDO
    }
}

?>

<div class="home-content">
    <button class="hidden-print" id="btnPrint" style="position: relative; left: 45%;"><i class='bx bx-printer'></i> Imprimer</button>

    <div class="page">
        <div class="cote-a-cote">
            <h2>IMPRESSION</h2>
            <div>
                <p>Reçu N° #: <?= $vente['id'] ?> </p>
                <p>Date: <?= date('d/m/Y H:i:s', strtotime($vente['date_vente'])) ?> </p>
            </div>
        </div>

        <div class="cote-a-cote" style="width: 50%;">
            <p>Nom du client :</p>
            <p><?= $vente['client_nom'] . " " . $vente['prenom'] ?></p>
        </div>
        <div class="cote-a-cote" style="width: 50%;">
            <p>Tel :</p>
            <p><?= $vente['telephone'] ?></p>
        </div>
        <div class="cote-a-cote" style="width: 50%;">
            <p>Adresse :</p>
            <p><?= $vente['adresse'] ?></p>
        </div>

        <br>

        <table class="mtable">
            <tr>
                <th>Designation</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Prix total</th>
            </tr>

            <tr>
                <td><?= $vente['nom_article'] ?></td>
                <td><?= $vente['quantite'] ?></td>
                <td><?= $vente['prix_unitaire'] ?></td>
                <td><?= $vente['prix'] ?></td>
            </tr>

        </table>
    </div>
</div>

<?php


// Traitement pour générer le PDF si le bouton "Imprimer" est cliqué
if (isset($_POST['imprimer'])) {
    // Récupérer l'ID de la vente depuis le formulaire POST
    $id_vente = $_POST['id_vente'];

    // Récupérer les détails de la vente depuis la base de données
    $vente = getVente($id_vente, $pdo);

    if (!$vente) {
        die("Vente non trouvée.");
    }

    // Définir le type de contenu du fichier
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="recu_vente_' . $id_vente . '.pdf"');

    // Générer le contenu du PDF
    ob_start(); // Démarrer la capture de sortie

    // Contenu du PDF
    ?>
    <h1>Récépissé de vente</h1>
    <p>Date et heure: <?= date('d/m/Y H:i:s') ?></p>
    <p>Réçu numéro: <?= uniqid('RECU') ?></p>
    <p><strong>Nom du client:</strong> <?= $vente['client_nom'] . ' ' . $vente['prenom'] ?></p>
    <p><strong>Téléphone:</strong> <?= $vente['telephone'] ?></p>
    <p><strong>Adresse:</strong> <?= $vente['adresse'] ?></p>
    <hr>
    <p><strong>Article:</strong> <?= $vente['nom_article'] ?></p>
    <p><strong>Quantité:</strong> <?= $vente['quantite'] ?></p>
    <p><strong>Prix unitaire:</strong> <?= $vente['prix_unitaire'] ?></p>
    <p><strong>Prix total:</strong> <?= $vente['prix'] ?></p>
    <?php

    $content = ob_get_clean(); // Récupérer le contenu de la capture et arrêter la capture

    // Convertir le contenu HTML en PDF
    require_once('chemin/vers/mpdf/mpdf.php'); // Inclure la bibliothèque mPDF
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($content);

    // Sortie du PDF
    $mpdf->Output();

    exit(); // Terminer le script après la génération du PDF
}
?>

<script>
    var btnPrint = document.querySelector('#btnPrint');
    btnPrint.addEventListener("click", () => {
        window.print();
    });
</script>
