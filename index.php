<?php
session_start();
require_once('header.php');
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// On récupère 4 derniers produits
$produits = $pdo->query("SELECT * FROM articles ORDER BY id_article DESC LIMIT 4")->fetchAll();
?>

<style>
.accueil {
    max-width: 700px;
    margin: 40px auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.04);
    text-align: center;
}
.accueil h1 {
    font-size: 1.8em;
    color: #222;
    margin-bottom: 10px;
}
.accueil p {
    font-size: 1em;
    color: #555;
    margin-bottom: 30px;
}
.accueil .liens a {
    display: inline-block;
    margin: 10px;
    padding: 10px 16px;
    background-color: #e9bcd3;
    color: #333;
    font-weight: 500;
    text-decoration: none;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}
.accueil .liens a:hover {
    background-color: #d7a8c2;
}

.produits-recents {
    margin-top: 40px;
    text-align: left;
}
.produits-recents h2 {
    font-size: 1.4em;
    color: #444;
    margin-bottom: 20px;
}
.produit-card {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 16px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.03);
}
.produit-card h3 {
    margin: 0 0 6px;
    font-size: 1.1em;
    color: #333;
}
.produit-card p {
    margin: 4px 0;
    color: #555;
}
.produit-card a {
    color: #d38cad;
    font-weight: 500;
    text-decoration: none;
}
.produit-card a:hover {
    text-decoration: underline;
}
</style>

<div class="accueil">
    <h1>Bienvenue dans notre boutique 🛍️</h1>
    <p>Parcourez nos catégories, découvrez des produits variés et passez vos commandes facilement.</p>

    <div class="liens">
        <a href="categories.php">🗂️ Voir les catégories</a>
        <a href="account.php">👤 Mon compte</a>
        <a href="panier.php">🛒 Mon panier</a>
    </div>

    <div class="produits-recents">
        <h2>🆕 Derniers produits ajoutés</h2>
        <?php foreach ($produits as $prod): ?>
            <div class="produit-card">
                <h3><?= htmlspecialchars($prod['produit']) ?></h3>
                <p>Prix : <?= number_format($prod['prix'], 2, ',', ' ') ?> €</p>
                <a href="produit.php?id=<?= $prod['id_article'] ?>">→ Voir le produit</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once('footer.php'); ?>

