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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #111;
            color: #fff;
        }

        header {
            background: linear-gradient(90deg, #000 0%, #550055 100%);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 4px 12px rgba(255, 20, 147, 0.3);
        }

        .logo {
            font-size: 1.8em;
            font-weight: 700;
            color: #ff4ecb;
            text-shadow: 0 0 5px #ff4ecb;
        }

        nav {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            background-color: #ff4ecb;
            padding: 10px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 0 8px rgba(255, 20, 147, 0.5);
        }

        nav a:hover {
            background-color: #ff0099;
            box-shadow: 0 0 12px rgba(255, 0, 153, 0.7);
        }

        .container {
            padding: 30px;
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
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">🛍️ MonCatalogue</div>
    <nav>
        <a href="account.php"><?= $utilisateur ? 'Mon Compte' : 'Se connecter' ?></a>
        <a href="categories.php">Catégories</a>
        <a href="ajouter_article.php">➕ Ajouter</a>
        <a href="panier.php">Panier (<?= $panierCount ?>)</a>
    </nav>
</header>

<div class="container">
