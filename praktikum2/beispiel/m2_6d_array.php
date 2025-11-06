<?php
/**
 * Praktikum DBWT. Autoren:
 * Vorname1, Nachname1, Matrikelnummer1
 * Vorname2, Nachname2, Matrikelnummer2
 */
$famousMeals = [
1 => ['name' => 'Currywurst mit Pommes',
'winner' => [2001, 2003, 2007, 2010, 2020]],
2 => ['name' => 'Hähnchencrossies mit Paprikareis',
'winner' => [2002, 2004, 2008]],
3 => ['name' => 'Spaghetti Bolognese',
'winner' => [2011, 2012, 2017]],
4 => ['name' => 'Jägerschnitzel mit Pommes',
'winner' => 2019]];

/*foreach ($famousMeals as $meal) {
    echo "<br>";
    echo $i . "." . " " . $meal['name'];
    echo "<br>";
    foreach ($meal['winner'] as $winner) {
        echo "<br>";
        echo $winner;
    };
    echo "<br>";
    $i++;
}*/

function keinGewinn($meals, $startYear = 2000, $endYear = 2025) {
    $wonYears = [];

    foreach ($meals as $meal) {
        $winners = is_array($meal['winner']) ? $meal['winner'] : [$meal['winner']];
        $wonYears = array_merge($wonYears, $winners);
    }

    $wonYears = array_unique($wonYears);
    sort($wonYears);

    $noWinnerYears = [];
    for ($year = $startYear; $year <= $endYear; $year++) {
        if (!in_array($year, $wonYears)) {
            $noWinnerYears[] = $year;
        }
    }
    return $noWinnerYears;
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meals</title>
    <style>
        ol { padding-left: 20px; }
        li { margin: 15px 0; }
    </style>
</head>
<body>

<ol>
    <?php
    foreach ($famousMeals as $meal) {
        echo "<li>";


        echo   $meal['name'] . "<br>";

        //letzte ist kein array
        $winners = is_array($meal['winner']) ? $meal['winner'] : [$meal['winner']];

        sort($winners);


        echo  implode(", ", $winners);

        echo "</li>";
    }
     $nowin = keinGewinn($famousMeals);
    echo "no winner years are";
    foreach ($nowin as $meal) {
        echo "<p>";
        echo $meal ;
        echo "</p>";
    }

    ?>
</ol>

</body>
</html>

