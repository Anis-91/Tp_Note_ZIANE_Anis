<?php
// inscription.php

require __DIR__ . '/db.php';
require_once __DIR__ . '/classes/Utilisateur.php';

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $utilisateur = Utilisateur::creer($_POST['email'] ?? '', $_POST['mot_de_passe'] ?? '');

        $email  = $utilisateur->getEmail();
        $hache  = $utilisateur->getMotDePasseHache();
        $creeLe = date('c');

        $sql = "INSERT INTO php_utilisateurs (email, mot_de_passe_hache, cree_le)
                VALUES (:email, :hache, :cree_le)";
        $requete = $pdo->prepare($sql);

        $requete->bindParam(':email',   $email,  PDO::PARAM_STR);
        $requete->bindParam(':hache',   $hache,  PDO::PARAM_STR);
        $requete->bindParam(':cree_le', $creeLe, PDO::PARAM_STR);
        $requete->execute();

        header('Location: connexion.php?inscrit=1');
        exit;

    } catch (InvalidArgumentException $e) {
        $erreur = $e->getMessage();
    } catch (PDOException $e) {
        error_log('Inscription : ' . $e->getMessage());
        $erreur = "Cette adresse est peut-être déjà utilisée.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Inscription — Isoculture</title></head>
<body>
<h1>Créer un compte</h1>

<?php if ($erreur !== null): ?>
    <p role="alert">⚠️ <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form method="post" action="inscription.php">
    <label for="email">Adresse e-mail</label>
    <input type="email" id="email" name="email" required>

    <label for="mdp">Mot de passe (12 caractères minimum)</label>
    <input type="password" id="mdp" name="mot_de_passe" required minlength="12">

    <button type="submit">Créer mon compte</button>
</form>
</body>
</html>
