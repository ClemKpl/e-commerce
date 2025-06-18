<?php
session_start();
require_once('header.php');

// Connexion à la base de données
$pdo = new PDO("mysql:host=10.96.16.82;dbname=magasin;charset=utf8", "colin", "");

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
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

// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nom && $email) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $erreurInscription = "Cet email est déjà utilisé.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO clients (nom, email) VALUES (:nom, :email)");
            $stmt->execute([
                'nom' => $nom,
                'email' => $email
            ]);
            $successInscription = "Compte créé avec succès ! Connectez-vous.";
        }
    } else {
        $erreurInscription = "Veuillez remplir tous les champs.";
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
        margin-bottom: 30px;
    }

    .account-container input[type="email"],
    .account-container input[type="password"],
    .account-container input[type="text"] {
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

    .success-message {
        color: green;
        font-weight: 500;
    }

    .historique {
        max-width: 800px;
        margin: 60px auto 30px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
    }

    .historique h2 {
        font-size: 1.5em;
        margin-bottom: 20px;
        color: #333;
    }

    .historique table {
        width: 100%;
        border-collapse: collapse;
    }

    .historique th,
    .historique td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    .historique th {
        background-color: #f9f9f9;
        color: #444;
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

<?php
// SECTION HISTORIQUE DES COMMANDES
if (isset($_SESSION['utilisateur'])) {
    $id_client = $_SESSION['utilisateur']['id'];
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id_client = :id ORDER BY date_commande DESC");
    $stmt->execute(['id' => $id_client]);
    $commandes = $stmt->fetchAll();

    if ($commandes):
?>
    <div class="historique">
        <h2>📦 Historique de vos commandes</h2>
        <table>
            <thead>
                <tr>
                    <th># Commande</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['id_commande']) ?></td>
                        <td><?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?></td>
                        <td><?= htmlspecialchars($commande['statut_livraison']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
    else:
        echo '<p style="text-align:center; margin-top:40px;">Vous n’avez passé aucune commande pour le moment.</p>';
    endif;
}
?>

<?php require_once('footer.php'); ?>
