<?php

// Datei: beispiele/m2_8a_accesslog.php

// Zugriffsinformationen sammeln
$datumUhrzeit = date('Y-m-d H:i:s'); // aktuelles Datum und Uhrzeit
$ipClient = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unbekannt'; // IP des Clients
$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unbekannt'; // Browserinformationen

// Zeile für das Log
$logEintrag = "$datumUhrzeit | IP: $ipClient | Browser: $browser" . PHP_EOL;

// Datei-Pfad
$logDatei = __DIR__ . '/accesslog.txt';

// Logzeile anhängen (erstellt Datei, falls sie nicht existiert)
file_put_contents($logDatei, $logEintrag, FILE_APPEND | LOCK_EX);

// Optional: Info auf der Webseite ausgeben
if (file_put_contents($logDatei, $logEintrag, FILE_APPEND | LOCK_EX) === false) {
    echo "Fehler: konnte Datei nicht schreiben!";
} else {
    echo "Zugriff protokolliert.";
}



