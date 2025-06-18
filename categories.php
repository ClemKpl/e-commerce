<?php
session_start(); // Ajout essentiel
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");
require_once('header.php');

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$articles = [];

if (isset($_GET['categorie'])) {
    $categorieId = (int) $_GET['categorie'];
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id_categorie = :id_categorie");
    $stmt->execute(['id_categorie' => $categorieId]);
    $articles = $stmt->fetchAll();
}

$notations = [];
if (!empty($articles)) {
    $ids = implode(',', array_column($articles, 'id_article'));
    $stmt = $pdo->query("SELECT * FROM notation WHERE id_article IN ($ids)");
    $allNotes = $stmt->fetchAll();

    foreach ($allNotes as $note) {
        $id = $note['id_article'];
        if (!isset($notations[$id])) $notations[$id] = [];
        $notations[$id][] = $note;
    }
}
?>

<style>
    h1, h2 {
        color: #333;
        font-weight: 600;
        margin-top: 30px;
    }

    .categories {
        margin: 20px 0;
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
    }

    .categories a {
        display: inline-block;
        background-color: #f3d1e0;
        color: #444;
        padding: 8px 14px;
        margin: 5px 10px 5px 0;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }

    .categories a:hover {
        background-color: #e9bcd3;
    }

    .article {
        background-color: #ffffff;
        border: 1px solid #e5e5e5;
        padding: 20px;
        margin-bottom: 24px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .article strong a {
        color: #222;
        text-decoration: none;
        font-size: 1.1em;
    }

    .article strong a:hover {
        text-decoration: underline;
    }

    .article p {
        margin: 8px 0;
        color: #555;
        font-size: 0.95em;
    }

    .article ul {
        padding-left: 20px;
        margin: 10px 0;
        color: #666;
        font-style: italic;
    }

    .add-to-cart-form button {
        background-color: #e9bcd3;
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
        color: #333;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .add-to-cart-form button:hover {
        background-color: #d7a8c2;
    }

    .add-to-cart-form button:disabled {
        background-color: #ddd;
        cursor: not-allowed;
    }

    a[href*="account.php"] {
        color: #d38cad;
        text-decoration: underline;
        font-weight: 500;
    }
</style>

<h1>Catégories</h1>

<div class="categories">
    <?php foreach ($categories as $cat): ?>
        <a href="?categorie=<?= $cat['id_categorie'] ?>">
            <?= htmlspecialchars($cat['nom']) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (isset($_GET['categorie'])): ?>
    <h2>Articles de la catégorie :
        <?= htmlspecialchars($categories[array_search($categorieId, array_column($categories, 'id_categorie'))]['nom']) ?>
    </h2>

    <?php if (count($articles) === 0): ?>
        <p>Aucun article dans cette catégorie.</p>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="article">
                <strong>
                    <a href="produit.php?id=<?= $article['id_article'] ?>">
                        <?= htmlspecialchars($article['produit']) ?>
                    </a>
                </strong>
                <br>Prix : <?= number_format($article['prix'], 2, ',', ' ') ?> €

                <?php
                $id = $article['id_article'];
                if (isset($notations[$id])) {
                    $notes = array_column($notations[$id], 'note');
                    $moyenne = round(array_sum($notes) / count($notes), 1);
                    echo "<p>Note moyenne : <strong>$moyenne/5</strong></p>";
                    echo "<ul>";
                    foreach ($notations[$id] as $n) {
                        echo "<li><em>« " . htmlspecialchars($n['avis']) . " »</em></li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>Aucune évaluation</p>";
                }
                ?>

                <?php if (isset($_SESSION['utilisateur'])): ?>
                    <form class="add-to-cart-form" data-id="<?= $article['id_article'] ?>" style="margin-top: 10px;">
                        <button type="submit">Ajouter au panier 🛒</button>
                    </form>
                <?php else: ?>
                    <p style="margin-top: 10px;">
                        <a href="account.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
                            Se connecter pour ajouter au panier 🔐
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($_SESSION['utilisateur'])): ?>
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
<?php endif; ?>

<?php require_once('footer.php'); ?>
