<?php
session_start();
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");
require_once('header.php');

// Vérifie que l'id est dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Produit non trouvé.</p>";
    require_once('footer.php');
    exit;
}

$id = (int) $_GET['id'];

// Récupère les infos du produit
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id_article = :id");
$stmt->execute(['id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    echo "<p>Ce produit n'existe pas.</p>";
    require_once('footer.php');
    exit;
}

// Récupère les notations
$stmt = $pdo->prepare("SELECT * FROM notation WHERE id_article = :id");
$stmt->execute(['id' => $id]);
$notations = $stmt->fetchAll();
?>

<style>
    .produit {
        max-width: 600px;
        margin: 40px auto;
        background: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .produit h1 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #2c3e50;
    }
    .produit p {
        margin: 10px 0;
    }
    .avis {
        background: #fff;
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
    }
</style>

<div class="produit">
    <h1><?= htmlspecialchars($article['produit']) ?></h1>
    <p><strong>Prix :</strong> <?= number_format($article['prix'], 2, ',', ' ') ?> €</p>

    <?php if (!empty($article['description'])): ?>
        <p><strong>Description :</strong><br><?= nl2br(htmlspecialchars($article['description'])) ?></p>
    <?php endif; ?>

    <?php if ($notations): ?>
        <?php
        $notes = array_column($notations, 'note');
        $moyenne = round(array_sum($notes) / count($notes), 1);
        ?>
        <p><strong>Note moyenne :</strong> <?= $moyenne ?>/5</p>

        <div class="avis">
            <strong>Avis :</strong>
            <ul>
                <?php foreach ($notations as $note): ?>
                    <li><em>« <?= htmlspecialchars($note['avis']) ?> »</em></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <p>Aucune évaluation pour ce produit.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['utilisateur'])): ?>
        <!-- Formulaire pour ajouter au panier -->
        <form class="add-to-cart-form" data-id="<?= $article['id_article'] ?>" style="margin-top: 20px;">
            <button type="submit">Ajouter au panier 🛒</button>
        </form>
    <?php else: ?>
        <p style="margin-top: 20px;">
            <a href="account.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
                Se connecter pour ajouter au panier 🔐
            </a>
        </p>
    <?php endif; ?>

    <p style="margin-top: 20px;">
        <a href="categories.php?categorie=<?= $article['id_categorie'] ?>">← Retour à la catégorie</a>
    </p>
</div>

<!-- Script AJAX pour ajouter au panier -->
<script>
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const id = this.dataset.id;
        const button = this.querySelector('button');
        const originalText = button.textContent;

        button.disabled = true;
        button.textContent = "✅ Ajouté";

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_article=' + encodeURIComponent(id)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const panierLink = document.querySelector('a[href="panier.php"]');
                if (panierLink) {
                    panierLink.textContent = "Panier (" + data.total + ")";
                }

                setTimeout(() => {
                    button.disabled = false;
                    button.textContent = originalText;
                }, 3000);
            } else {
                button.textContent = "Erreur";
                setTimeout(() => {
                    button.disabled = false;
                    button.textContent = originalText;
                }, 3000);
            }
        })
        .catch(() => {
            button.textContent = "⚠️ Erreur réseau";
            setTimeout(() => {
                button.disabled = false;
                button.textContent = originalText;
            }, 1500);
        });
    });
});
</script>

<?php require_once('footer.php'); ?>
