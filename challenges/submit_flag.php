<?php
session_start();
require_once '../config/db.php';

// 1. Sécurité de base
if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$challenge_id = (int)$_POST['challenge_id'];
$submitted_flag = trim($_POST['flag']); // On enlève les espaces en trop

// 2. Vérifier si l'utilisateur possède bien le challenge et ne l'a pas déjà résolu
$stmt = $pdo->prepare("SELECT * FROM user_challenges WHERE user_id = ? AND challenge_id = ?");
$stmt->execute([$user_id, $challenge_id]);
$purchase = $stmt->fetch();

if (!$purchase) {
    die("Triche détectée : Vous n'avez pas acheté l'accès à ce challenge !");
}
if ($purchase['solved_at'] !== null) {
    // Il l'a déjà réussi, on le renvoie sur la page
    header("Location: detail.php?id=$challenge_id");
    exit;
}

// 3. Récupérer le VRAI flag depuis la table challenges
$stmt_chal = $pdo->prepare("SELECT flag_code FROM challenges WHERE id = ?");
$stmt_chal->execute([$challenge_id]);
$challenge = $stmt_chal->fetch();

if (!$challenge) {
    die("Challenge introuvable.");
}

// 4. Comparaison des flags
// On fait une comparaison stricte (sensible aux majuscules/minuscules)
if ($submitted_flag === $challenge['flag_code']) {
    
    // ✅ C'EST GAGNÉ ! On met à jour la date de résolution
    $update = $pdo->prepare("UPDATE user_challenges SET solved_at = NOW() WHERE user_id = ? AND challenge_id = ?");
    $update->execute([$user_id, $challenge_id]);

    // Redirection avec un message de succès dans l'URL
    header("Location: detail.php?id=$challenge_id&success=1");
    exit;

} else {
    // ❌ MAUVAIS FLAG
    // Redirection avec un message d'erreur dans l'URL
    header("Location: detail.php?id=$challenge_id&error=1");
    exit;
}
?>