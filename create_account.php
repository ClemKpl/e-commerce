<?php
session_start();
require_once('header.php');

$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

$erreurInscription = '';
$successInscription = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $successInscription = "Compte créé avec succès ! <a href='account.php'>Connectez-vous</a>.";
        }
    } else {
        $erreurInscription = "Veuillez remplir tous les champs.";
    }
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
    }

    label {
        display: flex;
        flex-direction: column;
        font-weight: bold;
    }

    input[type="text"], input[type="email"] {
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
    }

    .error-message {
        color: red;
    }

    .success-message {
        color: green;
    }

    .create-account-link {
        margin-top: 20px;
    }

    .create-account-link a {
        color: #e9bcd3;
        text-decoration: none;
        font-weight: bold;
    }

    .create-account-link a:hover {
        text-decoration: underline;
    }
</style>

<div class="account-container">
    <h1>📝 Créer un compte</h1>

    <?php if ($erreurInscription): ?>
        <p class="error-message"><?= $erreurInscription ?></p>
    <?php elseif ($successInscription): ?>
        <p class="success-message"><?= $successInscription ?></p>
    <?php endif; ?>

    <?php if (!$successInscription): ?>
    <form method="post">
        <label>Nom :
            <input type="text" name="nom" required>
        </label>
        <label>Email :
            <input type="email" name="email" required>
        </label>
        <button type="submit">Créer mon compte</button>
    </form>
    <?php endif; ?>

    <div class="create-account-link">
        🔙 <a href="account.php">Retour à la connexion</a>
    </div>
</div>

<?php require_once('footer.php'); ?>
