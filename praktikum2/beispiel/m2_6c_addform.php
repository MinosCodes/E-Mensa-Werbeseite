<?php
/**
 * Praktikum DBWT. Autoren:
 * Vorname1, Nachname1, Matrikelnummer1
 * Vorname2, Nachname2, Matrikelnummer2
 */
$result = '';


$a = $_GET['a'];
$b = $_GET['b'];


if (isset($_GET['addition'])) {
    $result = $a + $b;
} elseif (isset($_GET['multiplication'])) {
    $result = $a * $b;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title> Multi/Addi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        input, button { margin: 5px; }
        #result { margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
<h2>Rechner</h2>
<form method="get" action="m2_6c_addform.php">
    <label for="a">a: </label>
    <input type="number" name="a" id="a" required value="<?php echo $a; ?>">
    <label for="b">b: </label>
    <input type="number" name="b" id="b" required value="<?php echo $b; ?>">
    <br>
    <button type="submit" name="addition">Addieren</button>
    <button type="submit" name="multiplication">Multiplizieren</button>
</form>

<div id="result">
    <?php echo "Ergebnis: $result"; ?>
</div>
</body>
</html>
