<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// 1. Déterminer si on regarde son propre profil ou celui de quelqu'un d'autre
$is_public = isset($_GET['id']);
$target_id = $is_public ? (int)$_GET['id'] : ($_SESSION['user']['id'] ?? null);

if (!$target_id) {
    echo "<div class='container'><div class='alert alert-error'>Veuillez vous connecter.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

// 2. Récupérer les infos de l'utilisateur ciblé
$stmt_user = $pdo->prepare("SELECT id, username, email, role, balance, created_at FROM users WHERE id = ?");
$stmt_user->execute([$target_id]);
$user_info = $stmt_user->fetch();

if (!$user_info) {
    die("<div class='container'>Utilisateur introuvable.</div>");
}

// 3. Récupérer ses challenges résolus
$stmt_chals = $pdo->prepare("
    SELECT c.title, c.category, c.difficulty, uc.purchased_at, uc.solved_at 
    FROM user_challenges uc
    JOIN challenges c ON uc.challenge_id = c.id
    WHERE uc.user_id = ? AND uc.solved_at IS NOT NULL
");
$stmt_chals->execute([$target_id]);
$solved_challenges = $stmt_chals->fetchAll();

// Calcul du score (50 pts par challenge)
$score = count($solved_challenges) * 50;
?>

<div style="max-width: 900px; margin: 0 auto;">
    <div style="background: #0f3460; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between;">
        <div>
            <h1 style="margin: 0; color: #e94560;">👤 <?= htmlspecialchars($user_info['username']) ?></h1>
            <span style="background: #533483; padding: 5px 10px; border-radius: 4px; font-size: 0.9em;">
                Rôle : <?= strtoupper($user_info['role']) ?>
            </span>
            <p style="color: #aaa; margin-top: 10px;">Membre depuis le <?= date('d/m/Y', strtotime($user_info['created_at'])) ?></p>
        </div>
        <div style="text-align: right;">
            <h2 style="margin: 0; color: #4cd137;">Score : <?= $score ?> pts</h2>
            <p style="color: #fbc531;">🏆 <?= count($solved_challenges) ?> Challenges résolus</p>
        </div>
    </div>

    <?php if (!$is_public || (isset($_SESSION['user']) && $_SESSION['user']['id'] === $user_info['id'])): ?>
        <div style="background: #1a1a2e; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px dashed #e94560;">
            <h3 style="margin-top:0;">🔒 Informations privées</h3>
            <p><strong>Email :</strong> <?= htmlspecialchars($user_info['email']) ?></p>
            <p><strong>Solde :</strong> <?= number_format($user_info['balance'], 2) ?> €</p>
            <a href="../challenges/create.php" class="btn" style="background: #fbc531; color: #111;">➕ Créer un challenge</a>
        </div>
    <?php endif; ?>

    <div style="background: #1a1a2e; padding: 20px; border-radius: 8px;">
        <h2>🎯 Challenges Résolus</h2>
        <?php if (empty($solved_challenges)): ?>
            <p>Aucun challenge résolu pour le moment.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($solved_challenges as $chal): ?>
                    <li style="margin-bottom: 10px;">
                        <strong><?= htmlspecialchars($chal['title']) ?></strong> 
                        <span style="color: #4cd137;">(Résolu le <?= date('d/m/Y', strtotime($chal['solved_at'])) ?>)</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>