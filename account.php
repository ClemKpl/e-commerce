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

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 15px;
    }

    label {
        display: flex;
        flex-direction: column;
        font-weight: bold;
    }

    input[type="email"], input[type="password"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    button {
        padding: 10px;
        background-color: #e9bcd3;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #d7a8c2;
    }

    .error-message {
        color: red;
        margin-top: 10px;
        font-weight: bold;
    }

    .create-account-link {
        margin-top: 20px;
        text-align: right;
    }

    .create-account-link a {
        color: #e9bcd3;
        text-decoration: none;
        font-weight: bold;
        transition: text-decoration 0.3s ease;
    }

    .create-account-link a:hover {
        text-decoration: underline;
    }

    .welcome-text {
        font-size: 1.1em;
        margin-bottom: 10px;
    }

    .logout-link {
        margin-bottom: 25px;
        display: inline-block;
        color: #e9bcd3;
        font-weight: bold;
        text-decoration: none;
        transition: text-decoration 0.3s ease;
    }

    .logout-link:hover {
        text-decoration: underline;
    }

    /* Commandes styling */
    .commandes h3 {
        margin-bottom: 15px;
        font-weight: bold;
        color: #333;
    }

    .commande-block {
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }

    th, td {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        text-align: left;
    }

    th {
        background: #eee;
    }

    .toggle-details {
        cursor: pointer;
        color: #e9bcd3;
        font-weight: bold;
        user-select: none;
        display: inline-block;
        margin-bottom: 5px;
        transition: color 0.3s ease;
    }

    .toggle-details:hover {
        color: #d39ac5;
    }

    .details {
        display: none;
        background: #f9f1f7;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .article-item {
        padding: 4px 0;
        font-style: italic;
        color: #555;
    }
</style>

<div class="account-container">
    <h1>🧍 Mon compte</h1>

    <?php if (isset($_SESSION['utilisateur'])): ?>
        <p class="welcome-text">
            Bienvenue, <strong><?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?></strong>
            <?php if (!empty($_SESSION['utilisateur']['admin'])): ?>
                <span style="color: #e9bcd3;">(admin)</span>
            <?php endif; ?>
        </p>
        <a href="account.php?logout=1" class="logout-link">Se déconnecter</a>

        <div class="commandes">
            <h3>📦 Mes commandes</h3>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id_client = :id ORDER BY date DESC");
            $stmt->execute(['id' => $_SESSION['utilisateur']['id']]);
            $commandes = $stmt->fetchAll();

            if ($commandes):
                foreach ($commandes as $commande):
                    $idCommande = $commande['id_commande'];

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
                <?php endforeach;
            else: ?>
                <p>Vous n'avez encore passé aucune commande.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <h2>Connexion</h2>
        <?php if (isset($erreur)): ?>
            <p class="error-message"><?= $erreur ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="login">
            <label>Email :
                <input type="email" name="email" required>
            </label>
            <label>Mot de passe :
                <input type="password" name="mdp" required placeholder="Tapez votre nom">
            </label>
            <button type="submit">Se connecter</button>
        </form>

        <div class="create-account-link">
            <a href="create_account.php">Pas encore de compte ? Créez-en un</a>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleDetails(elem) {
    const details = elem.nextElementSibling;
    const isVisible = details.style.display === 'block';
    details.style.display = isVisible ? 'none' : 'block';
    elem.textContent = isVisible ? '▶ Voir les articles' : '▼ Masquer les articles';
}
</script>

<?php require_once('footer.php'); ?>
