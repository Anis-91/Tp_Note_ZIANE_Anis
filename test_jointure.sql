-- Test de la jointure entre php_lieu et php_visite
SELECT l.nom, l.categorie, l.ville, v.date_visite, v.frequentation
FROM php_lieu l
JOIN php_visite v ON v.lieu_id = l.id
ORDER BY l.nom, v.date_visite;
