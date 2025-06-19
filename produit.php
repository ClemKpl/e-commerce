<?php
session_start();
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");
require_once('header.php');

// Vérifie que la catégorie est bien spécifiée
if (!isset($_GET['categorie']) || !is_numeric($_GET['categorie'])) {
    echo "<p>Catégorie non spécifiée.</p>";
    require_once('footer.php');
    exit;
}

$categorie_id = (int) $_GET['categorie'];
$tri = $_GET['tri'] ?? null;

// Requête SQL : articles + note moyenne
$sql = "
    SELECT 
        a.*,
        AVG(n.note) AS moyenne_note
    FROM articles a
    LEFT JOIN notation n ON a.id_article = n.id_article
    WHERE a.id_categorie = :categorie
    GROUP BY a.id_article
";

// Tri selon paramètre
if ($tri === 'meilleure_note') {
    $sql .= " ORDER BY moyenne_note DESC";
} elseif ($tri === 'pire_note') {
    $sql .= " ORDER BY moyenne_note ASC";
} else {
    $sql .= " ORDER BY a.produit ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute(['categorie' => $categorie_id]);
$articles = $stmt->fetchAll();
?>

<style>
.tri-buttons {
    display: flex;
    gap: 10px;
    margin: 20px 0;
}
.tri-buttons a {
    background-color: #e9bcd3;
    padding: 10px 14px;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
    transition: background 0.3s;
}
.tri-buttons a:hover {
    background-color: #d7a8c2;
}
.produit-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.produit-card h3 {
    margin-top: 0;
    font-size: 1.2em;
}
.produit-card p {
    margin: 8px 0;
    color: #555;
}
.produit-card a {
    color: #d38cad;
    text-decoration: none;
}
.produit-card a:hover {
    text-decoration: underline;
}
</style>

<div class="container">
    <h1>Produits de la catégorie <?= htmlspecialchars($categorie_id) ?></h1>

    <div class="tri-buttons">
        <a href="?categorie=<?= $categorie_id ?>&tri=meilleure_note">🔝 Meilleure note</a>
        <a href="?categorie=<?= $categorie_id ?>&tri=pire_note">🔻 Pire note</a>
        <a href="?categorie=<?= $categorie_id ?>">📄 Par défaut</a>
    </div>

    <?php if (!$articles): ?>
        <p>Aucun produit trouvé dans cette catégorie.</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="produit-card">
                <h3><?= htmlspecialchars($article['produit']) ?></h3>
                <p><strong>Prix :</strong> <?= number_format($article['prix'], 2, ',', ' ') ?> €</p>
                <?php if ($article['moyenne_note'] !== null): ?>
                    <p><strong>Note moyenne :</strong> <?= round($article['moyenne_note'], 1) ?>/5</p>
                <?php else: ?>
                    <p><em>Pas encore noté</em></p>
                <?php endif; ?>
                <p>
                    <a href="produit.php?id=<?= $article['id_article'] ?>">Voir le produit →</a>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once('footer.php'); ?>
