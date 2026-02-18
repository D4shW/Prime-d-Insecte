<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// 1. Vérification : A-t-on bien un ID dans l'URL ? (ex: detail.php?id=1)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div class='container'><div class='alert alert-error'>Erreur : Challenge introuvable.</div></div>");
}

$challenge_id = (int)$_GET['id'];

// 2. Récupérer les informations du challenge dans la BDD
$stmt = $pdo->prepare("SELECT * FROM challenges WHERE id = ? AND is_active = 1");
$stmt->execute([$challenge_id]);
$challenge = $stmt->fetch();

// Si le challenge n'existe pas dans la base
if (!$challenge) {
    die("<div class='container'><div class='alert alert-error'>Ce challenge n'existe pas ou a été désactivé.</div></div>");
}

// 3. Vérifier le statut de l'utilisateur (Pour savoir quel bouton afficher)
$is_logged_in = isset($_SESSION['user']);
$has_purchased = false;
$has_solved = false;

if ($is_logged_in) {
    $user_id = $_SESSION['user']['id'];
    
    // On regarde dans la table user_challenges s'il possède ce challenge
    $stmt_check = $pdo->prepare("SELECT * FROM user_challenges WHERE user_id = ? AND challenge_id = ?");
    $stmt_check->execute([$user_id, $challenge_id]);
    $purchase = $stmt_check->fetch();
    
    if ($purchase) {
        $has_purchased = true;
        // Si la date de résolution n'est pas nulle, c'est qu'il l'a réussi
        if ($purchase['solved_at'] !== null) {
            $has_solved = true;
        }
    }
}
?>

<div style="max-width: 800px; margin: 0 auto; background: #0f3460; padding: 20px; border-radius: 8px;">
    <a href="../index.php" style="color: #e94560;">&larr; Retour aux challenges</a>
    
    <h1 style="margin-top: 15px;"><?= htmlspecialchars($challenge['title']) ?></h1>
    
    <div style="margin-bottom: 20px;">
        <span style="background: #4cd137; color: #111; padding: 5px 10px; border-radius: 4px; font-weight: bold;">
            <?= htmlspecialchars($challenge['category']) ?>
        </span>
        <span style="background: #fbc531; color: #111; padding: 5px 10px; border-radius: 4px; font-weight: bold; margin-left: 10px;">
            Difficulté : <?= htmlspecialchars($challenge['difficulty']) ?>
        </span>
        <span style="background: #e94560; color: #fff; padding: 5px 10px; border-radius: 4px; font-weight: bold; margin-left: 10px;">
            Prix : <?= number_format($challenge['price'], 2) ?> €
        </span>
    </div>

    <div style="background: #1a1a2e; padding: 15px; border-radius: 5px; margin-bottom: 20px; line-height: 1.6;">
        <h3 style="margin-top: 0;">Description du lab :</h3>
        <p><?= nl2br(htmlspecialchars($challenge['description'])) ?></p>
    </div>

    <div style="background: #16213e; padding: 20px; border-radius: 5px; text-align: center; border: 1px dashed #533483;">
        
        <?php if (!$is_logged_in): ?>
            <p>Vous devez être membre pour acheter ou résoudre ce challenge.</p>
            <a href="../auth/login.php" class="btn">Se connecter</a>
            <a href="../auth/register.php" class="btn" style="background: #533483;">S'inscrire</a>
            
        <?php elseif ($has_solved): ?>
            <h2 style="color: #4cd137; margin: 0;">🎉 Challenge Résolu !</h2>
            <p style="margin-bottom: 0;">Vous avez déjà trouvé le flag. Bien joué, hacker !</p>
            
        <?php elseif ($has_purchased): ?>
            <h3 style="margin-top: 0; color: #fbc531;">🚩 Soumettre le Flag</h3>
            <p>Vous avez l'accès ! Trouvez la vulnérabilité et validez le drapeau.</p>
            <form method="POST" action="submit_flag.php">
                <input type="hidden" name="challenge_id" value="<?= $challenge['id'] ?>">
                <input type="text" name="flag" placeholder="Ex: FLAG{...}" required style="width: 70%; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: none;">
                <br>
                <button type="submit" class="btn" style="background: #4cd137; color: #111; font-weight: bold;">Valider le Flag</button>
            </form>
            
        <?php else: ?>
            <h3 style="margin-top: 0;">Acheter l'accès au lab</h3>
            <p>Ce challenge coûte <strong><?= number_format($challenge['price'], 2) ?> €</strong>.</p>
            <form method="POST" action="../cart/add.php">
                <input type="hidden" name="challenge_id" value="<?= $challenge['id'] ?>">
                <button type="submit" class="btn">🛒 Ajouter au panier</button>
            </form>
        <?php endif; ?>
        
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>