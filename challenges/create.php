<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Sécurité
if (!isset($_SESSION['user'])) {
    die("<div class='container'><div class='alert alert-error'>Accès refusé. Connectez-vous.</div></div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $cat = trim($_POST['category']);
    $diff = trim($_POST['difficulty']);
    $price = (float)$_POST['price'];
    $flag = trim($_POST['flag_code']);

    $stmt = $pdo->prepare("INSERT INTO challenges (title, description, category, difficulty, price, flag_code, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
    if ($stmt->execute([$title, $desc, $cat, $diff, $price, $flag])) {
        echo "<div class='container'><div class='alert alert-success'>Challenge créé avec succès !</div></div>";
    }
}
?>
<div style="max-width: 600px; margin: 0 auto; background: #0f3460; padding: 20px; border-radius: 8px;">
    <h2>➕ Créer un nouveau Challenge</h2>
    <form method="POST">
        <label>Titre :</label><br>
        <input type="text" name="title" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>
        
        <label>Description :</label><br>
        <textarea name="description" required rows="5" style="width: 100%; padding: 8px; margin-bottom: 10px;"></textarea><br>
        
        <label>Catégorie :</label><br>
        <select name="category" style="width: 100%; padding: 8px; margin-bottom: 10px;">
            <option value="Web">Web</option>
            <option value="Crypto">Crypto</option>
            <option value="Pwn">Pwn</option>
            <option value="Forensic">Forensic</option>
        </select><br>

        <label>Difficulté :</label><br>
        <select name="difficulty" style="width: 100%; padding: 8px; margin-bottom: 10px;">
            <option value="Noob">Noob</option>
            <option value="Intermédiaire">Intermédiaire</option>
            <option value="Difficile">Difficile</option>
            <option value="Insane">Insane</option>
        </select><br>

        <label>Prix (€) :</label><br>
        <input type="number" step="0.01" name="price" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><br>

        <label>Le Flag à trouver (ex: FLAG{MON_CODE}) :</label><br>
        <input type="text" name="flag_code" required style="width: 100%; padding: 8px; margin-bottom: 20px;"><br>

        <button type="submit" class="btn" style="width: 100%;">Mettre en ligne</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>