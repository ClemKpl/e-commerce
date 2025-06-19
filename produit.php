<?php
session_start();
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");
require_once('header.php');

// Récupère la catégorie
$categorie = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$tri = $_GET['tri'] ?? 'default';

// Détermine l'ordre de tri
$orderBy = match ($tri) {
    'meilleure_note' => 'ORDER BY moyenne_note DESC',
    'pire_note' => 'ORDER BY moyenne_note ASC',
    default => 'ORDER BY a.produit ASC',
};

// Récupère les articles de la catégorie avec leur moyenne de note
$stmt = $pdo->prepare("
    SELECT 
        a.*, 
        AVG(n.note) AS moyenne_note
    FROM 
        articles a
    LEFT JOIN 
        notation n ON a.id_article = n.id_article
    WHERE 
        a.id_categorie = :categorie
    GROUP BY 
        a.id_article
    $orderBy
");

$stmt->execute(['categorie' => $categorie]);
$articles = $stmt->fetchAll();
?>

<style>
    .articles-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
    }

    .tri-options {
        margin-bottom: 20px;
    }

    .tri-options a {
        margin-right: 10px;
        text-decoration: none;
        color: #555;
        padding: 6px 12px;
        background: #f2f2f2;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .tri-options a:hover {
        background: #e2e2e2;
    }

    .article-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 20px;
    }

    .article-card h2 {
        font-size: 1.4em;
        color: #222;
        margin-bottom: 10px;
    }

    .article-card p {
        color: #555;
        margin-bottom: 8px;
    }

    .article-card a {
        color: #d38cad;
        text-decoration: underline;
    }
</style>

<div class="articles-container">
    <h1>Articles</h1>

    <div class="tri-options">
        <strong>🔽 Trier par :</strong>
        <a href="?categorie=<?= $categorie ?>&tri=default">Nom</a>
        <a href="?categorie=<?= $categorie ?>&tri=meilleure_note">Meilleure note ⭐</a>
        <a href="?categorie=<?= $categorie ?>&tri=pire_note">Pire note 😞</a>
    </div>

    <?php if (count($articles) > 0): ?>
        <?php foreach ($articles as $article): ?>
            <div class="article-card">
                <h2><?= htmlspecialchars($article['produit']) ?></h2>
                <p><strong>Prix :</strong> <?= number_format($article['prix'], 2, ',', ' ') ?> €</p>
                <?php if (!is_null($article['moyenne_note'])): ?>
                    <p><strong>Note :</strong> <?= round($article['moyenne_note'], 1) ?>/5</p>
                <?php else: ?>
                    <p><em>Pas encore noté</em></p>
                <?php endif; ?>
                <a href="produit.php?id=<?= $article['id_article'] ?>">Voir le produit →</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun article trouvé dans cette catégorie.</p>
    <?php endif; ?>
</div>

<?php require_once('footer.php'); ?>
