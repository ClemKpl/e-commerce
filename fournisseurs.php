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
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        font-family: 'Segoe UI', sans-serif;
    }

    .fournisseur-container h1 {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .fournisseur-container p {
        font-size: 16px;
        margin-bottom: 10px;
    }

    .back-link {
        margin-top: 20px;
        display: inline-block;
        color: #8051a3;
        text-decoration: underline;
        font-size: 15px;
    }
</style>

<div class="fournisseur-container">
    <h1>🏢 Fournisseur : <?= htmlspecialchars($fournisseur['nom']) ?></h1>
    <p><strong>📍 Localisation :</strong> <?= htmlspecialchars($fournisseur['lieu']) ?></p>
    
    <p><strong>📝 Description :</strong> 
        <?= htmlspecialchars($fournisseur['description'] ?? 'Ce fournisseur ne dispose pas encore de description.') ?>
    </p>

    <a class="back-link" href="index.php">← Retour à l'accueil</a>
</div>

<?php require_once('footer.php'); ?>
