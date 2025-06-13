<?
function getArticleFromDB() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM articles");
    return $stmt->fetchAll();
}

// Fonction pour récupérer tous les fournisseurs depuis la base de données
function getFournisseursFromDB() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM fournisseurs");
    return $stmt->fetchAll();
}

// Fonction pour récupérer toutes les commandes depuis la base de données
function getCommandesFromDB() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT c.*, a.nom_article, f.nom, f.prenom 
                         FROM commandes c
                         LEFT JOIN articles a ON c.id_article = a.id
                         LEFT JOIN fournisseurs f ON c.id_fournisseur = f.id");
    return $stmt->fetchAll();
}
?>
