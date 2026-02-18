<?php
session_start();
require_once '../config/db.php';

// 1. Sécurité de base
if (!isset($_SESSION['user']) || empty($_SESSION['cart']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

try {
    // 2. On récupère le solde RÉEL en BDD (plus sûr que la session)
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    $current_balance = $user['balance'];

    // 3. On recalcule le total RÉEL des objets du panier
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, price FROM challenges WHERE id IN ($placeholders)");
    $stmt->execute($_SESSION['cart']);
    $items = $stmt->fetchAll();

    $total_price = 0.00;
    foreach ($items as $item) {
        $total_price += $item['price'];
    }

    // 4. Vérification finale du solde
    if ($current_balance < $total_price) {
        die("Erreur de sécurité : Solde insuffisant.");
    }

    // ====== DÉBUT DE LA TRANSACTION SQL ======
    $pdo->beginTransaction();

    // A. Débiter l'utilisateur
    $new_balance = $current_balance - $total_price;
    $update_user = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $update_user->execute([$new_balance, $user_id]);

    // B. Créer la facture (Invoice)
    $insert_invoice = $pdo->prepare("INSERT INTO invoices (user_id, amount) VALUES (?, ?)");
    $insert_invoice->execute([$user_id, $total_price]);

    // C. Donner l'accès aux challenges (Table user_challenges)
    $insert_purchase = $pdo->prepare("INSERT INTO user_challenges (user_id, challenge_id) VALUES (?, ?)");
    foreach ($items as $item) {
        // On insère chaque challenge acheté
        $insert_purchase->execute([$user_id, $item['id']]);
    }

    // ====== FIN DE LA TRANSACTION SQL ======
    $pdo->commit();

    // 5. Mettre à jour la session
    $_SESSION['user']['balance'] = $new_balance;
    unset($_SESSION['cart']); // On vide le panier

    // 6. Afficher le succès
    require_once '../includes/header.php';
    echo "<div class='container' style='text-align: center; margin-top: 50px;'>";
    echo "<h1>✅ Paiement Validé !</h1>";
    echo "<p>Merci pour votre achat. Vous avez maintenant accès aux challenges.</p>";
    echo "<a href='../index.php' class='btn'>Aller hacker</a>";
    echo "</div>";
    require_once '../includes/footer.php';

} catch (Exception $e) {
    // En cas d'erreur (ex: coupure base de données), on annule tout !
    $pdo->rollBack();
    die("Erreur lors de la transaction : " . $e->getMessage());
}
?>