<?php
/**
 * Praktikum DBWT. Autoren:
 * Adem, Essouei, 3730582
 * Mohamed-amine, Merdassi, 3729412
 */

// Zugriffsinformationen sammeln
$datumUhrzeit = date('Y-m-d H:i:s'); // aktuelles Datum und Uhrzeit
$ipClient = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unbekannt'; // IP des Clients
$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unbekannt'; // Browserinformationen

// Zeile für das Log
$logEintrag = "$datumUhrzeit | IP: $ipClient | Browser: $browser" . PHP_EOL;

// Datei-Pfad
$logDatei = __DIR__ . '/accesslog.txt';

// Logzeile anhängen (erstellt Datei, falls sie nicht existiert)
file_put_contents($logDatei, $logEintrag, FILE_APPEND);

// Optional: Info auf der Webseite ausgeben
if (file_put_contents($logDatei, $logEintrag, FILE_APPEND) === false) {
    echo "Fehler: konnte Datei nicht schreiben!";
} else {
    echo "Zugriff protokolliert.";
}
