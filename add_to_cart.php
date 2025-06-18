<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    // Si l'appel n'est pas AJAX, on redirige vers la page de connexion
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        header("Location: account.php");
        exit;
    }

    // Sinon, si c'est un appel AJAX, on renvoie un JSON d'erreur
    header('Content-Type: application/json');
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

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'total' => $total
    ]);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Requête invalide']);
