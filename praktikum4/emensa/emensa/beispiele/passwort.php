<?php
/**
 * Utility script to generate password hashes for the E-Mensa admin user.
 * Usage from project root:
 *   php beispiele/passwort.php <plainPassword>
 */

const EMENSA_SALT = 'eMensa2025!'; // global salt shared across app

$password = $argv[1] ?? '';
if ($password === '') {
    fwrite(STDERR, "Bitte übergeben Sie das Klartext-Passwort als Argument.\n");
    exit(1);
}

$hash = password_hash(EMENSA_SALT . $password, PASSWORD_DEFAULT);

echo "Salt: " . EMENSA_SALT . PHP_EOL;

echo "Password: " . $password . PHP_EOL;

echo "Hash: " . $hash . PHP_EOL;
