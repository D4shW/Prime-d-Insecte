<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // 1. Récupérer l'utilisateur par son email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 2. Vérifier le mot de passe
        // password_verify compare le texte clair avec le hash en BDD
        if ($user && password_verify($password, $user['password'])) {
            // SUCCESS : On stocke les infos utiles en session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'balance' => $user['balance']
            ];

            // Redirection vers l'accueil
            header('Location: ../index.php');
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<div style="max-width: 400px; margin: 0 auto;">
    <h2>🔐 Connexion</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="margin-bottom: 15px;">
            <label>Email :</label><br>
            <input type="email" name="email" required style="width: 100%; padding: 8px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Mot de passe :</label><br>
            <input type="password" name="password" required style="width: 100%; padding: 8px;">
        </div>

        <button type="submit" class="btn" style="width: 100%;">Se connecter</button>
    </form>
    
    <p style="text-align: center; margin-top: 15px;">
        Pas encore de compte ? <a href="register.php">S'inscrire</a>
    </p>
</div>

<?php require_once '../includes/footer.php'; ?>