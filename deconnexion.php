<?php
// deconnexion.php
require __DIR__ . '/session.php';

$_SESSION = [];
session_destroy();

header('Location: connexion.php');
exit;
