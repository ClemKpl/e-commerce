<?php
session_start();
require_once('header.php');

$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Récupération des catégories et fournisseurs
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$fournisseurs = $pdo->query("SELECT * FROM fournisseurs")->fetchAll();

$confirmation = null;

// Vérifie si l'utilisateur est connecté et admin
$connecte = isset($_SESSION['utilisateur']);
$admin = $connecte && !empty($_SESSION['utilisateur']['admin']) && $_SESSION['utilisateur']['admin'] == 1;

// Traitement du formulaire uniquement si admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $admin) {
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

<style>
    .form-container {
        max-width: 600px;
        margin: 40px auto;
        background-color: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
    }

    .form-container h1 {
        margin-top: 0;
        font-size: 1.8em;
        color: #222;
    }

    .form-container form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .form-container label {
        font-weight: 500;
        color: #444;
    }

    .form-container input,
    .form-container select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 1em;
        width: 100%;
    }

    .form-container button {
        background-color: #e9bcd3;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        color: #333;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s ease;
        width: fit-content;
    }

    .form-container button:hover {
        background-color: #d7a8c2;
    }

    .confirmation-message {
        font-weight: 500;
        margin-bottom: 20px;
        padding: 12px;
        border-radius: 6px;
        background-color: #f3d1e0;
        color: #333;
    }

    .confirmation-message.error {
        background-color: #ffe1e1;
        color: #a00;
    }

    .warning-message {
        display: block;
        background-color: #fff5f5;
        color: #b10000;
        padding: 16px;
        text-align: center;
        border: 1px solid #ffcccc;
        border-radius: 10px;
        font-weight: 500;
        margin: 40px auto;
        max-width: 600px;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }

    .warning-message:hover {
        background-color: #ffecec;
    }
</style>

<div class="form-container">
    <h1>➕ Ajouter un nouvel article</h1>

    <?php if (!$admin): ?>
        <a href="account.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="warning-message">
            🔒 Accès réservé aux administrateurs. Cliquez ici pour vous connecter.
        </a>
    <?php else: ?>

        <?php if ($confirmation): ?>
            <div class="confirmation-message <?= str_starts_with($confirmation, '❌') ? 'error' : '' ?>">
                <?= $confirmation ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>
                Nom du produit :
                <input type="text" name="produit" required>
            </label>

            <label>
                Prix (€) :
                <input type="number" name="prix" step="0.01" min="0" required>
            </label>

            <label>
                Catégorie :
                <select name="id_categorie" required>
                    <option value="">-- Choisir une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Fournisseur (optionnel) :
                <select name="id_fournisseur">
                    <option value="">-- Aucun fournisseur --</option>
                    <?php foreach ($fournisseurs as $f): ?>
                        <option value="<?= $f['id_fournisseur'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button type="submit">Ajouter l'article</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once('footer.php'); ?>
