<?php
session_start();

header('Content-Type: application/json');

// Vérifie que l’utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$id_client = $_SESSION['utilisateur']['id'];

if (isset($_POST['id_article'])) {
    $id_article = (int) $_POST['id_article'];

    // Initialise la structure
    if (!isset($_SESSION['panier'][$id_client])) {
        $_SESSION['panier'][$id_client] = [];
    }

    // Ajoute ou incrémente l'article
    if (isset($_SESSION['panier'][$id_client][$id_article])) {
        $_SESSION['panier'][$id_client][$id_article]++;
    } else {
        $_SESSION['panier'][$id_client][$id_article] = 1;
    }

    // Compte total pour l'affichage dans le header
    $total = array_sum($_SESSION['panier'][$id_client]);

    echo json_encode(['success' => true, 'total' => $total]);
    exit;
}

echo json_encode(['success' => false]);
