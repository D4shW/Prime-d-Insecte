<?php
// admin/function.php

function check_admin() {
    // Si la session n'est pas encore démarrée, on la démarre
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Vérification stricte du rôle
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        require_once '../includes/header.php';
        echo "<div class='container'>";
        echo "<div class='alert alert-error'>🚨 ALERTE INTRUSION : Accès refusé. Cette zone est strictement réservée aux administrateurs.</div>";
        echo "</div>";
        require_once '../includes/footer.php';
        exit; // Arrête l'exécution de la page
    }
}
?>