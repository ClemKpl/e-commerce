<?php
session_start();
require_once('header.php');

// Connexion BDD
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Récupération du panier
$panier = $_SESSION['panier'] ?? [];

// Vider tout le panier
if (isset($_GET['vider'])) {
    unset($_SESSION['panier']);
    header("Location: panier.php");
    exit;
}

if (isset($_GET['retirer'])) {
    $id = (int) $_GET['retirer'];

    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]--;

        if ($_SESSION['panier'][$id] <= 0) {
            unset($_SESSION['panier'][$id]);
        }
    }

    header("Location: panier.php");
    exit;
}

$total = 0;
$articles = [];

if (!empty($panier)) {
    $ids = implode(',', array_keys($panier));
    $stmt = $pdo->query("SELECT * FROM articles WHERE id_article IN ($ids)");
    $articles = $stmt->fetchAll();
}
?>

<h1>🛒 Mon panier</h1>

<?php if (empty($articles)): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>

    <table style="width:100%; border-collapse: collapse; margin-bottom: 30px;">
        <thead>
            <tr style="background-color: #ecf0f1;">
                <th style="padding: 10px; border: 1px solid #ccc;">Produit</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Prix unitaire</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Quantité</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Total</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article):
                $id = $article['id_article'];
                $quantite = $panier[$id];
                $sousTotal = $article['prix'] * $quantite;
                $total += $sousTotal;
            ?>
            <tr>
                <td style="padding: 10px; border: 1px solid #ccc;"><?= htmlspecialchars($article['produit']) ?></td>
                <td style="padding: 10px; border: 1px solid #ccc;"><?= number_format($article['prix'], 2, ',', ' ') ?> €</td>
                <td style="padding: 10px; border: 1px solid #ccc;"><?= $quantite ?></td>
                <td style="padding: 10px; border: 1px solid #ccc;"><?= number_format($sousTotal, 2, ',', ' ') ?> €</td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <a href="panier.php?retirer=<?= $id ?>">❌ Retirer 1</a>

                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <th colspan="3" style="padding: 10px; border: 1px solid #ccc;">Total général</th>
                <th colspan="2" style="padding: 10px; border: 1px solid #ccc;"><?= number_format($total, 2, ',', ' ') ?> €</th>
            </tr>
        </tfoot>
    </table>

    <a href="panier.php?vider=1" style="
        display: inline-block;
        background-color: #e74c3c;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
    " onclick="return confirm('Vider tout le panier ?')">🧹 Vider le panier</a>

<?php endif; ?>

<?php require_once('footer.php'); ?>
