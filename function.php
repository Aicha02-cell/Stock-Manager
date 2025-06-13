<?php
// Vérifier si la fonction n'est pas déjà définie avant de la déclarer
if (!function_exists('getCategorie')) {
    // Fonction pour récupérer une catégorie par son ID (exemple)
    function getCategorie($id = null) {
        // Code pour récupérer la catégorie depuis la source de données
        // Exemple : connexion à la base de données, requête SQL, etc.
        // Ici, retournez des données fictives pour illustration
        $categories = [
            ['id' => 1, 'libelle_categorie' => 'Catégorie 1'],
            ['id' => 2, 'libelle_categorie' => 'Catégorie 2'],
            ['id' => 3, 'libelle_categorie' => 'Catégorie 3'],
        ];

        if ($id !== null) {
            foreach ($categories as $categorie) {
                if ($categorie['id'] == $id) {
                    return $categorie;
                }
            }
            return []; // Retourner un tableau vide si aucune catégorie trouvée
        } else {
            return $categories; // Retourner toutes les catégories si aucun ID spécifié
        }
    }
}
?>
