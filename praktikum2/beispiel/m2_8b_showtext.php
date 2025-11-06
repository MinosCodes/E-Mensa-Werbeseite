<?php

// Check if 'suche' is provided
if (empty($_GET['suche'])) {
    echo "Bitte geben Sie ein Suchwort über den GET-Parameter 'suche' an.";
    exit;
}

$searchWord = $_GET['suche'];
$filename = "en.txt";
$found = false;

// Check if file exists
if (!file_exists($filename)) {
    echo "Die Datei 'en.txt' wurde nicht gefunden.";
    exit;
}

// Open file and search
$file = fopen($filename, "r");

while (($line = fgets($file)) !== false) {
    if (stripos($line, $searchWord) !== false) {
        echo nl2br($line); // Output matching translation
        $found = true;
        break;
    }
}

fclose($file);

// If not found, show message
if (!$found) {
    echo "Das gesuchte Wort <b>" . $searchWord . "</b> ist nicht enthalten";
}

