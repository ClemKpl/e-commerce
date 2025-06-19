<?php
// Démarre la session pour accéder au panier
session_start();

// Inclut l'en-tête de la page (navigation, logo, etc.)
require_once('header.php');

$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    echo "<p>Veuillez vous connecter pour accéder à votre panier.</p>";
    require_once('footer.php');
    exit;
}

$idClient = $_SESSION['utilisateur']['id'];

// Récupère le panier du client
$panier = $_SESSION['panier'][$idClient] ?? [];

// Gérer les suppressions
if (isset($_GET['retirer'])) {
    $id = (int) $_GET['retirer'];
    if (isset($panier[$id])) {
        $panier[$id]--;
        if ($panier[$id] <= 0) {
            unset($panier[$id]);
        }
        $_SESSION['panier'][$idClient] = $panier;
    }
    header("Location: panier.php");
    exit;
}

// Gérer les ajouts
if (isset($_GET['ajouter'])) {
    $id = (int) $_GET['ajouter'];
    if (isset($panier[$id])) {
        $panier[$id]++;
        $_SESSION['panier'][$idClient] = $panier;
    }
    header("Location: panier.php");
    exit;
}

if (isset($_GET['vider'])) {
    unset($_SESSION['panier'][$idClient]);
    header("Location: panier.php");
    exit;
}

if (isset($_GET['acheter'])) {
    //Ici on pourrait enregistrer la commande dans la base de données

    //On vide le panier après l'achat
    unset($_SESSION['panier'][$idClient]);

    //On définit un message de confirmation
    $_SESSION['achat_validé'] = true;

    header("Location : panier.php");
    exit
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
<?php if (isset($_SESSION['achat_validé'])): ?>
    <div style="display:
        padding : 15px;
        background-color: #2ecc71;
        color: white;
        border-radius: 5px;
        margin-bottom: 20px;
        font-weight : bold;
    ">
        ✅ Votre achat a été validé avec succès !
    </div>
    <?php unset($_SESSION['achat_validé']); ?>
<?php endif; ?>

<?php if (empty($articles)): ?>
    <!-- Message si le panier est vide -->
    <p>Votre panier est vide.</p>
<?php else: ?>
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
            <?php foreach ($articles as $article): 
                $id = $article['id_article'];
                $quantite = $panier[$id];
                $sousTotal = $article['prix'] * $quantite;
                $total += $sousTotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($article['produit']) ?></td>
                <td><?= number_format($article['prix'], 2, ',', ' ') ?> €</td>
                <td>
                    <a href="panier.php?retirer=<?= $id ?>" style="margin-right:5px;">➖</a>
                    <?= $quantite ?>
                    <a href="panier.php?ajouter=<?= $id ?>" style="margin-left:5px;">➕</a>
                </td>
                <td><?= number_format($sousTotal, 2, ',', ' ') ?> €</td>
                <td><a href="panier.php?retirer=<?= $id ?>">❌ Retirer 1</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <!-- Ligne du total général du panier -->
            <tr style="background-color: #f8f9fa;">
                <th colspan="3">Total</th>
                <th colspan="2"><?= number_format($total, 2, ',', ' ') ?> €</th>
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
