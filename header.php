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
        /* Style général de la page */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        /* En-tête du site (header) */
        header {
            background-color: #2c3e50; /* Bleu foncé */
            padding: 15px 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Titre ou logo */
        .logo {
            font-size: 1.4em;
            font-weight: bold;
        }

        /* Barre de navigation */
        nav {
            display: flex;
            gap: 15px; /* Espacement entre les liens */
            flex-wrap: wrap;
        }

        /* Style des liens de navigation */
        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            background-color: #34495e;
            padding: 8px 12px;
            border-radius: 5px;
        }

        /* Effet au survol */
        nav a:hover {
            background-color: #1abc9c;
        }

        /* Conteneur principal */
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        /* Adaptation mobile */
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

<!-- Début de l’en-tête -->
<header>
    <div class="logo">🛍️ MonCatalogue</div>
    <nav>
        <!-- Lien vers les catégories -->
        <a href="categories.php">Catégories</a>
        <a href="ajouter_article.php">➕ Ajouter</a>
        <a href="account.php">
            <?= $utilisateur ? 'Mon Compte' : 'Se connecter' ?>
        </a>

        <a href="panier.php">Panier (<?= $panierCount ?>)</a>
    </nav>
</header>

<!-- Contenu principal de la page -->
<div class="container">
