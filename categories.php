<?php
// Connexion PDO à la base de données
$pdo = new PDO("mysql:host=10.96.16.82;dbname=VOTRE_BASE;charset=utf8", "colin", "");

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

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Navigation par catégorie</title>
    <style>
        body { font-family: sans-serif; }
        .categories a { margin-right: 10px; text-decoration: none; color: blue; }
        .categories a:hover { text-decoration: underline; }
        .article { border-bottom: 1px solid #ccc; margin: 10px 0; padding: 5px 0; }
    </style>
</head>
<body>
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
                    <strong><?= htmlspecialchars($article['produit']) ?></strong><br>
                    Prix : <?= number_format($article['prix'], 2, ',', ' ') ?> €
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
