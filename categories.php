<?php
// Connexion PDO à la base de données
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

require_once('header.php');

// Récupérer toutes les catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Si une catégorie est sélectionnée, récupérer ses articles
$articles = [];
if (isset($_GET['categorie'])) {
    $categorieId = (int) $_GET['categorie'];
    
    $stmt = $pdo->prepare("
        SELECT * FROM articles 
        WHERE id_categorie = :id_categorie
    ");
    $stmt->execute(['id_categorie' => $categorieId]);
    $articles = $stmt->fetchAll();
}
?>

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

<h1>Catégories</h1>
<div class="categories">
    <?php foreach ($categories as $cat): ?>
        <a href="?categorie=<?= $cat['id_categorie'] ?>">
            <?= htmlspecialchars($cat['nom']) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (isset($_GET['categorie'])): ?>
    <h2>Articles de la catégorie : 
        <?= htmlspecialchars($categories[array_search($categorieId, array_column($categories, 'id_categorie'))]['nom']) ?>
    </h2>
    <?php if (count($articles) === 0): ?>
        <p>Aucun article dans cette catégorie.</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <strong><?= htmlspecialchars($article['produit']) ?></strong>
                Prix : <?= number_format($article['prix'], 2, ',', ' ') ?> €
                <form action="add_to_cart.php" method="post" style="margin-top: 10px;">
                    <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                    <button type="submit">Ajouter au panier 🛒</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<?php require_once('footer.php'); ?>
