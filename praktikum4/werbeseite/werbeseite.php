<?php
/**
 * Praktikum DBWT. Autoren:
 * Adem, Essouei, 3730582
 * Mohamed-amine, Merdassi, 3729412
 */

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$link = mysqli_connect(
        "localhost",
        "root",
        "26269946",
        "emensawerbeseite"
);

if (!$link) {
    die("Datenbankverbindung fehlgeschlagen: " . htmlspecialchars(mysqli_connect_error(), ENT_QUOTES, 'UTF-8'));
}

// Gericht-Query (keine User-Eingaben -> sicher bzgl. SQLi)
$sql = "SELECT 
        gericht.id,
        gericht.name,
        gericht.beschreibung,
        gericht.erfasst_am,
        gericht.preisintern,
        gericht.preisextern,
        GROUP_CONCAT(gericht_hat_allergen.code SEPARATOR ', ') AS codes
    FROM gericht
    LEFT JOIN gericht_hat_allergen
        ON gericht.id = gericht_hat_allergen.gericht_id
    GROUP BY gericht.id
    LIMIT 5;
";

$sql2 = "SELECT COUNT(*) AS anzahl_gericht FROM gericht";
$resultAnzahlgericht = mysqli_query($link, $sql2);
$result = mysqli_query($link, $sql);
$rowAnzahlgericht = mysqli_fetch_assoc($resultAnzahlgericht);

// Fehlerliste Newsletter
$Errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF-Check
    if (
            !isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $Errors[] = "Ungültiger Sicherheits-Token. Bitte laden Sie die Seite neu.";
    } else {
        $Name = trim($_POST["name"] ?? '');
        $Vorname = trim($_POST["vorname"] ?? '');
        $Email = trim($_POST["email"] ?? '');

        if (strlen($Name) === 0) {
            $Errors[] = "Bitte geben Sie einen Namen ein.";
        }
        if (strlen($Vorname) === 0) {
            $Errors[] = "Bitte geben Sie einen Vornamen ein.";
        }
        if (strlen($Email) === 0 || !filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $Errors[] = "Bitte geben Sie eine gültige E-Mail ein.";
        }
        if (!isset($_POST["datenschutz"])) {
            $Errors[] = "Bitte stimmen Sie den Datenschutzbestimmungen zu.";
        }

        $domain = strtolower(substr(strrchr($Email, "@"), 1));
        $UngultDomains = [
                "trashmail.de",
                "trashmail.com",
                "wegwerfmail.de",
                "wegwerfmail.com",
        ];

        if (in_array($domain, $UngultDomains, true)) {
            $Errors[] = "Diese E-Mail-Adresse ist nicht erlaubt.";
        }

        // Nur speichern, wenn keine Fehler
        if (empty($Errors)) {
            $stmt = mysqli_prepare(
                    $link,
                    "INSERT INTO newsletter (name, vorname, email, erstellt_am) VALUES (?, ?, ?, ?)"
            );
            $now = date("Y-m-d H:i:s");
            mysqli_stmt_bind_param($stmt, "ssss", $Name, $Vorname, $Email, $now);

            if (mysqli_stmt_execute($stmt)) {
                echo "<p style='color:green; text-align:center;'>Vielen Dank, Ihre Anmeldung war erfolgreich!</p>";
                // Formular-Felder zurücksetzen
                $_POST = [];
            } else {
                $Errors[] = "Fehler beim Speichern der Daten. Bitte versuchen Sie es später erneut.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Besucherzähler (keine User-Eingabe -> sicher)
mysqli_query($link, "UPDATE visitor_counter SET count = count + 1 WHERE id = 1");
$resultVisitorCounter = mysqli_query($link, "SELECT count FROM visitor_counter WHERE id = 1");
$rowVisitorCounter = mysqli_fetch_assoc($resultVisitorCounter);
$visitorCount = (int)$rowVisitorCounter['count'];

// Newsletter-Anzahl
$resultNewsletter = mysqli_query($link, "SELECT COUNT(*) AS newsletterCount FROM newsletter");
$rowNewsletter = mysqli_fetch_assoc($resultNewsletter);
$newsletterCount = (int)$rowNewsletter['newsletterCount'];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>E-Mensa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #222;
            background-color: #fafafa;
        }
        header {
            padding: 15px;
            border-bottom: 3px solid #005f88;
            background-color: #0073aa;
            color: white;
        }
        .Mensa_Logo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .navBar {
            display: flex;
            justify-content: center;
            gap: 25px;
        }
        .navBar a {
            text-decoration: none;
            color: black;
            font-weight: bold;
            transition: color 0.2s ease;
        }
        .navBar a:hover {
            color: white;
        }
        .Description, .Speisen_Tabelle, .containerZahlen, .Container_Kontakt, .containerFooter {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 40px;
        }
        h1{
            color: #0073aa;
            margin-bottom: 15px;
            text-align: center;
        }
        p{
            text-align: center;
            max-width: 700px;
        }
        table, th, td {
            border: 1px solid #ccc;
            border-collapse: collapse;
            padding: 10px;
            text-align: center;
        }
        table {
            width: 70%;
            background-color: white;
        }
        th {
            background-color: #e8f4fa;
        }
        .containerZahlen {
            display: grid;
            grid-template-columns: repeat(3, 300px);
            gap: 5px;
            text-align: center;
            font-weight: bold;
        }
        .Container_Kontakt input, .Container_Kontakt select {
            margin-top: 5px;
            margin-bottom: 15px;
            padding: 8px;
            width: 250px;
        }
        .Container_Kontakt div {
            margin: 10px 0;
        }
        #datensch {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
        }
        #datensch label {
            margin-right: -5px;
        }
        #datensch input[type="checkbox"] {
            width: auto;
            margin: 10px;
            margin-right: 0;
        }
        button {
            background-color: #0073aa;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #005f88;
        }
        button:active {
            background-color: #004766;
        }
        #wichtig ul {
            font-size: 1.2em;
            line-height: 1.8em;
            list-style-type: "🍽️ ";
            padding-left: 20px;
            margin-top: -5px;
        }
        #wichtig li {
            margin: 10px 0;
        }
        .containerFooter a {
            text-decoration: none;
            color: #0073aa;
        }
        .containerFooter a:hover {
            text-decoration: underline;
        }
        hr{
            height: 2px;
            margin: 20px 0;
        }
        .wish-link-wrapper {
            grid-column: 1 / -1;
            text-align: center;
            margin-top: 20px;
        }
        .wish-link-button {
            display: inline-block;
            background-color: #0073aa;
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.1s ease;
        }
        .wish-link-button:hover {
            background-color: #005f88;
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        }
        .wish-link-button:active {
            background-color: #004766;
            transform: translateY(0);
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
<header>
    <div class="Mensa_Logo">E-Mensa Logo</div>
    <nav class="navBar">
        <a href="#ankündigung">Ankündigung</a>
        <a href="#speisen">Speisen</a>
        <a href="#zahlen">Zahlen</a>
        <a href="#kontakt">Kontakt</a>
        <a href="#wichtig">Wichtig für uns</a>
    </nav>
</header>

<section id="ankündigung" class="Description">
    <h1>Bald gibt es Essen auch online :-)</h1>
    <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
        Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet
        quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
</section>

<section id="speisen" class="Speisen_Tabelle">
    <h1>Köstlichkeiten, die Sie erwarten</h1>
    <table>
        <tr>
            <th>Name</th>
            <th>beschreibung</th>
            <th>erfasst_am</th>
            <th>preisintern</th>
            <th>preisextern</th>
            <th>Inhaltsstoffe</th>
            <th>Foto</th>
        </tr>

        <?php
        $dishesArray = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $dishesArray[] = $row;
        }

        $sort = 'ASC';
        if (isset($_GET['sort']) && in_array(strtoupper($_GET['sort']), ['ASC','DESC'], true)) {
            $sort = strtoupper($_GET['sort']);
        }
        usort($dishesArray, function($a, $b) use ($sort) {
            if ($sort === 'ASC') {
                return strcmp($a['name'], $b['name']);
            } else {
                return strcmp($b['name'], $a['name']);
            }
        });

        foreach ($dishesArray as $dish): ?>
            <tr>
                <td><?php echo htmlspecialchars($dish['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($dish['beschreibung'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($dish['erfasst_am'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($dish['preisintern'], ENT_QUOTES, 'UTF-8'); ?> €</td>
                <td><?php echo htmlspecialchars($dish['preisextern'], ENT_QUOTES, 'UTF-8'); ?> €</td>
                <td><?php echo htmlspecialchars($dish['codes'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <img src="img/<?php echo (int)$dish['id']; ?>.png"
                         alt="Bild von <?php echo htmlspecialchars($dish['name'], ENT_QUOTES, 'UTF-8'); ?>"
                         style="width: 150px;">
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p>
        Sortieren nach Name:
        <a href="?sort=ASC" <?php echo $sort === 'ASC' ? 'style="font-weight:bold;"' : ''; ?>>Aufsteigend</a> |
        <a href="?sort=DESC" <?php echo $sort === 'DESC' ? 'style="font-weight:bold;"' : ''; ?>>Absteigend</a>
    </p>

    <?php
    $sql_codes = "SELECT code, name FROM allergen ORDER BY code";
    $result_codes = mysqli_query($link, $sql_codes);
    echo "<ul style='text-align:left;'>";
    while ($row = mysqli_fetch_assoc($result_codes)) {
        echo "<li><strong>" .
                htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8') .
                "</strong> : " .
                htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') .
                "</li>";
    }
    echo "</ul>";
    ?>
</section>

<section id="zahlen">
    <h1 style="text-align:center;">E-Mensa in Zahlen</h1>
    <div class="containerZahlen">
        <div><?php echo $visitorCount; ?> Besuche</div>
        <div><?php echo $newsletterCount; ?> Anmeldungen zum Newsletter</div>
        <div><?php echo (int)$rowAnzahlgericht['anzahl_gericht']; ?> Speisen </div>
        <div class="wish-link-wrapper">
            <a href="wunschgericht.php" class="wish-link-button">
                Wunschgericht vorschlagen
            </a>
        </div>
    </div>
</section>

<section id="kontakt">
    <h1 style="text-align:center;">Interesse geweckt? Wir informieren Sie</h1>
    <form method="post">
        <div class="Container_Kontakt">
            <div>
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name" placeholder="Ihr Name"
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div>
                <label for="vorname">Vorname:</label><br>
                <input type="text" id="vorname" name="vorname" placeholder="Ihr Vorname"
                       value="<?php echo isset($_POST['vorname']) ? htmlspecialchars($_POST['vorname'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div>
                <label for="email">E-Mail:</label><br>
                <input type="email" id="email" name="email" placeholder="Ihre E-Mail Adresse"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div>
                <label for="sprache">Sprache:</label><br>
                <select id="sprache" name="sprache">
                    <option value="deutsch">Deutsch</option>
                    <option value="englisch">English</option>
                </select>
            </div>
            <div id="datensch">
                <label for="datenschutz">Datenschutz:</label>
                <input type="checkbox" id="datenschutz" name="datenschutz"
                        <?php echo isset($_POST['datenschutz']) ? 'checked' : ''; ?>>
                <span>Ich stimme den Datenschutzbedingungen zu</span>
            </div>

            <!-- CSRF-Token -->
            <input type="hidden" name="csrf_token"
                   value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <div>
                <button type="submit">Zum Newsletter anmelden</button>
            </div>
        </div>

        <?php
        if (!empty($Errors)) {
            echo "<div style='color:red; text-align:center; margin-top:10px;'>";
            foreach ($Errors as $Error) {
                echo htmlspecialchars($Error, ENT_QUOTES, 'UTF-8') . "<br>";
            }
            echo "</div>";
        }
        ?>
    </form>
</section>

<section id="wichtig">
    <h1 style="text-align:center;">Das ist uns Wichtig</h1>
    <div class="Container_Kontakt">
        <ul>
            <li>Beste frische saisonale Zutaten</li>
            <li>Ausgewogene abwechslungsreiche Gerichte</li>
            <li>Sauberkeit</li>
        </ul>
    </div>
    <h1 style="text-align:center;">Wir freuen uns auf Ihren Besuch!</h1>
</section>

<footer>
    <hr>
    <div class ="containerFooter">
        <div> (c) E-Mensa GmbH</div>
        <div> Adem / Amine</div>
        <div> <a href="werbeseite.php">Impressum</a></div>
    </div>
</footer>

</body>
</html>
<?php mysqli_close($link); ?>
