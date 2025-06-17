<?php
session_start();
require_once('header.php');

// Connexion à la base
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Vérifie si un ID d'article est fourni
if (!isset($_GET['id'])) {
    echo "<p>Article non spécifié.</p>";
    require_once('footer.php');
    exit;
}

// Récupère l'article depuis la BDD
$id_article = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id_article = :id");
$stmt->execute(['id' => $id_article]);
$article = $stmt->fetch();

if (!$article) {
    echo "<p>Article introuvable.</p>";
    require_once('footer.php');
    exit;
}
?>

<h1><?= htmlspecialchars($article['produit']) ?></h1>
<p><strong>Prix :</strong> <?= number_format($article['prix'], 2, ',', ' ') ?> €</p>
<p><strong>Notation :</strong><br><?= nl2br(htmlspecialchars($article['notation'])) ?></p>

<!-- Formulaire pour ajouter au panier -->
<form class="add-to-cart-form" data-id="<?= $article['id_article'] ?>">
    <button type="submit">Ajouter au panier 🛒</button>
</form>

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
                if (data.message === "Utilisateur non connecté") {
                    window.location.href = "account.php"; // 🔒 Redirection si pas connecté
                } else {
                    button.textContent = "Erreur";
                    setTimeout(() => {
                        button.disabled = false;
                        button.textContent = originalText;
                    }, 3000);
                }
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
