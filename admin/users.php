<?php
// admin/users.php
require_once '../config/db.php';
require_once 'function.php'; // On inclut notre nouveau fichier de fonctions

// 1. SÉCURITÉ : On appelle la fonction qu'on vient de créer
check_admin();

// 2. TRAITEMENT DES ACTIONS DE L'ADMIN
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    // Bannir (Supprimer le compte)
    if ($action === 'delete') {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    } 
    // Changer le rôle (user <-> admin)
    elseif ($action === 'toggle_role') {
        $pdo->prepare("UPDATE users SET role = IF(role='admin', 'user', 'admin') WHERE id = ?")->execute([$id]);
    } 
    // Remettre le solde à zéro
    elseif ($action === 'reset_balance') {
        $pdo->prepare("UPDATE users SET balance = 0 WHERE id = ?")->execute([$id]);
    }
    
    // Rafraîchir la page après l'action
    header("Location: users.php"); 
    exit;
}

// 3. RÉCUPÉRATION DE TOUS LES UTILISATEURS
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div style="max-width: 1000px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 style="color: #e94560;">👥 Gestion des Utilisateurs</h1>
        <a href="index.php" class="btn" style="background: #533483;">Retour au Dashboard Admin</a>
    </div>

    <div style="background: #1a1a2e; padding: 20px; border-radius: 8px; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <tr style="border-bottom: 2px solid #533483;">
                <th style="padding: 10px;">ID</th>
                <th style="padding: 10px;">Pseudo</th>
                <th style="padding: 10px;">Email</th>
                <th style="padding: 10px;">Rôle</th>
                <th style="padding: 10px;">Solde</th>
                <th style="padding: 10px;">Actions</th>
            </tr>
            
            <?php foreach ($users as $u): ?>
                <tr style="border-bottom: 1px solid #0f3460;">
                    <td style="padding: 10px;"><?= $u['id'] ?></td>
                    
                    <td style="padding: 10px; font-weight: bold;">
                        <a href="../user/profile.php?id=<?= $u['id'] ?>" style="color: #3498db;">
                            <?= htmlspecialchars($u['username']) ?>
                        </a>
                    </td>
                    
                    <td style="padding: 10px;"><?= htmlspecialchars($u['email']) ?></td>
                    
                    <td style="padding: 10px;">
                        <?php if($u['role'] === 'admin'): ?>
                            <span style="color: #e94560; font-weight: bold;">ADMIN</span>
                        <?php else: ?>
                            <span style="color: #aaa;">USER</span>
                        <?php endif; ?>
                    </td>
                    
                    <td style="padding: 10px; color: #4cd137; font-weight: bold;">
                        <?= number_format($u['balance'], 2) ?> €
                    </td>
                    
                    <td style="padding: 10px;">
                        <a href="?action=toggle_role&id=<?= $u['id'] ?>" class="btn" style="padding: 5px; font-size: 0.8em; background: #fbc531; color:#111; margin: 2px;">Changer Rôle</a>
                        
                        <a href="?action=reset_balance&id=<?= $u['id'] ?>" class="btn" style="padding: 5px; font-size: 0.8em; background: #888; margin: 2px;" onclick="return confirm('Remettre le solde à 0 ?');">Reset Solde</a>
                        
                        <?php if($_SESSION['user']['id'] !== $u['id']): // On empêche l'admin de se bannir lui-même ?>
                            <a href="?action=delete&id=<?= $u['id'] ?>" class="btn" style="padding: 5px; font-size: 0.8em; background: #c0392b; margin: 2px;" onclick="return confirm('Bannir définitivement cet utilisateur ?');">Bannir</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>