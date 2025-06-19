<?php
session_start();
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");
require_once('header.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Fournisseur non trouvé.</p>";
    require_once('footer.php');
    exit;
}

$id_fournisseur = (int) $_GET['id'];

// ✅ On récupère aussi l'ID du produit s’il est présent dans l’URL
$produit_id = isset($_GET['produit_id']) && is_numeric($_GET['produit_id']) ? (int) $_GET['produit_id'] : null;

// Récupération des infos du fournisseur
$stmt = $pdo->prepare("SELECT * FROM fournisseurs WHERE id_fournisseur = :id");
$stmt->execute(['id' => $id_fournisseur]);
$fournisseur = $stmt->fetch();

if (!$fournisseur) {
    echo "<p>Ce fournisseur n'existe pas.</p>";
    require_once('footer.php');
    exit;
}
?>

<div style="max-width:700px;margin:40px auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.04);">
    <h1><?= htmlspecialchars($fournisseur['nom']) ?></h1>
    <p><strong>Adresse :</strong> <?= htmlspecialchars($fournisseur['adresse']) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($fournisseur['email']) ?></p>

    <?php if ($produit_id): ?>
        <p style="margin-top:30px;">
            <a href="produit.php?id=<?= $produit_id ?>" style="color:#d38cad;text-decoration:underline;">
                ← Retour au produit
            </a>
        </p>
    <?php endif; ?>
</div>

<?php require_once('footer.php'); ?>
