<?php
session_start();
require_once('header.php');

$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['mdp'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && strtolower($utilisateur['nom']) === strtolower($mdp)) {
        $_SESSION['utilisateur'] = [
            'id' => $utilisateur['id_client'],
            'nom' => $utilisateur['nom'],
            'email' => $utilisateur['email'],
            'admin' => $utilisateur['admin'] ?? 0
        ];
        header("Location: account.php");
        exit;
    } else {
        $erreur = "Identifiants incorrects.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'register') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nom && $email) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $erreurInscription = "Cet email est déjà utilisé.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO clients (nom, email) VALUES (:nom, :email)");
            $stmt->execute(['nom' => $nom, 'email' => $email]);
            $successInscription = "Compte créé avec succès ! Connectez-vous.";
        }
    } else {
        $erreurInscription = "Veuillez remplir tous les champs.";
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['utilisateur']);
    header("Location: account.php");
    exit;
}
?>

<style>
    .account-container {
        max-width: 700px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
    }

    .commandes table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .commandes th, .commandes td {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        text-align: left;
    }

    .commandes th {
        background: #eee;
    }

    .toggle-details {
        cursor: pointer;
        color: #b10000;
        font-size: 18px;
        user-select: none;
        margin-left: 5px;
    }

    .details {
        display: none;
        background: #f1f1f1;
        margin-top: 5px;
        padding: 10px;
        border-radius: 6px;
    }

    .article-item {
        padding: 5px 0;
    }

    .rotate {
        transform: rotate(90deg);
    }
</style>

<div class="account-container">
    <h1>🧍 Mon compte</h1>

    <?php if (isset($_SESSION['utilisateur'])): ?>
        <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?></strong>
            <?php if (!empty($_SESSION['utilisateur']['admin'])): ?>
                <span style="color: #b10000;">(admin)</span>
            <?php endif; ?>
        </p>
        <p><a href="account.php?logout=1">🔓 Se déconnecter</a></p>

        <div class="commandes">
            <h3>📦 Mes commandes</h3>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id_client = :id ORDER BY date DESC");
            $stmt->execute(['id' => $_SESSION['utilisateur']['id']]);
            $commandes = $stmt->fetchAll();

            if ($commandes):
                foreach ($commandes as $commande):
                    $idCommande = $commande['id_commande'];

                    // Récupération des articles liés
                    $stmtArt = $pdo->prepare("
                        SELECT a.produit, a.prix 
                        FROM articles_commandes ac
                        JOIN articles a ON ac.id_article = a.id_article
                        WHERE ac.id_commande = :id_commande
                    ");
                    $stmtArt->execute(['id_commande' => $idCommande]);
                    $articles = $stmtArt->fetchAll();
                    ?>
                    <div class="commande-block">
                        <table>
                            <tr>
                                <td><strong>#<?= $commande['id_commande'] ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($commande['date'])) ?></td>
                                <td><?= htmlspecialchars($commande['statut_livraison']) ?></td>
                            </tr>
                        </table>
                        <div>
                            <span class="toggle-details" onclick="toggleDetails(this)">▶ Voir les articles</span>
                            <div class="details">
                                <?php if ($articles): ?>
                                    <?php foreach ($articles as $article): ?>
                                        <div class="article-item">
                                            🛍️ <?= htmlspecialchars($article['produit']) ?> — <?= number_format($article['prix'], 2) ?> €
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div>Aucun article trouvé pour cette commande.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                <?php endforeach;
            else: ?>
                <p>Vous n'avez encore passé aucune commande.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <h2>Connexion</h2>
        <?php if (isset($erreur)): ?><p class="error-message"><?= $erreur ?></p><?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="login">
            <label>Email : <input type="email" name="email" required></label>
            <label>Mot de passe : <input type="password" name="mdp" required placeholder="Tapez votre nom"></label>
            <button type="submit">Se connecter</button>
        </form>

        <h2>Créer un compte</h2>
        <?php if (isset($erreurInscription)): ?>
            <p class="error-message"><?= $erreurInscription ?></p>
        <?php elseif (isset($successInscription)): ?>
            <p class="success-message"><?= $successInscription ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="register">
            <label>Nom : <input type="text" name="nom" required></label>
            <label>Email : <input type="email" name="email" required></label>
            <button type="submit">Créer mon compte</button>
        </form>
    <?php endif; ?>
</div>

<script>
function toggleDetails(elem) {
    const details = elem.nextElementSibling;
    details.style.display = details.style.display === 'block' ? 'none' : 'block';
    elem.textContent = details.style.display === 'block' ? '▼ Masquer les articles' : '▶ Voir les articles';
}
</script>

<?php require_once('footer.php'); ?>
