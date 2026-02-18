<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// 1. Sécurité : Il faut être connecté
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

// 2. Récupérer les infos à jour de l'utilisateur
$stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user_info = $stmt_user->fetch();

// 3. Récupérer les challenges achetés et résolus (Jointure SQL)
$stmt_chals = $pdo->prepare("
    SELECT c.title, c.category, c.difficulty, uc.purchased_at, uc.solved_at 
    FROM user_challenges uc
    JOIN challenges c ON uc.challenge_id = c.id
    WHERE uc.user_id = ?
    ORDER BY uc.purchased_at DESC
");
$stmt_chals->execute([$user_id]);
$my_challenges = $stmt_chals->fetchAll();

// 4. Calcul du score (Ex: 1 challenge résolu = 50 points)
$score = 0;
$solved_count = 0;
foreach ($my_challenges as $chal) {
    if ($chal['solved_at'] !== null) {
        $score += 50; 
        $solved_count++;
    }
}

// 5. Récupérer les factures (Invoices)
$stmt_inv = $pdo->prepare("SELECT * FROM invoices WHERE user_id = ? ORDER BY date DESC");
$stmt_inv->execute([$user_id]);
$invoices = $stmt_inv->fetchAll();
?>

<div style="max-width: 900px; margin: 0 auto;">
    
    <div style="background: #0f3460; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="margin: 0; color: #e94560;">👤 <?= htmlspecialchars($user_info['username']) ?></h1>
            <p style="color: #aaa; margin-top: 5px;">✉️ <?= htmlspecialchars($user_info['email']) ?></p>
            <span style="background: #533483; padding: 5px 10px; border-radius: 4px; font-size: 0.9em;">
                Rôle : <?= strtoupper($user_info['role']) ?>
            </span>
        </div>
        <div style="text-align: right;">
            <h2 style="margin: 0; color: #4cd137;">Score : <?= $score ?> pts</h2>
            <p style="margin-top: 5px;">💰 Solde : <?= number_format($user_info['balance'], 2) ?> €</p>
        </div>
    </div>

    <div style="background: #1a1a2e; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2>🎯 Mes Challenges (<?= count($my_challenges) ?>)</h2>
        
        <?php if (empty($my_challenges)): ?>
            <p>Vous n'avez acheté aucun challenge pour le moment.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 2px solid #533483; text-align: left;">
                    <th style="padding: 10px;">Titre</th>
                    <th style="padding: 10px;">Catégorie</th>
                    <th style="padding: 10px;">Acheté le</th>
                    <th style="padding: 10px;">Statut</th>
                </tr>
                <?php foreach ($my_challenges as $chal): ?>
                    <tr style="border-bottom: 1px solid #0f3460;">
                        <td style="padding: 10px;"><strong><?= htmlspecialchars($chal['title']) ?></strong></td>
                        <td style="padding: 10px;"><?= htmlspecialchars($chal['category']) ?></td>
                        <td style="padding: 10px;"><?= date('d/m/Y H:i', strtotime($chal['purchased_at'])) ?></td>
                        <td style="padding: 10px;">
                            <?php if ($chal['solved_at']): ?>
                                <span style="color: #4cd137; font-weight: bold;">✅ Résolu</span>
                            <?php else: ?>
                                <span style="color: #fbc531;">⏳ En cours</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <div style="background: #1a1a2e; padding: 20px; border-radius: 8px;">
        <h2>🧾 Mes Factures</h2>
        
        <?php if (empty($invoices)): ?>
            <p>Aucune facture disponible.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 2px solid #533483; text-align: left;">
                    <th style="padding: 10px;">N° Facture</th>
                    <th style="padding: 10px;">Date</th>
                    <th style="padding: 10px;">Montant</th>
                </tr>
                <?php foreach ($invoices as $inv): ?>
                    <tr style="border-bottom: 1px solid #0f3460;">
                        <td style="padding: 10px;">#INV-<?= str_pad($inv['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td style="padding: 10px;"><?= date('d/m/Y H:i', strtotime($inv['date'])) ?></td>
                        <td style="padding: 10px; color: #e94560; font-weight: bold;">
                            <?= number_format($inv['amount'], 2) ?> €
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>