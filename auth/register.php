<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$error = null;
$success = null;

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validations de base
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // 2. Vérifier si l'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        
        if ($stmt->fetch()) {
            $error = "Cet email ou ce pseudo est déjà utilisé.";
        } else {
            // 3. Création du compte
            // Hachage du mot de passe (Sécurité obligatoire !)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = $pdo->prepare("INSERT INTO users (username, email, password, role, balance) VALUES (?, ?, ?, 'user', 0.00)");
            
            if ($insert->execute([$username, $email, $hashed_password])) {
                $success = "Compte créé avec succès ! Vous pouvez vous connecter.";
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<div style="max-width: 400px; margin: 0 auto;">
    <h2>📝 Inscription</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= $success ?> <br>
            <a href="login.php">Cliquez ici pour vous connecter</a>
        </div>
    <?php else: ?>

    <form method="POST" action="">
        <div style="margin-bottom: 15px;">
            <label>Pseudo :</label><br>
            <input type="text" name="username" required style="width: 100%; padding: 8px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Email :</label><br>
            <input type="email" name="email" required style="width: 100%; padding: 8px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Mot de passe :</label><br>
            <input type="password" name="password" required style="width: 100%; padding: 8px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Confirmer le mot de passe :</label><br>
            <input type="password" name="confirm_password" required style="width: 100%; padding: 8px;">
        </div>

        <button type="submit" class="btn" style="width: 100%;">S'inscrire</button>
    </form>
    
    <p style="text-align: center; margin-top: 15px;">
        Déjà un compte ? <a href="login.php">Se connecter</a>
    </p>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>