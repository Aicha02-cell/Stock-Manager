<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_stock";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des articles depuis la base de données
$stmt_articles = $pdo->query("SELECT * FROM article");
$articles = $stmt_articles->fetchAll(PDO::FETCH_ASSOC);

// Définir le numéro de contact pour le virement
$numero_contact = "+22375678790";

// Traitement du formulaire de commande
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'commander') {
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $telephone = $_POST['telephone'];
        $date_commande = $_POST['date_commande'] ?? date('Y-m-d');

        // Validation des données du formulaire
        if (empty($prenom) || empty($nom) || empty($adresse) || empty($telephone)) {
            $_SESSION['message'] = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (empty($_SESSION['panier'])) {
            $_SESSION['message'] = 'Veuillez ajouter au moins un article à la commande.';
        } else {
            $total = 0;
            $quantites_commandees = [];
            foreach ($_SESSION['panier'] as $article) {
                if (isset($article['prix']) && isset($article['quantite']) && !empty($article['prix']) && !empty($article['quantite'])) {
                    $total += $article['prix'] * $article['quantite'];
                    $quantites_commandees[$article['id']] = $article['quantite'];
                } else {
                    $_SESSION['message'] = 'Les informations des articles sont incomplètes.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                }
            }

            try {
                $pdo->beginTransaction();

                // Vérifier le stock pour chaque article commandé
                $problemes_stock = false;
                foreach ($quantites_commandees as $id_article => $quantite_commandee) {
                    $stmt_stock = $pdo->prepare("SELECT quantite FROM article WHERE id = :id_article");
                    $stmt_stock->execute(['id_article' => $id_article]);
                    $article_stock = $stmt_stock->fetch(PDO::FETCH_ASSOC);

                    if ($article_stock['quantite'] < $quantite_commandee) {
                        // Si la quantité commandée dépasse le stock, enregistrer le problème de stock mais quand même traiter la commande
                        $problemes_stock = true;
                    }
                }

                // Insérer la commande avec le statut approprié
                $stmt = $pdo->prepare("INSERT INTO commande (prenom, nom, adresse, telephone, date_commande, total, statut) VALUES (:prenom, :nom, :adresse, :telephone, :date_commande, :total, :statut)");
                $statut_commande = $problemes_stock ? 'en attente' : 'acceptée';
                $stmt->execute([
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'adresse' => $adresse,
                    'telephone' => $telephone,
                    'date_commande' => $date_commande,
                    'total' => $total,
                    'statut' => $statut_commande
                ]);

                $id_commande = $pdo->lastInsertId();

                // Insérer chaque article commandé dans la table 'commande_article'
                $stmt = $pdo->prepare("INSERT INTO commande_article (id_commande, id_article, quantite, prix) VALUES (:id_commande, :id_article, :quantite, :prix)");
                foreach ($_SESSION['panier'] as $article) {
                    $stmt->execute([
                        'id_commande' => $id_commande,
                        'id_article' => $article['id'],
                        'quantite' => $article['quantite'],
                        'prix' => $article['prix']
                    ]);

                    // Mettre à jour le stock dans la table 'article' même si la quantité commandée dépasse le stock disponible
                    $stmt_update = $pdo->prepare("UPDATE article SET quantite = quantite - :quantite WHERE id = :id_article");
                    $stmt_update->execute([
                        'quantite' => $article['quantite'],
                        'id_article' => $article['id']
                    ]);
                }

                $pdo->commit();

                // Vider le panier après la commande
                unset($_SESSION['panier']);

                if ($problemes_stock) {
                    $_SESSION['message'] = 'Commande validée ! Livraison dans deux jours. Veuillez nous contacter au ' . $numero_contact . ' pour plus d\'informations.';
                } else {
                    $_SESSION['message'] = 'Commande validée !';
                }

                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['message'] = 'Erreur lors de la validation de la commande : ' . $e->getMessage();
            }
        }
    }
}

// Récupérer le message de la session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passer une Commande</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
        .cart-icon {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: #007bff;
            color: #fff;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            font-size: 24px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .cart-popup {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 400px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: none;
            z-index: 1000;
            max-height: 500px;
            overflow-y: auto;
        }
        .cart-popup .card-header {
            background: #007bff;
            color: #fff;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-popup .btn-close {
            background: #fff;
            border: none;
            color: #007bff;
            font-size: 20px;
        }
        .cart-popup .btn-close:hover {
            background: #f8f9fa;
        }
        .table img {
            max-width: 150px;
            height: auto;
            object-fit: cover;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .form-group label {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Passer une Commande</h1>
            <!-- Formulaire de commande -->
            <div class="card">
                <h3 class="card-header">Passer une Commande</h3>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-info">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="numero_contact">Numéro de Contact pour Virement</label>
                        <input type="text" class="form-control" id="numero_contact" name="numero_contact" value="<?= htmlspecialchars($numero_contact) ?>" readonly>
                    </div>
                    <form id="commande" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                        <input type="hidden" name="action" value="commander">
                        <input type="hidden" name="date_commande" value="<?= date('Y-m-d') ?>">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="prenom">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Votre prénom" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="nom">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" placeholder="Votre nom" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Votre adresse" required>
                        </div>

                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Votre téléphone" required>
                        </div>

                        <h2>Liste des Articles</h2>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Article</th>
                                    <th>Prix Unitaire (FCFA)</th>
                                    <th>Quantité</th>
                                    <th>Ajouter</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($articles)): ?>
                                <?php foreach ($articles as $article): ?>
                                    <tr>
                                        <td><img src="<?= htmlspecialchars($article['images']) ?>" alt="Image de l'article"></td>
                                        <td><?= htmlspecialchars($article['nom_article']) ?></td>
                                        <td><?= htmlspecialchars($article['prix_unitaire']) ?> FCFA</td>
                                        <td><input type="number" class="form-control" id="quantite-<?= $article['id'] ?>" placeholder="Quantité" min="1"></td>
                                        <td><button type="button" class="btn btn-primary" onclick="ajouterAuPanier(<?= $article['id'] ?>, '<?= htmlspecialchars($article['nom_article']) ?>', document.getElementById('quantite-<?= $article['id'] ?>').value, <?= $article['prix_unitaire'] ?>, '<?= htmlspecialchars($article['images']) ?>')">Ajouter</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5">Aucun article trouvé.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>

                        <h2>Votre Panier</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Article</th>
                                    <th>Quantité</th>
                                    <th>Prix Unitaire</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="panier">
                                <!-- Les articles du panier seront affichés ici -->
                            </tbody>
                        </table>

                        <!-- Bouton pour valider la commande -->
                        <button type="submit" class="btn btn-primary">Valider la commande</button>
                    </form>
                </div>
            </div>
            <!-- Fin du formulaire de commande -->
        </div>
        <div class="col-md-4">
            <!-- Ajout du panier -->
            <div class="cart-icon" onclick="toggleCart()">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count">0</span>
            </div>
            <div class="cart-popup" id="cart-popup">
                <div class="card">
                    <div class="card-header">
                        Votre Panier
                        <button type="button" class="btn-close" onclick="toggleCart()">&times;</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Article</th>
                                    <th>Quantité</th>
                                    <th>Prix Unitaire</th>
                                </tr>
                            </thead>
                            <tbody id="panier-popup">
                                <!-- Les articles du panier seront affichés ici -->
                            </tbody>
                        </table>
                        <h4>Total: <span id="cart-total">0</span> FCFA</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let panier = <?php echo json_encode(isset($_SESSION['panier']) ? $_SESSION['panier'] : []); ?>;

    function ajouterAuPanier(id, nom, quantite, prix, images) {
        if (quantite <= 0) {
            alert("Veuillez entrer une quantité valide.");
            return;
        }

        // Vérifier si l'article est déjà dans le panier
        let index = panier.findIndex(article => article.id === id);
        if (index !== -1) {
            panier[index].quantite += parseInt(quantite);
        } else {
            const article = { id, nom, quantite, prix, images };
            panier.push(article);
        }

        // Mettre à jour le panier en session
        fetch('panier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'action': 'update',
                'panier': JSON.stringify(panier)
            })
        });

        afficherPanier();
        afficherPanierPopup();
    }

    function retirerDuPanier(index) {
        panier.splice(index, 1);
        // Mettre à jour le panier en session
        fetch('panier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'action': 'update',
                'panier': JSON.stringify(panier)
            })
        });

        afficherPanier();
        afficherPanierPopup();
    }

    function afficherPanier() {
        const panierElement = document.getElementById('panier');
        panierElement.innerHTML = '';

        panier.forEach((article, index) => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td><img src="${article.images}" alt="Image de l'article" style="max-width: 100px;"></td>
                <td>${article.nom}</td>
                <td>${article.quantite}</td>
                <td>${article.prix} FCFA</td>
                <td><button class="btn btn-danger" type="button" onclick="retirerDuPanier(${index})">Retirer</button></td>
            `;

            panierElement.appendChild(row);
        });

        document.getElementById('cart-count').textContent = panier.length;
    }

    function afficherPanierPopup() {
        const panierPopupElement = document.getElementById('panier-popup');
        const cartTotalElement = document.getElementById('cart-total');
        panierPopupElement.innerHTML = '';

        let total = 0;

        panier.forEach(article => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td><img src="${article.images}" alt="Image de l'article" style="max-width: 80px;"></td>
                <td>${article.nom}</td>
                <td>${article.quantite}</td>
                <td>${article.prix} FCFA</td>
            `;

            panierPopupElement.appendChild(row);

            total += article.prix * article.quantite;
        });

        cartTotalElement.textContent = total;
    }

    function toggleCart() {
        const cartPopup = document.getElementById('cart-popup');
        cartPopup.style.display = cartPopup.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('DOMContentLoaded', () => {
        afficherPanier();
        afficherPanierPopup();
    });
</script>
</body>
</html>
