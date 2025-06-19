<?php
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");
require_once('header.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Fournisseur non trouvé.</p>";
    require_once('footer.php');
    exit;
}

$id_fournisseur = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM fournisseurs WHERE id_fournisseur = :id");
$stmt->execute(['id' => $id_fournisseur]);
$fournisseur = $stmt->fetch();

if (!$fournisseur) {
    echo "<p>Ce fournisseur n'existe pas.</p>";
    require_once('footer.php');
    exit;
}
?>

<div class="fournisseur" style="max-width:700px;margin:40px auto;padding:30px;background:#fff;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.04);">
    <h1>Fournisseur : <?= htmlspecialchars($fournisseur['nom_fournisseur']) ?></h1>
    <p><strong>ID :</strong> <?= $fournisseur['id_fournisseur'] ?></p>
    <p><a href="index.php">← Retour à l'accueil</a></p>
</div>

<?php require_once('footer.php'); ?>
