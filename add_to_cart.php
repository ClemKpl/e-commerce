<?php
session_start();
header('Content-Type: application/json');

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$idClient = $_SESSION['utilisateur']['id'];

if (isset($_POST['id_article'])) {
    $idArticle = (int) $_POST['id_article'];

    // Initialise le panier du client si nécessaire
    if (!isset($_SESSION['panier'][$idClient])) {
        $_SESSION['panier'][$idClient] = [];
    }

    // Ajoute ou incrémente l'article
    if (isset($_SESSION['panier'][$idClient][$idArticle])) {
        $_SESSION['panier'][$idClient][$idArticle]++;
    } else {
        $_SESSION['panier'][$idClient][$idArticle] = 1;
    }

    // Compte le total pour ce client
    $total = array_sum($_SESSION['panier'][$idClient]);

    echo json_encode([
        'success' => true,
        'total' => $total
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Requête invalide']);
