<?php
/**
 * Praktikum DBWT. Autoren:
 * Adem, Essouei, 3730582
 * Mohamed-amine, Merdassi, 3729412
 */

include 'm2_6a_standardparameter.php';

$operation_text = '';
$result = null;

if (isset($_POST['operation'])) {

    $a = isset($_POST['a']) ? (int)$_POST['a'] : 0;
    $b = isset($_POST['b']) ? (int)$_POST['b'] : 0;

    $operation = $_POST['operation'];

    if ($operation === 'add') {
        $result = addieren($a, $b);
        $operation_text = 'Addition';
    } elseif ($operation === 'multiply') {
        $result = 0;
        for ($i = 0; $i < $a; $i++) {
            $result = addieren($result, $b);
        }
        $operation_text = 'Multiplikation';
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Addition/Multiplikationsformular</title>
</head>
<body>

<h2>Add / Mult</h2>

<form action="" method="post">
    <label for="a">Zahl a:</label>
    <input type="number" id="a" name="a" value="<?php echo $a; ?>" required><br><br>

    <label for="b">Zahl b:</label>
    <input type="number" id="b" name="b" value="<?php echo $b; ?>" required><br><br>

    <button type="submit" name="operation" value="add">addieren</button>

    <button type="submit" name="operation" value="multiply">multiplizieren</button>

    <div id="result">
        <?php
        if ($result !== null) {
            echo "<h3>Ergebnis der $operation_text:</h3>";
            echo "<p>Das Ergebnis von $a und $b ist: <strong>$result</strong></p>";
        }
        ?>
    </div>

</form>
</body>
</html>