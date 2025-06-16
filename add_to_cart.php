<?php
session_start();

header('Content-Type: application/json');

if (isset($_POST['id_article'])) {
    $id = (int) $_POST['id_article'];

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]++;
    } else {
        $_SESSION['panier'][$id] = 1;
    }

    // Compteur total du panier
    $total = array_sum($_SESSION['panier']);

    echo json_encode([
        'success' => true,
        'total' => $total
    ]);
    exit;
}

echo json_encode(['success' => false]);
