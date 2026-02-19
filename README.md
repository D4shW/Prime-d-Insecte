# 🐞 Prime d'Insecte — Plateforme de Bug Bounty

**Prime d'Insecte** est une plateforme web de type **Bug Bounty / CTF** (Capture The Flag) développée en **PHP natif** avec **MySQL**. Les utilisateurs peuvent acheter l'accès à des challenges de cybersécurité, résoudre des vulnérabilités et soumettre des flags pour valider leurs compétences.

---

## 🚀 Fonctionnalités

- **Catalogue de challenges** — Parcourir et filtrer les challenges par catégorie (Web, Crypto, Pwn…) et difficulté (Noob, Intermédiaire, Difficile, Insane)
- **Système d'authentification** — Inscription, connexion et déconnexion sécurisées (mots de passe hachés avec `password_hash`)
- **Panier & achat** — Ajouter des challenges au panier, payer avec un solde virtuel et accéder aux labs
- **Soumission de flags** — Valider un challenge en soumettant le bon flag (`FLAG{...}`)
- **Profil utilisateur** — Consulter son score, ses challenges résolus et ses informations personnelles
- **Panneau d'administration** — Gérer les utilisateurs (rôles, bannissement, reset solde) et les challenges (création, édition, activation/désactivation, suppression)
- **Création de challenges** — Les utilisateurs connectés peuvent proposer leurs propres challenges
- **Système de facturation** — Table d'invoices préparée pour un suivi des transactions

---

## 🛠️ Stack technique

| Composant       | Technologie            |
|-----------------|------------------------|
| **Langage**     | PHP 7+                 |
| **Base de données** | MySQL / MariaDB    |
| **Serveur**     | Apache (XAMPP / WAMP / MAMP) |
| **Connexion BDD** | PDO (requêtes préparées) |
| **Frontend**    | HTML, CSS              |

---

## 📁 Arborescence du projet

```
Prime-d-Insecte/
├── index.php                  # Page d'accueil — liste des challenges
├── config/
│   └── db.php                 # Connexion PDO à la base de données
├── sql/
│   └── database.sql           # Script de création de la BDD et données de test
├── auth/
│   ├── login.php              # Formulaire de connexion
│   ├── register.php           # Formulaire d'inscription
│   └── logout.php             # Déconnexion (destruction de session)
├── challenges/
│   ├── index.php              # Redirection vers l'accueil
│   ├── detail.php             # Page détaillée d'un challenge
│   ├── create.php             # Création d'un nouveau challenge
│   ├── edit.php               # Édition d'un challenge existant
│   └── submit_flag.php        # Soumission et vérification d'un flag
├── cart/
│   ├── index.php              # Affichage du panier
│   ├── add.php                # Ajout d'un challenge au panier
│   └── validate.php           # Validation de la commande et paiement
├── user/
│   └── profile.php            # Profil utilisateur (public & privé)
├── admin/
│   ├── index.php              # Panneau d'administration principal
│   ├── function.php           # Fonctions utilitaires admin
│   └── users.php              # Gestion détaillée des utilisateurs
├── includes/
│   ├── header.php             # En-tête HTML + barre de navigation
│   ├── footer.php             # Pied de page HTML
│   └── function.php           # Fonctions utilitaires globales
└── assets/
    └── css/
        └── style.css          # Feuille de styles principale
```

---

## ⚙️ Installation

### Prérequis

- **PHP 7.0+**
- **MySQL** ou **MariaDB**
- **Apache** (via [XAMPP](https://www.apachefriends.org/), [WAMP](https://www.wampserver.com/) ou [MAMP](https://www.mamp.info/))

### Étapes

1. **Cloner le dépôt** dans le dossier `htdocs` (XAMPP) ou `www` (WAMP) :
   ```bash
   git clone https://github.com/<votre-utilisateur>/Prime-d-Insecte.git
   ```

2. **Créer la base de données** — Importer le fichier SQL via phpMyAdmin ou en ligne de commande :
   ```bash
   mysql -u root -p < sql/database.sql
   ```

3. **Configurer la connexion** — Modifier `config/db.php` si nécessaire :
   ```php
   $host     = 'localhost';
   $dbname   = 'prime_insecte';
   $username = 'root';
   $password = '';  // Vide sur XAMPP, 'root' sur MAMP
   ```

4. **Lancer le serveur** — Démarrer Apache et MySQL depuis le panneau XAMPP/WAMP.

5. **Accéder à l'application** :
   ```
   http://localhost/Prime-d-Insecte/
   ```

---

## 🗄️ Base de données

Le schéma comprend **4 tables** :

| Table              | Description                                      |
|--------------------|--------------------------------------------------|
| `users`            | Utilisateurs (pseudo, email, mot de passe, rôle, solde) |
| `challenges`       | Challenges de cybersécurité (titre, catégorie, difficulté, prix, flag) |
| `user_challenges`  | Liaison utilisateurs ↔ challenges (achat & résolution) |
| `invoices`         | Factures / historique des transactions            |

### Niveaux de difficulté

`Noob` → `Intermédiaire` → `Difficile` → `Insane`

### Rôles utilisateurs

- **user** — Peut parcourir, acheter et résoudre des challenges
- **admin** — Accès au panneau d'administration complet

---

## 🔐 Sécurité

- Mots de passe hachés avec `password_hash()` / `password_verify()`
- Requêtes SQL préparées via PDO (protection contre les injections SQL)
- Échappement des sorties HTML avec `htmlspecialchars()` (protection XSS)
- Vérification des rôles côté serveur pour l'accès admin
- Sessions PHP pour la gestion de l'authentification

---

## 📸 Aperçu

| Page | Description |
|------|-------------|
| **Accueil** | Grille de challenges avec catégorie, difficulté et prix |
| **Détail** | Description complète, achat et soumission de flag |
| **Panier** | Récapitulatif de la commande avec paiement |
| **Profil** | Score, challenges résolus et infos personnelles |
| **Admin** | Gestion des utilisateurs et des challenges |
