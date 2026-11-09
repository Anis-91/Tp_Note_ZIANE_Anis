<?php
// espace.php
require __DIR__ . '/session.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php');
    exit;
}
?>
<h1>Mon espace</h1>
<p>Votre carte Isoculture s'affichera ici — séance n°4 (semestre 4).</p>
<p><a href="deconnexion.php">Se déconnecter</a></p>
