<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$utilisateur = $_SESSION['utilisateur'] ?? null;
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
    <title>Mon site e-commerce</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: system-ui, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #ffffff;
            padding: 16px 24px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 1.5em;
            font-weight: 600;
            color: #d38cad;
        }

        nav {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        nav a {
            text-decoration: none;
            color: #555;
            background-color: #f3d1e0;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        nav a:hover {
            background-color: #e9bcd3;
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            nav {
                width: 100%;
                flex-direction: column;
            }
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 24px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <header>
        <div class="logo">💗 MonCatalogue</div>
        <nav>
            <a href="account.php"><?= $utilisateur ? 'Mon Compte' : 'Se connecter' ?></a>
            <a href="categories.php">Catégories</a>
            <a href="ajouter_article.php">➕ Ajouter</a>
            <a href="panier.php">Panier (<?= $panierCount ?>)</a>
        </nav>
    </header>
    <div class="container">
