<?php
// verrouillage.php

function est_verrouille(PDO $pdo, string $email): bool
{
    $empreinte = hash_hmac('sha256', $email, 'sel_secret_ssi');
    
    // Nettoyage des tentatives de plus de 15 minutes (au format DATETIME PostgreSQL)
    $limite = date('Y-m-d H:i:s', strtotime('-15 minutes'));
    $nettoyage = $pdo->prepare("DELETE FROM php_tentatives WHERE horodatage < :limite");
    $nettoyage->execute([':limite' => $limite]);

    // Comptage des tentatives récentes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM php_tentatives WHERE empreinte = :empreinte");
    $stmt->execute([':empreinte' => $empreinte]);
    $nbEchecs = (int) $stmt->fetchColumn();

    return $nbEchecs >= 5;
}

function enregistrer_echec(PDO $pdo, string $email): void
{
    $empreinte = hash_hmac('sha256', $email, 'sel_secret_ssi');
    $maintenant = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO php_tentatives (empreinte, horodatage) VALUES (:empreinte, :horodatage)");
    $stmt->execute([
        ':empreinte'  => $empreinte,
        ':horodatage' => $maintenant
    ]);
}
