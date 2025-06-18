<?php
session_start();
require_once('header.php');
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// On récupère 4 produits au hasard ou les plus récents
$produits = $pdo->query("SELECT * FROM articles ORDER BY id_article DESC LIMIT 4")->fetchAll();
?>

<style>
.accueil {
    max-width: 1000px;
    margin: 40px auto;
    padding: 30px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    text-align: center;
}
.accueil h1 {
    font-size: 2em;
    margin-bottom: 20px;
    color: #333;
}
.accueil p {
    font-size: 1.1em;
    color: #555;
}
.accueil .liens {
    margin-top: 30px;
}
.accueil .liens a {
    display: inline-block;
    margin: 10px;
    padding: 12px 20px;
    background-color: #e9bcd3;
    border-radius: 8px;
    color: #333;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.2s ease;
}
.accueil .liens a:hover {
    background-color: #d7a8c2;
}

.produits-en-vedette {
    margin-top: 40px;
}
.produits-en-vedette h2 {
    font-size: 1.5em;
    color: #444;
    margin-bottom: 20px;
}
.produits-grid {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
}
.produit-card {
    width: 220px;
    background-color: #fdfdfd;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    text-align: left;
}
.produit-card h3 {
    font-size: 1.1em;
    margin: 0 0 8px;
}
.produit-card p {
    margin: 0;
    font-size: 0.95em;
    color: #666;
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
    <h1>Bienvenue dans notre magasin en ligne 🛍️</h1>
    <p>Découvrez nos catégories, produits et promotions. Commandez en toute simplicité !</p>

    <div class="liens">
        <a href="categories.php">Voir les catégories 📂</a>
        <a href="account.php">Mon compte 👤</a>
        <a href="panier.php">Mon panier 🛒</a>
    </div>

    <div class="produits-en-vedette">
        <h2>🆕 Derniers produits ajoutés</h2>
        <div class="produits-grid">
            <?php foreach ($produits as $prod): ?>
                <div class="produit-card">
                    <h3><?= htmlspecialchars($prod['produit']) ?></h3>
                    <p><?= number_format($prod['prix'], 2, ',', ' ') ?> €</p>
                    <a href="produit.php?id=<?= $prod['id_article'] ?>">Voir le produit →</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>

