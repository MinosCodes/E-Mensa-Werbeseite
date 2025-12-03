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

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF-Check
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $errors[] = "Ungültiger Sicherheits-Token. Bitte laden Sie die Seite neu.";
    } else {

        // Creator data
        $erstellerName  = trim($_POST['ersteller_name'] ?? '');
        $erstellerEmail = trim($_POST['ersteller_email'] ?? '');

        // Dish data
        $gerichtName    = trim($_POST['gericht_name'] ?? '');
        $beschreibung   = trim($_POST['beschreibung'] ?? '');

        // If no creator name -> anonym
        if ($erstellerName === '') {
            $erstellerName = 'anonym';
        }

        // Simple validation
        if ($erstellerEmail === '' || !filter_var($erstellerEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Bitte geben Sie eine gültige E-Mail für den Ersteller ein.";
        }
        if ($gerichtName === '') {
            $errors[] = "Bitte geben Sie einen Namen für das Wunschgericht ein.";
        }

        if (empty($errors)) {
            mysqli_begin_transaction($link);

            try {
                // 1. Check if creator already exists (by email)
                $stmtCheck = mysqli_prepare(
                    $link,
                    "SELECT id FROM ersteller WHERE email = ? LIMIT 1"
                );
                mysqli_stmt_bind_param($stmtCheck, "s", $erstellerEmail);
                mysqli_stmt_execute($stmtCheck);
                mysqli_stmt_bind_result($stmtCheck, $existingId);
                $hasRow = mysqli_stmt_fetch($stmtCheck);
                mysqli_stmt_close($stmtCheck);

                if ($hasRow) {
                    // reuse existing creator
                    $erstellerId = $existingId;
                } else {
                    // 2. Insert new creator
                    $stmtErst = mysqli_prepare(
                        $link,
                        "INSERT INTO ersteller (name, email) VALUES (?, ?)"
                    );
                    mysqli_stmt_bind_param($stmtErst, "ss", $erstellerName, $erstellerEmail);
                    mysqli_stmt_execute($stmtErst);
                    $erstellerId = mysqli_insert_id($link);
                    mysqli_stmt_close($stmtErst);
                }

                // 3. Insert wish dish
                $now = date('Y-m-d H:i:s');
                $stmtWunsch = mysqli_prepare(
                    $link,
                    "INSERT INTO wunschgericht (name, beschreibung, erstellt_am, ersteller_id)
                     VALUES (?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmtWunsch, "sssi", $gerichtName, $beschreibung, $now, $erstellerId);
                mysqli_stmt_execute($stmtWunsch);
                mysqli_stmt_close($stmtWunsch);

                mysqli_commit($link);
                $success = true;
                $_POST = [];

            } catch (Throwable $e) {
                mysqli_rollback($link);
                $errors[] = "Fehler beim Speichern des Wunschgerichts.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Wunschgericht vorschlagen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fafafa;
            margin: 0;
        }
        .wish-form-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .wish-form-box {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
        }
        .wish-form-box h1 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #0073aa;
            text-align: center;
        }
        .wish-form-box h3 {
            margin-bottom: 8px;
            margin-top: 15px;
            color: #333;
        }
        .wish-form-box label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .wish-form-box input[type="text"],
        .wish-form-box input[type="email"],
        .wish-form-box textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .wish-form-box textarea {
            resize: vertical;
            min-height: 80px;
        }
        .wish-form-box button {
            background-color: #0073aa;
            color: #ffffff;
            border: none;
            padding: 10px 22px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            display: block;
            width: 100%;
        }
        .wish-form-box button:hover {
            background-color: #005f88;
        }
        .message-success {
            color: green;
            margin-bottom: 10px;
            text-align: center;
        }
        .message-error {
            color: red;
            margin-bottom: 10px;
        }
        .back-link {
            text-align: center;
            margin-top: 10px;
        }
        .back-link a {
            color: #0073aa;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="wish-form-container">
    <div class="wish-form-box">
        <h1>Wunschgericht vorschlagen</h1>

        <?php if ($success): ?>
            <div class="message-success">
                Vielen Dank! Ihr Wunschgericht wurde gespeichert.
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="message-error">
                <?php foreach ($errors as $e): ?>
                    <?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . "<br>"; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <h3>Ersteller:in</h3>

            <label for="ersteller_name">Name (leer = anonym)</label>
            <input type="text" id="ersteller_name" name="ersteller_name"
                   value="<?php echo isset($_POST['ersteller_name']) ? htmlspecialchars($_POST['ersteller_name'], ENT_QUOTES, 'UTF-8') : ''; ?>">

            <label for="ersteller_email">E-Mail</label>
            <input type="email" id="ersteller_email" name="ersteller_email"
                   value="<?php echo isset($_POST['ersteller_email']) ? htmlspecialchars($_POST['ersteller_email'], ENT_QUOTES, 'UTF-8') : ''; ?>">

            <h3>Wunschgericht</h3>

            <label for="gericht_name">Name des Wunschgerichts</label>
            <input type="text" id="gericht_name" name="gericht_name"
                   value="<?php echo isset($_POST['gericht_name']) ? htmlspecialchars($_POST['gericht_name'], ENT_QUOTES, 'UTF-8') : ''; ?>">

            <label for="beschreibung">Beschreibung / Hinweise</label>
            <textarea id="beschreibung" name="beschreibung"><?php
                echo isset($_POST['beschreibung']) ? htmlspecialchars($_POST['beschreibung'], ENT_QUOTES, 'UTF-8') : '';
                ?></textarea>

            <!-- CSRF-Token -->
            <input type="hidden" name="csrf_token"
                   value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <button type="submit">Wunsch abschicken</button>
        </form>

        <div class="back-link">
            <a href="werbeseite.php">Zurück zur Hauptseite</a>
        </div>
    </div>
</div>

</body>
</html>
<?php mysqli_close($link); ?>
