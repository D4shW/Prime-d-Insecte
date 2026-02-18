<?php
session_start();

// 1. Sécurité : Il faut être connecté
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

// 2. Vérification : A-t-on reçu un ID ?
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['challenge_id'])) {
    $challenge_id = (int)$_POST['challenge_id'];

    // 3. Création du panier s'il n'existe pas encore
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // 4. Ajouter le challenge au panier (seulement s'il n'y est pas déjà)
    if (!in_array($challenge_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $challenge_id;
    }
}

// 5. Redirection vers la page du panier
header('Location: index.php');
exit;