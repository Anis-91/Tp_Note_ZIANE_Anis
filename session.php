<?php
// session.php

ini_set('session.use_strict_mode', '1');    // un identifiant inconnu du serveur est remplacé
ini_set('session.use_only_cookies', '1');   // jamais d'identifiant passé dans l'URL

$https = ($_SERVER['HTTPS'] ?? '') === 'on'
      || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

session_set_cookie_params([
    'lifetime' => 0,          // le cookie meurt à la fermeture du navigateur
    'path'     => '/',
    'httponly' => true,       // invisible à JavaScript
    'secure'   => $https,     // HTTPS uniquement (en production)
    'samesite' => 'Lax',      // ne part pas sur une requête venue d'un autre site
]);

session_start();
