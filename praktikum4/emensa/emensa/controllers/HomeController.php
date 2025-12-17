<?php



require_once($_SERVER['DOCUMENT_ROOT'].'/../models/gericht.php');

class HomeController
{
    public function index(RequestData $request) {
        $this->ensureSession();

        $sortDirection = $this->resolveSortDirection($request->getGetData());
        $postData = $request->getPostData();
        $formData = [
            'name' => trim($postData['name'] ?? ''),
            'vorname' => trim($postData['vorname'] ?? ''),
            'email' => trim($postData['email'] ?? ''),
            'sprache' => $postData['sprache'] ?? 'deutsch',
            'datenschutz' => isset($postData['datenschutz'])
        ];

        $errors = [];
        $success = false;

        if (strtoupper($request->method) === 'POST') {
            [$errors, $success] = $this->handleNewsletterSubmission($postData);
            if ($success) {
                $formData = [
                    'name' => '',
                    'vorname' => '',
                    'email' => '',
                    'sprache' => 'deutsch',
                    'datenschutz' => false
                ];
            }
        }

        $gerichte = $this->sortGerichte($this->fetchGerichte(), $sortDirection);
        $allergens = $this->fetchAllergens();
        $stats = $this->fetchStats();

        return view('home', [
            'gerichte' => $gerichte,
            'allergens' => $allergens,
            'sortDirection' => $sortDirection,
            'stats' => $stats,
            'errors' => $errors,
            'success' => $success,
            'formData' => $formData,
            'csrfToken' => $_SESSION['csrf_token'],
            'loggedInUser' => $this->getLoggedInUser()
        ]);
    }
    
    public function debug(RequestData $request) {
        return view('debug');
    }

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function resolveSortDirection(array $query): string
    {
        $direction = strtoupper($query['sort'] ?? 'ASC');
        return in_array($direction, ['ASC', 'DESC'], true) ? $direction : 'ASC';
    }

    private function fetchGerichte(): array
    {
        $link = connectdb();
        $sql = "SELECT g.id, g.name, g.beschreibung, g.erfasst_am, g.preisintern, g.preisextern,
                       g.bildname,
                       GROUP_CONCAT(gha.code ORDER BY gha.code SEPARATOR ', ') AS codes
                FROM gericht g
                LEFT JOIN gericht_hat_allergen gha ON g.id = gha.gericht_id
                GROUP BY g.id
                LIMIT 5";

        $result = mysqli_query($link, $sql);
        $gerichte = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

        foreach ($gerichte as &$gericht) {
            $gericht['image_path'] = $this->resolveDishImagePath($gericht);
        }
        unset($gericht);

        if ($result) {
            mysqli_free_result($result);
        }

        mysqli_close($link);
        return $gerichte ?: [];
    }

    private function sortGerichte(array $gerichte, string $direction): array
    {
        usort($gerichte, static function ($a, $b) use ($direction) {
            return $direction === 'ASC'
                ? strcmp($a['name'], $b['name'])
                : strcmp($b['name'], $a['name']);
        });

        return $gerichte;
    }

    private function fetchAllergens(): array
    {
        $link = connectdb();
        $sql = "SELECT code, name FROM allergen ORDER BY code";
        $result = mysqli_query($link, $sql);
        $allergens = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

        if ($result) {
            mysqli_free_result($result);
        }

        mysqli_close($link);
        return $allergens ?: [];
    }

    private function fetchStats(): array
    {
        $link = connectdb();
        mysqli_query($link, "UPDATE visitor_counter SET count = count + 1 WHERE id = 1");

        $visits = $this->fetchSingleValue($link, "SELECT count FROM visitor_counter WHERE id = 1", 'count');
        $newsletter = $this->fetchSingleValue($link, "SELECT COUNT(*) AS newsletterCount FROM newsletter", 'newsletterCount');
        $dishes = $this->fetchSingleValue($link, "SELECT COUNT(*) AS anzahl_gericht FROM gericht", 'anzahl_gericht');

        mysqli_close($link);

        return [
            'visits' => $visits,
            'newsletter' => $newsletter,
            'dishes' => $dishes
        ];
    }

    private function fetchSingleValue($link, string $sql, string $column): int
    {
        $result = mysqli_query($link, $sql);
        $value = 0;

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $value = (int)($row[$column] ?? 0);
            mysqli_free_result($result);
        }

        return $value;
    }

    private function handleNewsletterSubmission(array $postData): array
    {
        $errors = [];
        $success = false;

        if (!isset($postData['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $postData['csrf_token'])) {
            $errors[] = "Ungültiger Sicherheits-Token. Bitte laden Sie die Seite neu.";
            return [$errors, $success];
        }

        $name = trim($postData['name'] ?? '');
        $vorname = trim($postData['vorname'] ?? '');
        $email = trim($postData['email'] ?? '');
        $datenschutzAkzeptiert = isset($postData['datenschutz']);

        if ($name === '') {
            $errors[] = "Bitte geben Sie einen Namen ein.";
        }
        if ($vorname === '') {
            $errors[] = "Bitte geben Sie einen Vornamen ein.";
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Bitte geben Sie eine gültige E-Mail ein.";
        }
        if (!$datenschutzAkzeptiert) {
            $errors[] = "Bitte stimmen Sie den Datenschutzbestimmungen zu.";
        }

        $domain = strtolower(substr(strrchr($email, '@') ?: '', 1));
        $blockedDomains = ['trashmail.de', 'trashmail.com', 'wegwerfmail.de', 'wegwerfmail.com'];
        if ($domain !== '' && in_array($domain, $blockedDomains, true)) {
            $errors[] = "Diese E-Mail-Adresse ist nicht erlaubt.";
        }

        if (empty($errors)) {
            $link = connectdb();
            $stmt = mysqli_prepare($link, "INSERT INTO newsletter (name, vorname, email, erstellt_am) VALUES (?, ?, ?, ?)");

            if ($stmt) {
                $now = date('Y-m-d H:i:s');
                mysqli_stmt_bind_param($stmt, 'ssss', $name, $vorname, $email, $now);
                if (mysqli_stmt_execute($stmt)) {
                    $success = true;
                } else {
                    $errors[] = "Fehler beim Speichern der Daten. Bitte versuchen Sie es später erneut.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $errors[] = "Newsletter konnte nicht gespeichert werden.";
            }

            mysqli_close($link);
        }

        return [$errors, $success];
    }

    private function getLoggedInUser(): ?array
    {
        $email = $_SESSION['user_email'] ?? '';
        if ($email === '') {
            return null;
        }

        $link = connectdb();
        if ($link === false) {
            return ['email' => $email, 'displayName' => $email];
        }

        $sql = "SELECT name FROM benutzer WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt === false) {
            mysqli_close($link);
            return ['email' => $email, 'displayName' => $email];
        }

        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $name);

        $displayName = null;
        if (mysqli_stmt_fetch($stmt)) {
            $name = trim($name ?? '');
            $displayName = $name !== '' ? $name : null;
        }

        mysqli_stmt_close($stmt);
        mysqli_close($link);

        if ($displayName === null) {
            $displayName = $email;
        }

        return ['email' => $email, 'displayName' => $displayName];
    }

    private function resolveDishImagePath(array $gericht): string
    {
        $publicBase = '/img/gerichte/';
        $absoluteBase = dirname(__DIR__) . '/public/img/gerichte/';
        $fallback = $publicBase . '00_image_missing.jpg';

        $filename = trim($gericht['bildname'] ?? '');
        if ($filename !== '') {
            $absolutePath = $absoluteBase . $filename;
            if (file_exists($absolutePath)) {
                return $publicBase . $filename;
            }
        }

        $id = (int)($gericht['id'] ?? 0);
        if ($id > 0) {
            $patterns = [
                sprintf('%s%02d_*.*', $absoluteBase, $id),
                sprintf('%s%d_*.*', $absoluteBase, $id)
            ];

            foreach ($patterns as $pattern) {
                $matches = glob($pattern, GLOB_NOSORT);
                if (!empty($matches)) {
                    $firstMatch = basename($matches[0]);
                    if ($firstMatch !== '') {
                        return $publicBase . $firstMatch;
                    }
                }
            }
        }

        return $fallback;
    }
}