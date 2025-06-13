<?php
// Vérifier si l'identifiant de la commande est présent dans l'URL et l'action demandée
if (isset($_GET['id'])) {
    $commande_id = $_GET['id'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestion_stock"; // Remplacez par le nom de votre base de données

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Commencer une transaction pour s'assurer que toutes les opérations se font atomiquement
        $pdo->beginTransaction();

        if ($action === 'accept') {
            // Vérifier les quantités disponibles par rapport aux quantités commandées
            $sql_check = "
                SELECT a.id AS article_id, ca.quantite AS quantite_commande, a.quantite AS quantite_disponible
                FROM commande_article ca
                JOIN article a ON ca.id_article = a.id
                WHERE ca.id_commande = :commande_id
            ";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->bindParam(':commande_id', $commande_id, PDO::PARAM_INT);
            $stmt_check->execute();

            $is_ready_to_accept = true;

            while ($row = $stmt_check->fetch(PDO::FETCH_ASSOC)) {
                if ($row['quantite_disponible'] < $row['quantite_commande']) {
                    $is_ready_to_accept = false;
                    break;
                }
            }

            if ($is_ready_to_accept) {
                // Mettre à jour le statut de la commande à "acceptee"
                $sql_update = "UPDATE commande SET statut = 'acceptee' WHERE id = :commande_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->bindParam(':commande_id', $commande_id, PDO::PARAM_INT);
                $stmt_update->execute();

                // Mettre à jour la quantité des articles
                $sql_update_quantite = "UPDATE article a
                                        JOIN commande_article ca ON a.id = ca.id_article
                                        SET a.quantite = a.quantite - ca.quantite
                                        WHERE ca.id_commande = :commande_id";
                $stmt_update_quantite = $pdo->prepare($sql_update_quantite);
                $stmt_update_quantite->bindParam(':commande_id', $commande_id, PDO::PARAM_INT);
                $stmt_update_quantite->execute();

                // Commit la transaction
                $pdo->commit();

                // Redirection vers la page principale avec un message de succès
                header("Location: liste_commadmin.php?acceptee=1");
                exit();
            } else {
                // Réponse si la quantité est toujours insuffisante
                echo "<p>La commande ne peut pas être acceptée car la quantité disponible est insuffisante.</p>";
                header("Location: liste_commadmin.php?acceptee=1");
            }

        } else {
            // Mise à jour du statut de la commande à "attente" en cas de rupture de stock
            $sql_update_waiting = "UPDATE commande SET statut = 'attente' WHERE id = :commande_id";
            $stmt_update_waiting = $pdo->prepare($sql_update_waiting);
            $stmt_update_waiting->bindParam(':commande_id', $commande_id, PDO::PARAM_INT);
            $stmt_update_waiting->execute();

            // Réapprovisionnement des articles (exemple de réapprovisionnement; ajustez selon vos besoins)
            $sql_replenish = "UPDATE article a
                              JOIN commande_article ca ON a.id = ca.id_article
                              SET a.quantite = a.quantite + 1
                              WHERE ca.id_commande = :commande_id";
            $stmt_replenish = $pdo->prepare($sql_replenish);
            $stmt_replenish->bindParam(':commande_id', $commande_id, PDO::PARAM_INT);
            $stmt_replenish->execute();

            // Commit la transaction
            $pdo->commit();

            // Redirection vers la page principale avec un message de réapprovisionnement
            header("Location: liste_commadmin.php?attente=1");
            exit();
        }

    } catch(PDOException $e) {
        // En cas d'erreur lors de la connexion à la base de données ou de l'exécution de la requête, afficher un message d'erreur
        echo "Erreur : " . $e->getMessage();
        $pdo->rollBack();
    }

    // Fermeture de la connexion PDO
    $pdo = null;
} else {
    // Si l'identifiant de la commande n'est pas présent dans l'URL, rediriger vers la page principale
    header("Location: liste_commadmin.php");
    exit();
}
?>
