<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Sécurité : Rediriger si non connecté
if (!isset($_SESSION['user'])) {
    echo "<div class='container'><div class='alert alert-error'>Veuillez vous connecter.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

// Gérer la suppression d'un article du panier
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    if (($key = array_search($remove_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
    }
    header('Location: index.php');
    exit;
}

// Récupérer les articles du panier
$cart_items = [];
$total_price = 0.00;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Créer une suite de "?" pour la requête SQL (ex: "?, ?, ?")
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT * FROM challenges WHERE id IN ($placeholders)");
    $stmt->execute($_SESSION['cart']);
    $cart_items = $stmt->fetchAll();

    // Calcul du total
    foreach ($cart_items as $item) {
        $total_price += $item['price'];
    }
}
?>

<div style="max-width: 800px; margin: 0 auto; background: #0f3460; padding: 20px; border-radius: 8px;">
    <h1>🛒 Mon Panier</h1>

    <?php if (empty($cart_items)): ?>
        <p>Votre panier est vide.</p>
        <a href="../index.php" class="btn">Parcourir les challenges</a>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="border-bottom: 2px solid #533483; text-align: left;">
                <th style="padding: 10px;">Challenge</th>
                <th style="padding: 10px;">Catégorie</th>
                <th style="padding: 10px;">Prix</th>
                <th style="padding: 10px;">Action</th>
            </tr>
            <?php foreach ($cart_items as $item): ?>
                <tr style="border-bottom: 1px solid #1a1a2e;">
                    <td style="padding: 10px;"><?= htmlspecialchars($item['title']) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars($item['category']) ?></td>
                    <td style="padding: 10px; font-weight: bold; color: #4cd137;"><?= number_format($item['price'], 2) ?> €</td>
                    <td style="padding: 10px;">
                        <a href="index.php?remove=<?= $item['id'] ?>" style="color: #e94560; font-size: 0.9em;">Retirer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div style="background: #1a1a2e; padding: 15px; border-radius: 5px; text-align: right;">
            <h2 style="margin: 0;">Total : <span style="color: #4cd137;"><?= number_format($total_price, 2) ?> €</span></h2>
            <p>Votre solde actuel : <strong><?= number_format($_SESSION['user']['balance'], 2) ?> €</strong></p>
            
            <?php if ($_SESSION['user']['balance'] >= $total_price): ?>
                <form method="POST" action="validate.php" style="margin-top: 15px;">
                    <button type="submit" class="btn" style="background: #4cd137; color: #111; font-weight: bold; font-size: 1.1em; padding: 10px 20px;">
                        💳 Payer et Valider
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-error" style="display: inline-block; margin-top: 15px;">
                    Solde insuffisant pour valider la commande.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>