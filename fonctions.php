<?php
// fonctions.php

function e(?string $texte): string
{
    return htmlspecialchars($texte ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verifier_csrf(): void
{
    $attendu = $_SESSION['csrf'] ?? '';
    $recu    = $_POST['csrf'] ?? '';

    // Deux pièges : un jeton absent des deux côtés ('' === '') ne doit jamais passer,
    // et csrf[]=x enverrait un tableau, que hash_equals() refuse en plantant.
    if ($attendu === '' || !is_string($recu) || !hash_equals($attendu, $recu)) {
        http_response_code(403);
        exit('Requête refusée.');
    }
}
