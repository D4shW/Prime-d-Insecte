<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Sécurité : Réservé aux admins
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("<div class='container'><div class='alert alert-error'>Accès interdit.</div></div>");
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    
    $stmt = $pdo->prepare("UPDATE challenges SET title = ?, price = ? WHERE id = ?");
    $stmt->execute([$title, $price, $id]);
    echo "<div class='container'><div class='alert alert-success'>Modifié !</div></div>";
}

$chal = $pdo->prepare("SELECT * FROM challenges WHERE id = ?");
$chal->execute([$id]);
$data = $chal->fetch();
?>
<div style="max-width: 600px; margin: 0 auto; background: #0f3460; padding: 20px; border-radius: 8px;">
    <h2>✏️ Modifier le challenge</h2>
    <form method="POST">
        <label>Titre :</label>
        <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>" style="width:100%; padding:8px; margin-bottom:10px;">
        <label>Prix :</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($data['price']) ?>" style="width:100%; padding:8px; margin-bottom:10px;">
        <button type="submit" class="btn">Enregistrer les modifications</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>