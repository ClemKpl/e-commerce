<?php
// Lancement de session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Utilisateur connecté ou non
$utilisateur = $_SESSION['utilisateur'] ?? null;

// Compter les articles du panier lié au compte uniquement
$panierCount = 0;
if ($utilisateur) {
    $idClient = $utilisateur['id'];
    if (isset($_SESSION['panier'][$idClient])) {
        $panierCount = array_sum($_SESSION['panier'][$idClient]);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        header {
            background-color: #2c3e50;
            padding: 15px 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 1.4em;
            font-weight: bold;
        }

        nav {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            background-color: #34495e;
            padding: 8px 12px;
            border-radius: 5px;
        }

        nav a:hover {
            background-color: #1abc9c;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }
            nav {
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">🛍️ MonCatalogue</div>
    <nav>
        <a href="categories.php">Catégories</a>

        <a href="account.php">
            <?= $utilisateur ? 'Mon Compte' : 'Se connecter' ?>
        </a>

        <a href="panier.php">Panier (<?= $panierCount ?>)</a>
    </nav>
</header>

<div class="container">
