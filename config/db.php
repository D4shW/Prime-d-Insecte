<?php
// config/db.php

$host = 'localhost';
$dbname = 'prime_insecte';
$username = 'root'; // Par défaut sur XAMPP/WAMP
$password = '';     // Laisse vide sur XAMPP, mets 'root' sur MAMP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configuration des erreurs : On veut que PHP nous crie dessus si SQL plante
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mode de récupération par défaut : Tableau associatif
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // En cas d'erreur, on arrête tout et on affiche le message
    die("❌ Erreur de connexion à la base de données : " . $e->getMessage());
}
?>