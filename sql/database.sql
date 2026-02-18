-- Création de la base de données
CREATE DATABASE IF NOT EXISTS prime_insecte CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE prime_insecte;
-- Table des Utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    balance DECIMAL(10, 2) DEFAULT 0.00,
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Table des Challenges
CREATE TABLE IF NOT EXISTS challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    -- ex: Web, Crypto, Pwn
    difficulty ENUM('Noob', 'Intermédiaire', 'Difficile', 'Insane') DEFAULT 'Noob',
    price DECIMAL(10, 2) DEFAULT 0.00,
    flag_code VARCHAR(255) NOT NULL,
    -- Le code secret à trouver
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Table des Factures (Invoice) - Préparation pour la phase 4
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Table des Achats (Liaison User <-> Challenge)
CREATE TABLE IF NOT EXISTS user_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    challenge_id INT NOT NULL,
    solved_at DATETIME NULL,
    purchased_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE
);
-- Insertion de 3 challenges de test (Pour voir quelque chose sur la page d'accueil)
INSERT INTO challenges (
        title,
        description,
        category,
        difficulty,
        price,
        flag_code
    )
VALUES (
        'Injection SQL Basique',
        'Trouvez le mot de passe admin via une faille SQL simple.',
        'Web',
        'Noob',
        10.00,
        'FLAG{SQL_IS_EASY}'
    ),
    (
        'Crypto César',
        'Déchiffrez ce message codé avec un décalage de 3.',
        'Crypto',
        'Intermédiaire',
        25.00,
        'FLAG{AVE_CAESAR}'
    ),
    (
        'Upload Fichier',
        'Uploadez un fichier PHP malveillant pour lire /etc/passwd.',
        'Web',
        'Difficile',
        100.00,
        'FLAG{RCE_MASTER}'
    );