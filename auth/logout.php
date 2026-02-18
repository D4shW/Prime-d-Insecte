<?php
session_start();
// On détruit toutes les données de session
session_destroy();
// On redirige vers l'accueil
header("Location: ../index.php");
exit;
?>