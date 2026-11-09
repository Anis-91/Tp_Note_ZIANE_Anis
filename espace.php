<?php
// espace.php
require __DIR__ . '/db.php';
require_once __DIR__ . '/fonctions.php';
require __DIR__ . '/session.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}

$id = $_SESSION['utilisateur_id'];
$stmt = $pdo->prepare("SELECT email FROM php_utilisateurs WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🐍 Snake Online - Mon Espace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header-brand">
            <h1>🐍 SNAKE ONLINE</h1>
            <p>Espace Joueur</p>
        </div>

        <div class="user-card" style="margin-bottom: 20px; text-align: center;">
            <p>Bienvenue, <strong><?= e($user['email'] ?? '') ?></strong> !</p>
        </div>

        <div class="game-actions" style="margin: 20px 0;">
            <button onclick="alert('Partie en cours de création...')" style="width: 100%; margin-bottom: 15px;">+ Nouvelle Partie</button>
            
            <h3 style="margin-top: 20px; color: #fff;">Vos parties récentes</h3>
            <ul style="list-style: none; padding: 0; text-align: left; margin-top: 10px;">
                <li style="padding: 8px; background: rgba(255,255,255,0.05); margin-bottom: 5px; border-radius: 4px;">🎮 Partie #1042 - Score: 120 pts (Terminée)</li>
                <li style="padding: 8px; background: rgba(255,255,255,0.05); margin-bottom: 5px; border-radius: 4px;">🎮 Partie #1038 - Score: 85 pts (Terminée)</li>
            </ul>
        </div>

        <div class="links" style="justify-content: center; margin-top: 20px;">
            <a href="deconnexion.php">Se déconnecter</a>
        </div>
    </div>
</body>
</html>
