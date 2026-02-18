<?php
// 1. Connexion à la base de données
require_once 'config/db.php';

// 2. Récupération des challenges depuis la BDD
try {
    $stmt = $pdo->query("SELECT * FROM challenges WHERE is_active = 1 ORDER BY created_at DESC");
    $challenges = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des challenges : " . $e->getMessage());
}

// 3. Inclusion du header (visuel)
require_once 'includes/header.php';
?>

<h1>🎯 Challenges Disponibles</h1>
<p>Bienvenue sur la plateforme. Choisissez une cible et commencez le hacking !</p>

<div class="challenge-grid">
    <?php if (count($challenges) > 0): ?>
        <?php foreach ($challenges as $chal): ?>
            <div class="challenge-card">
                <h3><?= htmlspecialchars($chal['title']) ?></h3>
                <span style="color: #4cd137;">[<?= htmlspecialchars($chal['category']) ?>]</span>
                <span style="color: #fbc531;">★ <?= htmlspecialchars($chal['difficulty']) ?></span>
                
                <p><?= htmlspecialchars(substr($chal['description'], 0, 100)) ?>...</p>
                
                <div style="margin-top: 15px; font-weight: bold;">
                    Prix : <?= number_format($chal['price'], 2) ?> €
                </div>
                
                <a href="challenges/detail.php?id=<?= $chal['id'] ?>" class="btn">Voir détails</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun challenge disponible pour le moment.</p>
    <?php endif; ?>
</div>

<?php
// 4. Inclusion du footer
require_once 'includes/footer.php';
?>