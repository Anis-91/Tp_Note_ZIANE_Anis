<?php
// inscription.php
require __DIR__ . '/db.php';
require_once __DIR__ . '/classes/Utilisateur.php';
require_once __DIR__ . '/fonctions.php';

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse e-mail invalide.";
    } elseif (mb_strlen($motDePasse) < 12) {
        $erreur = "Le mot de passe doit contenir au moins 12 caractères.";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM php_utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $erreur = "Cette adresse e-mail est déjà utilisée.";
        } else {
            $hache = Utilisateur::hacherMotDePasse($motDePasse);
            $maintenant = date('Y-m-d H:i:s');

            $insert = $pdo->prepare("INSERT INTO php_utilisateurs (email, mot_de_passe_hache, cree_le) VALUES (:email, :hache, :cree)");
            $insert->execute([
                ':email' => $email,
                ':hache' => $hache,
                ':cree'  => $maintenant
            ]);

            header('Location: connexion.php?inscrit=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🐍 Snake Online - Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header-brand">
            <h1>🐍 SNAKE</h1>
            <p>Créer un compte</p>
        </div>

        <?php if ($erreur !== null): ?>
            <div class="alert alert-danger"><?= e($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="inscription.php">
            <div class="form-group">
                <label for="email">Adresse e-mail :</label>
                <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe (12 caractères minimum) :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="12">
            </div>
            <button type="submit">Créer mon compte</button>
        </form>

        <div class="links" style="justify-content: center; margin-top: 15px;">
            <a href="connexion.php">Déjà un compte ? Se connecter</a>
        </div>
    </div>
</body>
</html>
