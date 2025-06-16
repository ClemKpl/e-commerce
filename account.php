<?php
session_start();
require_once('header.php');

// Connexion à ta base
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['mdp'] ?? '';

    // Préparation de la requête
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $utilisateur = $stmt->fetch();

    // Vérifie que le mot de passe correspond au nom
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

<h1>🧍 Mon compte</h1>

<?php if (isset($_SESSION['utilisateur'])): ?>
    <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?></strong> !</p>
    <p><a href="account.php?logout=1">🔓 Se déconnecter</a></p>

<?php else: ?>
    <h2>Connexion</h2>

    <?php if (isset($erreur)): ?>
        <p style="color: red"><?= $erreur ?></p>
    <?php endif; ?>

    <form method="post" style="max-width: 300px;">
        <label>Email :<br>
            <input type="email" name="email" required>
        </label><br><br>

        <label>Mot de passe :<br>
            <input type="password" name="mdp" required placeholder="Tapez votre nom">
        </label><br><br>

        <button type="submit">Se connecter</button>
    </form>
<?php endif; ?>

<?php require_once('footer.php'); ?>
