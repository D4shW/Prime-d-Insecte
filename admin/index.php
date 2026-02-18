<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// 1. SÉCURITÉ ABSOLUE : Vérifier le rôle ADMIN [cite: 140]
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("<div class='container'><div class='alert alert-error'>Accès refusé. Zone réservée aux administrateurs.</div></div>");
}

// --- TRAITEMENT DES ACTIONS ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'delete_user') {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    } elseif ($action === 'toggle_role') {
        $pdo->prepare("UPDATE users SET role = IF(role='admin', 'user', 'admin') WHERE id = ?")->execute([$id]);
    } elseif ($action === 'reset_balance') {
        $pdo->prepare("UPDATE users SET balance = 0 WHERE id = ?")->execute([$id]);
    } elseif ($action === 'toggle_chal') {
        $pdo->prepare("UPDATE challenges SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    } elseif ($action === 'delete_chal') {
        $pdo->prepare("DELETE FROM challenges WHERE id = ?")->execute([$id]);
    }
    header("Location: index.php"); // Rafraîchir
    exit;
}

// Récupération des données
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$challenges = $pdo->query("SELECT * FROM challenges ORDER BY created_at DESC")->fetchAll();
?>

<div style="max-width: 1000px; margin: 0 auto;">
    <h1 style="color: #e94560;">⚙️ Panneau d'Administration</h1>

    <div style="background: #1a1a2e; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2>👥 Gestion des Utilisateurs</h2>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <tr style="border-bottom: 2px solid #533483;">
                <th>ID</th><th>Pseudo</th><th>Email</th><th>Rôle</th><th>Solde</th><th>Actions</th>
            </tr>
            <?php foreach ($users as $u): ?>
                <tr style="border-bottom: 1px solid #0f3460;">
                    <td><?= $u['id'] ?></td>
                    <td><a href="../user/profile.php?id=<?= $u['id'] ?>" style="color: #4cd137;"><?= htmlspecialchars($u['username']) ?></a></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= strtoupper($u['role']) ?></td>
                    <td><?= $u['balance'] ?> €</td>
                    <td>
                        <a href="?action=toggle_role&id=<?= $u['id'] ?>" class="btn" style="padding: 5px; font-size: 0.8em; background: #fbc531; color:#111;">Rôle</a>
                        <a href="?action=reset_balance&id=<?= $u['id'] ?>" class="btn" style="padding: 5px; font-size: 0.8em; background: #888;">Reset Solde</a>
                        <a href="?action=delete_user&id=<?= $u['id'] ?>" class="btn" style="padding: 5px; font-size: 0.8em;" onclick="return confirm('Sûr ?');">Bannir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div style="background: #1a1a2e; padding: 20px; border-radius: 8px;">
        <h2>🎯 Gestion des Challenges</h2>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <tr style="border-bottom: 2px solid #533483;">
                <th>ID</th><th>Titre</th><th>Prix</th><th>Statut</th><th>Actions</th>
            </tr>
            <?php foreach ($challenges as $c): ?>
                <tr style="border-bottom: 1px solid #0f3460;">
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['title']) ?></td>
                    <td><?= $c['price'] ?> €</td>
                    <td><?= $c['is_active'] ? '✅ Actif' : '❌ Désactivé' ?></td>
                    <td>
                        <a href="../challenges/edit.php?id=<?= $c['id'] ?>" class="btn" style="padding: 5px; font-size: 0.8em; background: #3498db;">Editer</a>
                        <a href="?action=toggle_chal&id=<?= $c['id'] ?>" class="btn" style="padding: 5px; font-size: 0.8em; background: #fbc531; color:#111;">Désactiver</a>
                        <a href="?action=delete_chal&id=<?= $c['id'] ?>" class="btn" style="padding: 5px; font-size: 0.8em;" onclick="return confirm('Sûr ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>