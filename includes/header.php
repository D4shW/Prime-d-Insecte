<?php
// On s'assure que la session est démarrée partout
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Définition de la racine du site pour éviter les problèmes de liens
// (Adapte '/Prime-d-Insecte' si ton dossier s'appelle autrement)
$base_url = '/Prime-d-Insecte';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime d'Insecte | Bug Bounty</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>

<nav>
    <div class="logo">
        <a href="<?= $base_url ?>/index.php" style="color:#e94560; text-decoration:none;">
            🐞 Prime d'Insecte
        </a>
    </div>
    <div class="menu">
        <a href="<?= $base_url ?>/index.php">Challenges</a>
        
        <?php if (isset($_SESSION['user'])): ?>
            <span style="color: #4cd137; margin: 0 10px;">
                💰 <?= number_format($_SESSION['user']['balance'], 2) ?> €
            </span>
            
            <a href="<?= $base_url ?>/cart/index.php">Panier</a>
            <a href="<?= $base_url ?>/user/profile.php">Mon Profil</a>
            
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="<?= $base_url ?>/admin/index.php" style="color: #e94560; border: 1px solid #e94560; padding: 5px 10px; border-radius: 4px;">ADMIN</a>
            <?php endif; ?>

            <a href="<?= $base_url ?>/auth/logout.php" style="color: #888;">Déconnexion</a>
        <?php else: ?>
            <a href="<?= $base_url ?>/auth/login.php">Connexion</a>
            <a href="<?= $base_url ?>/auth/register.php" class="btn" style="padding: 5px 10px; margin-top:0;">Inscription</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">