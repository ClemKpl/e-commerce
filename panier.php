<?php
// Démarre la session pour accéder au panier
session_start();

// Inclut l'en-tête de la page (navigation, logo, etc.)
require_once('header.php');

// Connexion à la base de données via PDO
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Récupération du panier depuis la session ou tableau vide si non défini
$panier = $_SESSION['panier'] ?? [];

// Si l'utilisateur clique sur "vider le panier"
if (isset($_GET['vider'])) {
    unset($_SESSION['panier']); // Supprime tout le panier
    header("Location: panier.php"); // Recharge la page
    exit; // Arrête le script
}

// Si l'utilisateur veut supprimer un article spécifique
if (isset($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer']; // Récupère l'ID de l'article
    unset($_SESSION['panier'][$id]); // Supprime l'article du panier
    header("Location: panier.php"); // Recharge la page
    exit;
}

// Initialisation du total et du tableau d'articles
$total = 0;
$articles = [];

// Si le panier contient des articles
if (!empty($panier)) {
    // Récupère tous les IDs des articles dans le panier
    $ids = implode(',', array_keys($panier));

    // Requête SQL pour récupérer les détails des articles
    $stmt = $pdo->query("SELECT * FROM articles WHERE id_article IN ($ids)");
    $articles = $stmt->fetchAll(); // Stocke les résultats dans un tableau
}
?>

<h1>🛒 Mon panier</h1>

<?php if (empty($articles)): ?>
    <!-- Message si le panier est vide -->
    <p>Votre panier est vide.</p>
<?php else: ?>
<<<<<<< HEAD

    <!-- Tableau affichant les articles du panier -->
=======
>>>>>>> 48d8a0f227dc2b77e55a63b99f6e814c67ef5117
    <table style="width:100%; border-collapse: collapse; margin-bottom: 30px;">
        <thead>
            <tr style="background-color: #ecf0f1;">
                <th>Produit</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Sous-total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
<<<<<<< HEAD
            <?php foreach ($articles as $article):
                $id = $article['id_article']; // ID de l'article
                $quantite = $panier[$id]; // Quantité dans le panier
                $sousTotal = $article['prix'] * $quantite; // Prix total pour cet article
                $total += $sousTotal; // Ajoute au total général
            ?>
            <tr>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= htmlspecialchars($article['produit']) ?>
                </td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= number_format($article['prix'], 2, ',', ' ') ?> €
                </td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= $quantite ?>
                </td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= number_format($sousTotal, 2, ',', ' ') ?> €
                </td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <!-- Lien pour supprimer cet article du panier -->
                    <a href="panier.php?supprimer=<?= $id ?>" onclick="return confirm('Supprimer ce produit ?')">
                        ❌ Supprimer
                    </a>
                </td>
=======
            <?php foreach ($articles as $article): 
                $id = $article['id_article'];
                $quantite = $panier[$id];
                $sousTotal = $article['prix'] * $quantite;
                $total += $sousTotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($article['produit']) ?></td>
                <td><?= number_format($article['prix'], 2, ',', ' ') ?> €</td>
                <td><?= $quantite ?></td>
                <td><?= number_format($sousTotal, 2, ',', ' ') ?> €</td>
                <td><a href="panier.php?retirer=<?= $id ?>">❌ Retirer 1</a></td>
>>>>>>> 48d8a0f227dc2b77e55a63b99f6e814c67ef5117
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <!-- Ligne du total général du panier -->
            <tr style="background-color: #f8f9fa;">
<<<<<<< HEAD
                <th colspan="3" style="padding: 10px; border: 1px solid #ccc;">Total général</th>
                <th colspan="2" style="padding: 10px; border: 1px solid #ccc;">
                    <?= number_format($total, 2, ',', ' ') ?> €
                </th>
=======
                <th colspan="3">Total</th>
                <th colspan="2"><?= number_format($total, 2, ',', ' ') ?> €</th>
>>>>>>> 48d8a0f227dc2b77e55a63b99f6e814c67ef5117
            </tr>
        </tfoot>
    </table>

    <!-- Lien pour vider le panier -->
    <a href="panier.php?vider=1" style="
        display:inline-block;
        background-color: #e74c3c;
        color:white;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
    ">🧹 Vider le panier</a>
<?php endif; ?>

<!-- Inclusion du pied de page -->
<?php require_once('footer.php'); ?>
