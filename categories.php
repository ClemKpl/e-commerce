<?php
// Connexion à la base de données via PDO
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Inclusion du header (structure HTML, menu, etc.)
require_once('header.php');

// Récupération de toutes les catégories dans la base de données
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Initialisation du tableau des articles
$articles = [];

// Si l'URL contient un paramètre 'categorie'
if (isset($_GET['categorie'])) {
    // On convertit l'identifiant de la catégorie en entier (sécurité)
    $categorieId = (int) $_GET['categorie'];
    
    // Préparation de la requête pour récupérer les articles liés à cette catégorie
    $stmt = $pdo->prepare("
        SELECT * FROM articles 
        WHERE id_categorie = :id_categorie
    ");
    
    // Exécution de la requête avec liaison de paramètre
    $stmt->execute(['id_categorie' => $categorieId]);

    // Récupération des articles sous forme de tableau associatif
    $articles = $stmt->fetchAll();
}
?>

<!-- Style CSS intégré pour la page -->
<style>
    h1, h2 {
        color: #2c3e50;
    }

    .categories {
        margin: 20px 0;
        padding: 10px 0;
        border-bottom: 1px solid #ccc;
    }

    .categories a {
        display: inline-block;
        background-color: #3498db;
        color: white;
        padding: 8px 14px;
        margin: 5px 10px 5px 0;
        border-radius: 5px;
        text-decoration: none;
        transition: 0.3s;
    }

    .categories a:hover {
        background-color: #2980b9;
    }

    .article {
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .article strong {
        font-size: 1.1em;
        display: block;
        margin-bottom: 5px;
    }
</style>

<!-- Titre principal -->
<h1>Catégories</h1>

<!-- Liste des catégories sous forme de boutons -->
<div class="categories">
    <?php foreach ($categories as $cat): ?>
        <!-- Lien vers la même page avec l'ID de la catégorie en paramètre GET -->
        <a href="?categorie=<?= $cat['id_categorie'] ?>">
            <?= htmlspecialchars($cat['nom']) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Si une catégorie a été sélectionnée -->
<?php if (isset($_GET['categorie'])): ?>
    <h2>Articles de la catégorie : 
        <!-- Affiche le nom de la catégorie actuelle -->
        <?= htmlspecialchars($categories[array_search($categorieId, array_column($categories, 'id_categorie'))]['nom']) ?>
    </h2>

    <!-- Si aucun article dans cette catégorie -->
    <?php if (count($articles) === 0): ?>
        <p>Aucun article dans cette catégorie.</p>

    <!-- Sinon, affichage des articles -->
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <!-- Nom de l'article -->
                <strong><?= htmlspecialchars($article['produit']) ?></strong>

                <!-- Prix de l'article formaté en euro -->
                Prix : <?= number_format($article['prix'], 2, ',', ' ') ?> €

                <!-- Formulaire pour ajouter l'article au panier -->
                <form action="add_to_cart.php" method="post" style="margin-top: 10px;">
                    <!-- Champ caché avec l'ID de l'article -->
                    <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                    
                    <!-- Bouton pour ajouter au panier -->
                    <button type="submit">Ajouter au panier 🛒</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<!-- Inclusion du footer -->
<?php require_once('footer.php'); ?>
