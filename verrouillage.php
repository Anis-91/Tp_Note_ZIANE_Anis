<?php
// verrouillage.php — limitation des tentatives de connexion (atelier 7).

const HASH_FACTICE = '$2y$10$tS9UNk9hez8IbNfbIetLReKeGMWZrlqTa5D9ZrCJQbkPuS4mOoisO';

function cle_de_comptage(string $valeur): string
{
    $sel = getenv('SEL_APPLICATIF');

    if ($sel === false || $sel === '') {
        $https = ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        if ($https) {
            throw new RuntimeException("La variable d'environnement SEL_APPLICATIF n'est pas définie sur le serveur.");
        }
        $sel = 'sel-de-developpement';
    }

    return hash_hmac('sha256', $valeur, $sel);
}

function est_verrouille(PDO $pdo, string $email): bool
{
    $maintenant = time();

    $pdo->prepare("DELETE FROM php_tentatives WHERE horodatage < :limite")
        ->execute([':limite' => $maintenant - 3600]);

    $parEmail = $pdo->prepare("SELECT COUNT(*) FROM php_tentatives WHERE email_hache = :cle AND horodatage > :depuis");
    $parEmail->execute([':cle' => cle_de_comptage($email), ':depuis' => $maintenant - 900]);

    $parIp = $pdo->prepare("SELECT COUNT(*) FROM php_tentatives WHERE ip_hachee = :cle AND horodatage > :depuis");
    $parIp->execute([':cle' => cle_de_comptage($_SERVER['REMOTE_ADDR'] ?? ''), ':depuis' => $maintenant - 900]);

    return (int) $parEmail->fetchColumn() >= 5 || (int) $parIp->fetchColumn() >= 20;
}

function enregistrer_echec(PDO $pdo, string $email): void
{
    $pdo->prepare("INSERT INTO php_tentatives (email_hache, ip_hachee, horodatage) VALUES (:e, :i, :h)")
        ->execute([
            ':e' => cle_de_comptage($email),
            ':i' => cle_de_comptage($_SERVER['REMOTE_ADDR'] ?? ''),
            ':h' => time(),
        ]);
}
