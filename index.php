<?php
// index.php

$pagesAutorisees = ['accueil', 'contact', 'mentions'];
$page = $_GET['page'] ?? 'accueil';

if (!is_string($page) || !in_array($page, $pagesAutorisees, true)) {
    http_response_code(404);
    $page = 'accueil';
}

include __DIR__ . '/pages/' . $page . '.php';
