<?php
// connexion.php
require __DIR__ . '/db.php';
require_once __DIR__ . '/classes/Utilisateur.php';
require_once __DIR__ . '/fonctions.php';
require_once __DIR__ . '/verrouillage.php';

require __DIR__ . '/session.php';
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));

    // 1. Verrouillage si trop d'échecs
    if (est_verrouille($pdo, $email)) {
        http_response_code(429);
        exit("Trop de tentatives. Réessayez dans 15 minutes.");
    }

    $requete = $pdo->prepare("SELECT * FROM php_utilisateurs WHERE email = :email");
    $requete->execute([':email' => $email]);
    $ligne = $requete->fetch(PDO::FETCH_ASSOC);

    $ok = false;
    if ($ligne !== false) {
        $utilisateur = new Utilisateur($ligne['email'], $ligne['mot_de_passe_hache'], (int) $ligne['id']);
        $ok = $utilisateur->verifierMotDePasse($_POST['mot_de_passe'] ?? '');
    } else {
        // 2. Temps de réponse égalisé si compte inexistant
        password_verify($_POST['mot_de_passe'] ?? '', HASH_FACTICE);
    }

    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['utilisateur_id'] = $utilisateur->getId();

        $maintenant = date('c');
        $id         = $utilisateur->getId();

        $maj = $pdo->prepare("UPDATE php_utilisateurs SET derniere_connexion = :d WHERE id = :id");
        $maj->bindParam(':d',  $maintenant, PDO::PARAM_STR);
        $maj->bindParam(':id', $id,         PDO::PARAM_INT);
        $maj->execute();

        header('Location: espace.php');
        exit;
    }

    // 3. Enregistrement de l'échec
    enregistrer_echec($pdo, $email);
    $erreur = "Adresse e-mail ou mot de passe incorrect.";
    error_log("Échec de connexion (tentative sur un compte)");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🐍 Snake Online - Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header-brand">
            <h1>🐍 SNAKE</h1>
            <p>Jeu Snake multijoueur en ligne</p>
        </div>

        <?php if ($erreur !== null): ?>
            <div class="alert alert-danger"><?= e($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="connexion.php">
            <div class="form-group">
                <label for="email">Adresse e-mail :</label>
                <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>

        <div class="links">
            <a href="inscription.php">Créer un compte</a>
            <a href="reset_mdp.php">Mot de passe oublié ?</a>
        </div>
    </div>
</body>
</html>
