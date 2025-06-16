<?php
session_start();

// Liste de comptes (à remplacer plus tard par une table utilisateurs)
$utilisateurs = [
    'colin@example.com' => [
        'prenom' => 'Colin',
        'mdp' => '1234'
    ],
    'jeanne@example.com' => [
        'prenom' => 'Jeanne',
        'mdp' => 'azerty'
    ]
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['mdp'] ?? '';

    if (isset($utilisateurs[$email]) && $utilisateurs[$email]['mdp'] === $mdp) {
        $_SESSION['utilisateur'] = [
            'email' => $email,
            'prenom' => $utilisateurs[$email]['prenom']
        ];
        header("Location: account.php");
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    unset($_SESSION['utilisateur']);
    header("Location: account.php");
    exit;
}

require_once('header.php');
?>

<h1>🧍 Mon compte</h1>

<?php if (isset($_SESSION['utilisateur'])): ?>
    <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?></strong> !</p>
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
            <input type="password" name="mdp" required>
        </label><br><br>

        <button type="submit">Se connecter</button>
    </form>
<?php endif; ?>

<?php require_once('footer.php'); ?>

