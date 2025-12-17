-- Prozedur für das Inkrementieren des Anmeldungszählers
-- Ausführen innerhalb der Datenbank `emensawerbeseite`

DELIMITER $$
DROP PROCEDURE IF EXISTS increment_user_login $$
CREATE PROCEDURE increment_user_login(IN p_user_id INT)
BEGIN
    UPDATE benutzer
    SET anzahlanmeldungen = anzahlanmeldungen + 1,
        letzteanmeldung    = NOW()
    WHERE id = p_user_id;
END $$
DELIMITER ;

-- Weitere sinnvolle Prozeduren für die E-Mensa (nur Beschreibung):
-- 1) record_failed_login(p_user_id INT): erhöht `anzahlfehler`, setzt `letzterfehler` und könnte bei zu vielen Fehlern den Account sperren.
--    Vorteil: Konsistente Sicherheitslogik auf Datenbankebene, zentrale Nutzung durch alle Anwendungen.
-- 2) register_newsletter_subscription(p_name VARCHAR(100), p_vorname VARCHAR(100), p_email VARCHAR(255)): validiert und speichert Newsletter-Anmeldungen,
--    setzt automatisch Zeitstempel und blockiert bekannte Wegwerf-Domains. Vorteil: verhindert doppelte Implementierungen und garantiert einheitliche Regeln.
