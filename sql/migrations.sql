-- sql/migrations.sql — historique des changements de structure, dans l'ordre chronologique
-- Chaque ligne ici a déjà été exécutée manuellement en local ET sur Neon.

-- 2026-09-24 : passage de la colonne details en JSONB sur php_visite
ALTER TABLE php_visite ADD COLUMN IF NOT EXISTS details JSONB;
-- 2026-10-23 : table des comptes utilisateurs (transférée de SQLite vers Neon par export)
CREATE TABLE php_utilisateurs (
    id                 INTEGER PRIMARY KEY AUTOINCREMENT,
    email              TEXT NOT NULL UNIQUE,
    mot_de_passe_hache TEXT NOT NULL,
    cree_le            TEXT NOT NULL,
    derniere_connexion TEXT
);
