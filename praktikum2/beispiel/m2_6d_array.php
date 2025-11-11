<?php
/**
 * Praktikum DBWT. Autoren:
 * Adem, Essouei, 3730582
 * Mohamed-amine, Merdassi, 3729412
 */

$famousMeals = [
    1 => ['name' => 'Currywurst mit Pommes',
        'winner' => [2001, 2003, 2007, 2010, 2020]],
    2 => ['name' => 'Hähnchencrossies mit Paprikareis',
        'winner' => [2002, 2004, 2008]],
    3 => ['name' => 'Spaghetti Bolognese',
        'winner' => [2011, 2012, 2017]],
    4 => ['name' => 'Jägerschnitzel mit Pommes',
        'winner' => 2019]
];

foreach ($famousMeals as $key => $meal) {

    echo $key . ". " . $meal['name'] . "<br>";
    //letztes element ist kein array
    $winners = is_array($meal['winner']) ? $meal['winner'] : [$meal['winner']];
    sort($winners);
    echo "&nbsp;&nbsp;&nbsp;&nbsp;" . implode(", ", $winners), "<br>";
}

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


echo "<br>";
$nowin = keinGewinn($famousMeals);
echo "no winner years are", "<br>";
echo implode(", ", $nowin), "<br>";
