<?php 
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");
require_once('header.php');

// Vérification de l'ID du fournisseur
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='fournisseur-container'><p>Fournisseur non trouvé.</p></div>";
    require_once('footer.php');
    exit;
}

$id_fournisseur = (int) $_GET['id'];
$id_produit = $_GET['produit_id'] ?? null;

// Récupération du fournisseur
$stmt = $pdo->prepare("SELECT * FROM fournisseurs WHERE id_fournisseur = :id");
$stmt->execute(['id' => $id_fournisseur]);
$fournisseur = $stmt->fetch();

if (!$fournisseur) {
    echo "<div class='fournisseur-container'><p>Ce fournisseur n'existe pas.</p></div>";
    require_once('footer.php');
    exit;
}
?>

<style>
    .fournisseur-container {
        max-width: 700px;
        margin: 40px auto;
        background: #fff0f7;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 6px 12px rgba(211, 140, 173, 0.15);
        font-family: 'Segoe UI', sans-serif;
        color: #5a2a4f;
    }

    .fournisseur-container h1 {
        font-size: 28px;
        margin-bottom: 15px;
        color: #d38cad;
    }

    .fournisseur-container p {
        font-size: 16px;
        margin-bottom: 12px;
        line-height: 1.6;
    }

    .fournisseur-container strong {
        color:rgb(0, 0, 0);
    }

    .back-link {
        margin-top: 25px;
        display: inline-block;
        color: #cc6fa3;
        text-decoration: none;
        font-weight: bold;
        font-size: 15px;
        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: #a64f7b;
        text-decoration: underline;
    }
</style>

<div class="fournisseur-container">
    <h1>🏢 Fournisseur : <?= htmlspecialchars($fournisseur['nom']) ?></h1>
    <p><strong>📍 Localisation :</strong> <?= htmlspecialchars($fournisseur['lieu']) ?></p>
    
    <p><strong>📝 Description :</strong> 
        <?= htmlspecialchars($fournisseur['description'] ?? 'Ce fournisseur ne dispose pas encore de description.') ?>
    </p>

    <?php if ($id_produit): ?>
        <a class="back-link" href="produit.php?id=<?= urlencode($id_produit) ?>">← Retour au produit</a>
    <?php else: ?>
        <a class="back-link" href="produit.php">← Retour aux produits</a>
    <?php endif; ?>
</div>

<?php require_once('footer.php'); ?>
