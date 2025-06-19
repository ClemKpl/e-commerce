<?php
session_start();
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");
require_once('header.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Produit non trouvé.</p>";
    require_once('footer.php');
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id_article = :id");
$stmt->execute(['id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    echo "<p>Ce produit n'existe pas.</p>";
    require_once('footer.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM notation WHERE id_article = :id");
$stmt->execute(['id' => $id]);
$notations = $stmt->fetchAll();

$confirmation_avis = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'], $_POST['avis']) && isset($_SESSION['utilisateur'])) {
    $note = (int) $_POST['note'];
    $avis = trim($_POST['avis']);
    $id_client = $_SESSION['utilisateur']['id'];

    if ($note >= 1 && $note <= 5 && $avis !== '') {
        $stmt = $pdo->prepare("
            INSERT INTO notation (id_article, id_client, note, avis)
            VALUES (:id_article, :id_client, :note, :avis)
        ");
        $stmt->execute([
            'id_article' => $id,
            'id_client' => $id_client,
            'note' => $note,
            'avis' => $avis
        ]);
        $confirmation_avis = "✅ Merci pour votre évaluation !";
        $stmt = $pdo->prepare("SELECT * FROM notation WHERE id_article = :id");
        $stmt->execute(['id' => $id]);
        $notations = $stmt->fetchAll();
    } else {
        $confirmation_avis = "❌ Veuillez remplir correctement tous les champs.";
    }
}
?>

<style>
    .produit {
        max-width: 700px;
        margin: 40px auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.04);
    }

    .produit h1 {
        font-size: 1.8em;
        margin-bottom: 15px;
        color: #222;
    }

    .produit p {
        margin: 10px 0;
        font-size: 1em;
        color: #555;
    }

    .produit a {
        color: #d38cad;
        text-decoration: underline;
    }

    .avis {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .avis ul {
        padding-left: 20px;
        margin: 10px 0 0 0;
        color: #666;
        font-style: italic;
    }

    .add-to-cart-form button {
        background-color: #e9bcd3;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        color: #333;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s ease;
        margin-top: 16px;
    }

    .add-to-cart-form button:hover {
        background-color: #d7a8c2;
    }

    .add-to-cart-form button:disabled {
        background-color: #ddd;
        cursor: not-allowed;
    }

    .fournisseur-btn {
        display: inline-block;
        background:rgb(255, 217, 235);
        color: black;
        font-weight: 500;
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.3s ease;
    }

    .fournisseur-btn:hover {
        background:rgb(255, 174, 212);
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
            <strong>Avis des utilisateurs :</strong>
            <ul>
                <?php foreach ($notations as $note): ?>
                    <li>« <?= htmlspecialchars($note['avis']) ?> »</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <p>Aucune évaluation pour ce produit.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['utilisateur'])): ?>
        <form class="add-to-cart-form" data-id="<?= $article['id_article'] ?>">
            <button type="submit">Ajouter au panier 🛒</button>
        </form>
    <?php else: ?>
        <p style="margin-top: 20px;">
            <a href="account.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
                Se connecter pour ajouter au panier 🔐
            </a>
        </p>
    <?php endif; ?>

    <?php if (isset($_SESSION['utilisateur'])): ?>
        <div style="margin-top: 30px;">
            <h3 style="font-size:1.2em;color:#444;">📝 Laisser un avis</h3>

            <?php if ($confirmation_avis): ?>
                <p style="background:#f3d1e0;padding:10px;border-radius:6px;margin-top:10px;">
                    <?= htmlspecialchars($confirmation_avis) ?>
                </p>
            <?php endif; ?>

            <form method="post" style="display: flex; flex-direction: column; gap: 12px; max-width: 400px; margin-top: 16px;">
                <label>
                    Note :
                    <select name="note" required style="padding:10px;border:1px solid #ccc;border-radius:6px;">
                        <option value="">-- Choisir --</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?>/5</option>
                        <?php endfor; ?>
                    </select>
                </label>

                <label>
                    Avis :
                    <textarea name="avis" rows="4" required style="padding:10px;border:1px solid #ccc;border-radius:6px;"></textarea>
                </label>

                <button type="submit" style="background:#e9bcd3;padding:10px 16px;border-radius:6px;border:none;cursor:pointer;font-weight:500;">
                    Envoyer l'avis ✉️
                </button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (!empty($article['id_fournisseur'])): ?>
        <p style="margin-top: 30px;">
            <a href="fournisseurs.php?id=<?= $article['id_fournisseur'] ?>&produit_id=<?= $article['id_article'] ?>" class="fournisseur-btn">
                🏢 Voir le fournisseur
            </a>
        </p>
    <?php endif; ?>

    <p style="margin-top: 30px;">
        <a href="categories.php?categorie=<?= $article['id_categorie'] ?>">← Retour à la catégorie</a>
    </p>
</div>

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
