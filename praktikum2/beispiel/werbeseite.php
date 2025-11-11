<?php
/**
 * Praktikum DBWT. Autoren:
 * Adem, Essouei, 3730582
 * Mohamed-amine, Merdassi, 3729412
 */

include 'dishes.php';
echo "Test";

$Errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Name = trim($_POST["name"]);
    $Vorname = trim($_POST["vorname"]);
    $Email = trim($_POST["email"]);

    if(strlen($Name) === 0){
        $Errors[] = "Bitte geben Sie einen Namen ein.";
    }
    if(strlen($Vorname) === 0){
        $Errors[] = "Bitte geben Sie einen Vornamen ein.";
    }
    if(strlen($Email) === 0 || !filter_var($Email, FILTER_VALIDATE_EMAIL)){
        $Errors[] = "Bitte geben Sie eine gültige E-Mail ein.";
    }
    if(!isset($_POST["datenschutz"])){
        $Errors[] = "Bitte stimmen Sie den Datenschutzbestimmungen zu.";
    }

    $domain = strtolower(substr(strrchr($Email, "@"), 1));
    $UngultDomains = [
            "trashmail.de",
            "trashmail.com",
            "wegwerfmail.de",
            "wegwerfmail.com",
    ];

    if(in_array($domain, $UngultDomains)){
        $Errors[] = "Diese E-Mail-Adresse ist nicht erlaubt.";
    }

    // Save data only if no errors
    if(empty($Errors)){
        $file = fopen("newsletter.csv", "a");
        if($file){
            fputcsv($file, [$Name, $Vorname, $Email, date("Y-m-d H:i:s")], ",", "\"", "\\");
            fclose($file);
            echo "<p style='color:green;'>Vielen Dank, Ihre Anmeldung war erfolgreich!</p>";
        } else {
            $Errors[] = "Fehler beim Speichern der Daten. Bitte versuchen Sie es später erneut.";
        }
    }
}
$visitorFile = "visitors.txt";
$visitorCount = 0;
if(file_exists($visitorFile)){
    $visitorCount = (int)file_get_contents($visitorFile);
}
$visitorCount++;
file_put_contents($visitorFile, $visitorCount);

$dishesCount = count($dishesArray);

$newsletterFile = "newsletter.csv";
$newsletterCount = 0;
if(file_exists($newsletterFile)){
    $lines = file($newsletterFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $newsletterCount = count($lines);
}

?>


<!DOCTYPE html>
<html lang="en">
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
        .Description, .Speisen_Tabelle, .containerZahlen, .Container_Kontakt,.Container_Kontakt,.containerFooter {
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
            background-color: #ccc;
            color: #667;
            border: none;
            padding: 8px 16px;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #bbb;
        }
        button:active {
            background-color: #aaa;
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
            <th>Art</th>
            <th>Gericht</th>
            <th>Preis</th>
            <th>Werktag</th>
            <th>Wochenende</th>
            <th>Inhaltsstoffe</th>
            <th>Foto</th>
        </tr>
        <?php foreach ($dishesArray as $dish) { ?>
            <tr>
                <td><?php echo $dish['Art']; ?></td>
                <td><?php echo $dish['Gericht']; ?></td>
                <td><?php echo $dish['Preis']; ?></td>
                <td><?php echo $dish['Werktag']; ?></td>
                <td><?php echo $dish['Wochenende']; ?></td>
                <td><?php echo $dish['Inhaltsstoffe']; ?></td>

                <td>
                    <?php $imagePath = 'img/' . $dish['Bilddatei'];?>
                    <img src="<?php echo $imagePath; ?>" alt="Bild von <?php echo $dish['Gericht']; ?>" style="width: 150px;">
                </td>
            </tr>
        <?php } ?>
    </table>
</section>

<section id="zahlen">
    <h1 style="text-align:center;">E-Mensa in Zahlen</h1>
    <div class="containerZahlen">
        <div><?php echo $visitorCount; ?> Besuche</div>
        <div><?php echo $newsletterCount; ?> Anmeldungen zum Newsletter</div>
        <div><?php echo $dishesCount; ?> Speisen</div>
    </div>
</section>

<section id="kontakt">
    <h1 style="text-align:center;">Interesse geweckt? Wir informieren Sie</h1>
    <form method="post">
        <div class="Container_Kontakt">
            <div>
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name" placeholder="Ihr Name">
            </div>
            <div>
                <label for="vorname">Vorname:</label><br>
                <input type="text" id="vorname" name="vorname" placeholder="Ihr Vorname">
            </div>
            <div>
                <label for="email">E-Mail:</label><br>
                <input type="email" id="email" name="email" placeholder="Ihre E-Mail Adresse">
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
                <input type="checkbox" id="datenschutz" name="datenschutz">
                <span>Ich stimme den Datenschutzbedingungen zu</span>
            </div>
            <div>
                <button type="submit">Zum Newsletter anmelden</button>
            </div>
        </div>

        <?php
        if(!empty($Errors)){
            echo "<div style='color:red; text-align:center; margin-top:10px;'>";
            foreach($Errors as $Error){
                echo $Error . "<br>";
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
            <li>Beste Frische Saisonale Zutaten</li>
            <li>Ausgewagene abwechslungsreiche Gerichte</li>
            <li>Sauberkait</li>
        </ul>

    </div>
    <h1 style="text-align:center;">Wir Freuen Uns auf Ihren Besuch!</h1>

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