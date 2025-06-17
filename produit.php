<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=magasin;charset=utf8", "root", "");

// Vérifie qu'on a bien un ID dans l'URL
if (!isset($_GET['id'])) {
    die("ID de produit manquant.");
}

$id = (int) $_GET['id'];

// Récupère les infos de l'article
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id_article = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    die("Article non trouvé.");
}

// Récupère les notations liées à cet article
$stmt = $pdo->prepare("SELECT * FROM notation WHERE id_article = ?");
$stmt->execute([$id]);
$notations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title><?= htmlspecialchars($article['produit']) ?></title></head>
<body>
    <h1><?= htmlspecialchars($article['produit']) ?></h1>
    <p>Prix : <?= number_format($article['prix'], 2, ',', ' ') ?> €</p>

    <?php if (count($notations) > 0): ?>
        <h3>Notes :</h3>
        <ul>
            <?php 
            $total = 0;
            foreach ($notations as $note):
                $total += $note['note'];
            ?>
                <li>
                    Note : <?= $note['note'] ?>/5
                    <br><em><?= htmlspecialchars($note['avis']) ?></em>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Moyenne :</strong> <?= round($total / count($notations), 1) ?>/5</p>
    <?php else: ?>
        <p>Aucune notation.</p>
    <?php endif; ?>
</body>
</html>
