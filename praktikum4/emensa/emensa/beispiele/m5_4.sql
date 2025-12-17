-- SQL-Sichten für die E-Mensa Berichte
-- Hinweis: Bitte in der Datenbank `emensawerbeseite` ausführen.

DROP VIEW IF EXISTS view_suppengerichte;
CREATE VIEW view_suppengerichte AS
SELECT g.id,
       g.name,
       g.beschreibung,
       g.erfasst_am,
       g.preisintern,
       g.preisextern,
       g.vegan,
       g.vegetarisch,
       g.bildname
FROM   gericht AS g
WHERE  LOWER(g.name) LIKE '%suppe%';

DROP VIEW IF EXISTS view_anmeldungen;
CREATE VIEW view_anmeldungen AS
SELECT b.id,
       b.name,
       b.email,
       b.anzahlanmeldungen,
       b.anzahlfehler,
       b.letzteanmeldung,
       b.letzterfehler
FROM   benutzer AS b
ORDER  BY b.anzahlanmeldungen DESC;

DROP VIEW IF EXISTS view_kategoriegerichte_vegetarisch;
CREATE VIEW view_kategoriegerichte_vegetarisch AS
SELECT k.id            AS kategorie_id,
       k.name          AS kategorie_name,
       g.id            AS gericht_id,
       g.name          AS gericht_name,
       g.vegetarisch,
       g.vegan
FROM   kategorie AS k
       LEFT JOIN gericht_hat_kategorie AS ghk
              ON ghk.kategorie_id = k.id
       LEFT JOIN gericht AS g
              ON g.id = ghk.gericht_id
             AND g.vegetarisch = 1
ORDER  BY k.name, g.name;


SELECT * FROM view_suppengerichte;
SELECT * FROM view_anmeldungen;
SELECT * FROM view_kategoriegerichte_vegetarisch LIMIT 20;