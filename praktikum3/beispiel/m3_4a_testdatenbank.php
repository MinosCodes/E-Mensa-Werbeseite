<?php
$link = mysqli_connect(
    'localhost',
    'root',
    '26269946',
    'emensawerbeseite'
);
if (!$link) {
    echo "Verbindung fehlgeschlagen: ", mysqli_connect_error();
    exit();
}

$sql = "SELECT name, beschreibung
FROM gericht
ORDER BY name ASC
LIMIT 5 ";

$result = mysqli_query($link, $sql);

if (!$result) {
    echo "Fehler während der Abfrage:  ", mysqli_error($link);
    exit();
}

echo "<table>";
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['beschreibung'] . "</td>";
    echo "</tr>";
}
echo "</table>";
mysqli_free_result($result);
mysqli_close($link);