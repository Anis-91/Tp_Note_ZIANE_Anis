<?php
// db.php - connexion à la base de données selon l'environnement

/**
 * Lit un fichier .env simple (CLE=valeur) et renvoie un tableau associatif.
 * En projet réel, on utiliserait une bibliothèque dédiée (vlucas/phpdotenv).
 */
function charger_env(string $chemin): array
{
    if (!is_readable($chemin)) {
        die("Fichier de configuration introuvable : $chemin");
    }

    $config = [];
    foreach (file($chemin, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
        $ligne = trim($ligne);
        if ($ligne === '' || str_starts_with($ligne, '#')) {
            continue; // on saute les commentaires et les lignes vides
        }
        [$cle, $valeur] = explode('=', $ligne, 2);
        $config[trim($cle)] = trim($valeur);
    }

    return $config;
}

// En développement, .env.local existe et fournit toute la configuration (SQLite).
// En production (Render), il n'existe pas : la configuration vient alors directement des
// variables d'environnement du conteneur (voir module "02-prepa-env-prod", section 6.E) — c'est
// Render qui les y dépose, pas un fichier que vous géreriez vous-même.
if (is_readable(__DIR__ . '/.env.local')) {
    $config = charger_env(__DIR__ . '/.env.local');
} else {
    $config = [
        'DB_DRIVER'  => getenv('DB_DRIVER') ?: 'pgsql',
        'DB_HOST'    => getenv('DB_HOST'),
        'DB_PORT'    => getenv('DB_PORT'),
        'DB_NAME'    => getenv('DB_NAME'),
        'DB_USER'    => getenv('DB_USER'),
        'DB_PASS'    => getenv('DB_PASS'),
        'DB_SSLMODE' => getenv('DB_SSLMODE') ?: 'require',
    ];
}

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    if ($config['DB_DRIVER'] === 'sqlite') {
        // Développement : un simple fichier
        $dsn = 'sqlite:' . __DIR__ . '/' . $config['DB_PATH'];
        $pdo = new PDO($dsn, null, null, $options);
    } else {
        // Production : PostgreSQL managé (Neon)
        $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};sslmode={$config['DB_SSLMODE']}";
        $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], $options);
    }
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données.');
}
