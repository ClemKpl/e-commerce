<?php
session_start();
require_once('header.php');

// Connexion à la base de données
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['mdp'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && strtolower($utilisateur['nom']) === strtolower($mdp)) {
        $_SESSION['utilisateur'] = [
            'id' => $utilisateur['id_client'],
            'nom' => $utilisateur['nom'],
            'email' => $utilisateur['email']
        ];
        header("Location: account.php");
        exit;
    } else {
        $erreur = "Identifiants incorrects.";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    unset($_SESSION['utilisateur']);
    header("Location: account.php");
    exit;
}
?>

<style>
    .account-container {
        max-width: 500px;
        margin: 40px auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
    }

    .account-container h1, .account-container h2 {
        margin-top: 0;
        color: #333;
    }

    .account-container p {
        font-size: 1em;
        color: #555;
    }

    .account-container a {
        color: #d38cad;
        text-decoration: underline;
        font-weight: 500;
    }

    .account-container form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .account-container input[type="email"],
    .account-container input[type="password"] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 1em;
        width: 100%;
    }

    .account-container button {
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

    .account-container button:hover {
        background-color: #d7a8c2;
    }

    .error-message {
        color: red;
        font-weight: 500;
    }
</style>

<div class="account-container">
    <h1>🧍 Mon compte</h1>

    <?php if (isset($_SESSION['utilisateur'])): ?>
        <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?></strong> !</p>
        <p><a href="account.php?logout=1">🔓 Se déconnecter</a></p>

    <?php else: ?>
        <h2>Connexion</h2>

        <?php if (isset($erreur)): ?>
            <p class="error-message"><?= $erreur ?></p>
        <?php endif; ?>

        <form method="post">
            <label>
                Email :
                <input type="email" name="email" required>
            </label>

            <label>
                Mot de passe :
                <input type="password" name="mdp" required placeholder="Tapez votre nom">
            </label>

            <button type="submit">Se connecter</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once('footer.php'); ?>
