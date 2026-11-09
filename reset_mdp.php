<?php
// reset_mdp.php - Réinitialisation du mot de passe
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/fonctions.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = null;
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse e-mail invalide.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM php_utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiration = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $update = $pdo->prepare("UPDATE php_utilisateurs SET token_reset = :token, token_expiration = :exp WHERE id = :id");
            $update->execute([
                ':token' => $token,
                ':exp'   => $expiration,
                ':id'    => $user['id']
            ]);

            // Configuration PHPMailer avec le SMTP UGE ou variables d'environnement
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                // Hôte SMTP UGE (intranet) ou fallback configurable
                $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.univ-eiffel.fr';
                $mail->SMTPAuth   = false; // Le serveur SMTP UGE en intranet ne nécessite généralement pas d'auth
                $mail->Port       = (int) ($_ENV['SMTP_PORT'] ?? 25);
                $mail->Timeout    = 3; // Timeout de 3s pour ne pas bloquer l'interface
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom('no-reply@univ-eiffel.fr', 'Snake Online - UGE');
                $mail->addAddress($email);

                $lien = "https://tp-note-ziane-anis.onrender.com/nouveau_mdp.php?token=" . $token;
                
                $mail->isHTML(true);
                $mail->Subject = 'Snake Online - Reinitialisation du mot de passe';
                $mail->Body    = "Pour réinitialiser votre mot de passe, cliquez ici : <a href='{$lien}'>{$lien}</a>";

                $mail->send();
            } catch (Exception $e) {
                error_log("Notification SMTP : " . $e->getMessage());
            }
        }

        // Réponse utilisateur immédiate (sécurité : pas d'énumération d'emails)
        $message = "Si cette adresse existe, un mail vient d'être envoyé.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🐍 Snake Online - Mot de passe oublié</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header-brand">
            <h1>🐍 SNAKE</h1>
            <p>Mot de passe oublié</p>
        </div>

        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= e($erreur) ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="reset_mdp.php">
            <div class="form-group">
                <label for="email">Votre adresse e-mail</label>
                <input type="email" id="email" name="email" required placeholder="étudiant@etu.univ-eiffel.fr">
            </div>
            <button type="submit">Envoyer</button>
        </form>

        <div class="links" style="justify-content: center;">
            <a href="connexion.php">Retour à la connexion</a>
        </div>
    </div>
</body>
</html>
