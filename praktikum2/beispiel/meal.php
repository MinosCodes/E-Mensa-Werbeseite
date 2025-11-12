<?php
/**
 * Praktikum DBWT. Autoren:
 * Adem, Essouei, 3730582
 * Mohamed-amine, Merdassi, 3729412
 */
const GET_PARAM_MIN_STARS = 'search_min_stars';
const GET_PARAM_SEARCH_TEXT = 'search_text';
const GET_PARAM_SHOW_DESCRIPTION = 'show_description';
const GET_PARAM_LANGUAGE = 'sprache';
const GET_PARAM_HIGHLIGHT = 'highlight';


//sprachen
$texts = [
        'de' => [
                'header_meal' => 'Gericht: ',
                'allergens_title' => 'Allergene:',
                'ratings_header' => 'Bewertungen (Insgesamt: ',
                'ratings_table_author' => 'Author',
                'ratings_table_text' => 'Text',
                'ratings_table_stars' => 'Sterne',
                'filter_label' => 'Filter:',
                'search_button' => 'Suchen',
                'price_intern' => 'Preis (intern)',
                'price_extern' => 'Preis (extern)',
                'show_top' => 'Top Bewertungen',
                'show_flop' => 'Flop Bewertungen'
        ],
        'en' => [
                'header_meal' => 'Meal: ',
                'allergens_title' => 'Allergens:',
                'ratings_header' => 'Ratings (Total: ',
                'ratings_table_author' => 'Author',
                'ratings_table_text' => 'Text',
                'ratings_table_stars' => 'Stars',
                'filter_label' => 'Filter:',
                'search_button' => 'Search',
                'price_intern' => 'Price (internal)',
                'price_extern' => 'Price (external)',
                'show_top' => 'Top Ratings',
                'show_flop' => 'Flop Ratings'
        ]
];

/**
 * List of all allergens.
 */
$allergens = [
        11 => 'Gluten',
        12 => 'Krebstiere',
        13 => 'Eier',
        14 => 'Fisch',
        17 => 'Milch'
];

$meal = [
        'name' => 'Süßkartoffeltaschen mit Frischkäse und Kräutern gefüllt',
        'description' => 'Die Süßkartoffeln werden vorsichtig aufgeschnitten und der Frischkäse eingefüllt.',
        'price_intern' => 2.90,
        'price_extern' => 3.90,
        'allergens' => [11, 13],
        'amount' => 42             // Number of available meals
];

$ratings = [
        [   'text' => 'Die Kartoffel ist einfach klasse. Nur die Fischstäbchen schmecken nach Käse. ',
                'author' => 'Ute U.',
                'stars' => 2 ],
        [   'text' => 'Sehr gut. Immer wieder gerne',
                'author' => 'Gustav G.',
                'stars' => 4 ],
        [   'text' => 'Der Klassiker für den Wochenstart. Frisch wie immer',
                'author' => 'Renate R.',
                'stars' => 4 ],
        [   'text' => 'Kartoffel ist gut. Das Grüne ist mir suspekt.',
                'author' => 'Marta M.',
                'stars' => 3 ]
];

$currentLang = $_GET[GET_PARAM_LANGUAGE] ?? 'de';
if($currentLang !== 'en') $currentLang = 'de';

$allStars = array_column($ratings, 'stars');
$maxStarsValue = max($allStars);
$minStarsValue = min($allStars);

$searchTerm = '';
$showRatings = [];
if (!empty($_GET[GET_PARAM_SEARCH_TEXT])){
    $searchTerm = $_GET[GET_PARAM_SEARCH_TEXT];
    foreach ($ratings as $rating) {
        if (stripos($rating['text'], $searchTerm) !== false) {
            $showRatings[] = $rating;
        }
    }
} else if (!empty($_GET[GET_PARAM_MIN_STARS])) {
    $minStars = $_GET[GET_PARAM_MIN_STARS];
    foreach ($ratings as $rating) {
        if ($rating['stars'] >= $minStars) {
            $showRatings[] = $rating;
        }
    }
} else if (!empty($_GET[GET_PARAM_HIGHLIGHT])) {
        $highlightMode = $_GET[GET_PARAM_HIGHLIGHT];

    if ($highlightMode === 'top') {
        foreach ($ratings as $rating) {
            if ($rating['stars'] == $maxStarsValue) {
                $showRatings[] = $rating;
            }
        }
    } else if ($highlightMode === 'flop') {
        foreach ($ratings as $rating) {
            // Vergleiche mit dem dynamischen Min-Wert
            if ($rating['stars'] == $minStarsValue) {
                $showRatings[] = $rating;
            }
        }
    } else {
        $showRatings = $ratings;
    }
} else {
    $showRatings = $ratings;
}

function calcMeanStars(array $ratings) : float {
    $sum = 0;
    foreach ($ratings as $rating) {
        $sum += $rating['stars'];
    }
    if (count($ratings) === 0) return 0.0;
    return $sum/ count($ratings);
}

function translate(string $key) {
    global $texts, $currentLang;
    echo $texts[$currentLang][$key];
}

function displayStars(int $stars) : string {
    $starSymbol = '★';
    $output = '';

    for ($i = 0; $i < $stars; $i++) {
        $output .= $starSymbol;
    }
    return $output;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
    <meta charset="UTF-8"/>
    <title><?php translate('header_meal'); echo $meal['name']; ?></title>
    <style>
        * {
            font-family: Arial, serif;
        }
        .rating {
            color: darkgray;
        }
    </style>
</head>
<body>
<div>
    <a href="?sprache=de">Deutsch</a> |
    <a href="?sprache=en">English</a>
</div>
<h1><?php translate('header_meal'); echo $meal['name']; ?></h1>
<p><?php
    if (!isset($_GET[GET_PARAM_SHOW_DESCRIPTION]) || $_GET[GET_PARAM_SHOW_DESCRIPTION] != 0){echo $meal['description'];}
    ?>
</p>
<div>
    <strong><?php translate('price_intern'); ?>:</strong>
    <?php echo number_format($meal['price_intern'], 2, ',', '') . '€'; ?>
    <br>
    <strong><?php translate('price_extern'); ?>:</strong>
    <?php echo number_format($meal['price_extern'], 2, ',', '') . '€'; ?>
</div>
<h3><?php translate('allergens_title');?></h3>
<ul>
    <?php
    $allergie = $meal['allergens'];
    foreach ($allergie as $element) {
        echo "<li>", $allergens[$element], "</li>";
    }?>
</ul>

<h1><?php translate('ratings_header'); echo calcMeanStars($ratings); ?>)</h1>
<form method="get">
    <label for="search_text"><?php translate('filter_label'); ?></label>
    <input id="search_text" type="text" name="search_text" value="<?php echo htmlspecialchars($searchTerm); ?>">
    <input type="hidden" name="<?php echo GET_PARAM_LANGUAGE; ?>" value="<?php echo htmlspecialchars($currentLang); ?>">
    <input type="submit" value="<?php translate('search_button'); ?>">
</form>
<table class="rating">
    <thead>
    <tr>
        <td><?php translate('ratings_table_author'); ?></td>
        <td><?php translate('ratings_table_text'); ?></td>
        <td><?php translate('ratings_table_stars'); ?></td>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($showRatings as $rating) {
        echo "<tr>
                <td class='rating_author'>{$rating['author']}</td>
                <td class='rating_text'>{$rating['text']}</td>
                <td class='rating_stars'>" . displayStars($rating['stars']) . "</td>
              </tr>";
    }
    ?>
    </tbody>
</table>
<div>
    <a href="?<?php echo GET_PARAM_LANGUAGE; ?>=<?php echo $currentLang; ?>&<?php echo GET_PARAM_HIGHLIGHT; ?>=top">
        <?php translate('show_top'); ?>
    </a> |
    <a href="?<?php echo GET_PARAM_LANGUAGE; ?>=<?php echo $currentLang; ?>&<?php echo GET_PARAM_HIGHLIGHT; ?>=flop">
        <?php translate('show_flop'); ?>
    </a>
</div>
</body>
</html>