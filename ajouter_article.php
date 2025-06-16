<?php
session_start();
require_once('header.php');

$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Récupération des catégories et fournisseurs pour le menu déroulant
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$fournisseurs = $pdo->query("SELECT * FROM fournisseurs")->fetchAll();

$confirmation = null;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['produit'] ?? '';
    $prix = floatval($_POST['prix'] ?? 0);
    $id_categorie = intval($_POST['id_categorie'] ?? 0);
    $id_fournisseur = !empty($_POST['id_fournisseur']) ? intval($_POST['id_fournisseur']) : null;

    if ($nom && $prix > 0 && $id_categorie) {
        $stmt = $pdo->prepare("
            INSERT INTO articles (produit, prix, id_categorie, id_fournisseur)
            VALUES (:produit, :prix, :id_categorie, :id_fournisseur)
        ");
        $stmt->execute([
            'produit' => $nom,
            'prix' => $prix,
            'id_categorie' => $id_categorie,
            'id_fournisseur' => $id_fournisseur
        ]);

        $confirmation = "✅ Article ajouté avec succès !";
    } else {
        $confirmation = "❌ Merci de remplir tous les champs correctement.";
    }
}
?>

<h1>➕ Ajouter un nouvel article</h1>

<?php if ($confirmation): ?>
    <p><strong><?= $confirmation ?></strong></p>
<?php endif; ?>

<form method="post" style="max-width: 400px;">
    <label>Nom du produit :<br>
        <input type="text" name="produit" required>
    </label><br><br>

    <label>Prix (€) :<br>
        <input type="number" name="prix" step="0.01" min="0" required>
    </label><br><br>

    <label>Catégorie :<br>
        <select name="id_categorie" required>
            <option value="">-- Choisir une catégorie --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Fournisseur (optionnel) :<br>
        <select name="id_fournisseur">
            <option value="">-- Aucun fournisseur --</option>
            <?php foreach ($fournisseurs as $f): ?>
                <option value="<?= $f['id_fournisseur'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <button type="submit">Ajouter l'article</button>
</form>

<?php require_once('footer.php'); ?>
