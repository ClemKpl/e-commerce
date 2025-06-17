<?php
// Connexion à la base de données
$pdo = new PDO("mysql:host=localhost;dbname=ecommerce", "root", "");

// Vérifie si un ID de produit est passé dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Produit non trouvé.";
    exit;
}

$id = $_GET['id'];

// Récupère les infos du produit dans la BDD
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    echo "Produit introuvable.";
    exit;
}
?>

<h1><?php echo htmlspecialchars($produit['nom']); ?></h1>

<?php if (!empty($produit['image'])): ?>
    <img src="<?php echo htmlspecialchars($produit['image']); ?>" alt="Image du produit" width="300">
<?php endif; ?>

<p>Description : <?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
<p>Prix : <?php echo htmlspecialchars($produit['prix']); ?> €</p>

<form action="ajouter_panier.php" method="post">
    <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
    <button type="submit">Ajouter au panier</button>
</form>