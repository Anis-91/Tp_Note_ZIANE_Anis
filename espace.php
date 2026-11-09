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
$stmt = $pdo->prepare("SELECT email, derniere_connexion FROM php_utilisateurs WHERE id = :id");
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

        <div class="user-card" style="margin-bottom: 20px; text-align: left;">
            <p><strong>Joueur :</strong> <?= e($user['email'] ?? '') ?></p>
            <p><strong>Dernière connexion :</strong> <?= e($user['derniere_connexion'] ?? 'Première connexion') ?></p>
        </div>

        <div class="game-actions" style="margin: 20px 0;">
            <button onclick="alert('Partie en cours de développement !')" style="width: 100%; margin-bottom: 10px;">+ Nouvelle Partie</button>
        </div>

        <div class="links" style="justify-content: center;">
            <a href="deconnexion.php">Se déconnecter</a>
        </div>
    </div>
</body>
</html>
