<?php

class AuthController
{
    private array $authConfig;

    public function __construct()
    {
        $this->authConfig = include dirname(__DIR__) . '/config/auth.php';
    }

    public function show(RequestData $rd): string
    {
        $error = $rd->query['error'] ?? '';
        return view('auth.login', ['error' => $error]);
    }

    public function verify(RequestData $rd)
    {
        $email = trim($rd->getPostData()['email'] ?? '');
        $password = trim($rd->getPostData()['password'] ?? '');

        if ($this->isAuthenticated($email, $password)) {
            $this->recordSuccessfulLogin($email);
            $_SESSION['user_email'] = $email;
            logger()->info('User logged in', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            header('Location: /');
            exit;
        }

        $this->recordFailedLogin($email);
        logger()->warning('Failed login attempt', [
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        header('Location: /anmeldung?error=Anmeldung%20fehlgeschlagen');
        exit;
    }

    public function logout(RequestData $rd): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = $_SESSION['user_email'] ?? null;
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();

        logger()->info('User logged out', [
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        header('Location: /');
        exit;
    }

    private function isAuthenticated(string $email, string $password): bool
    {
        $salt = $this->authConfig['salt'] ?? '';
        $users = $this->authConfig['users'] ?? [];
        $storedHash = $users[$email] ?? null;

        if ($storedHash === null || $salt === '' || $password === '') {
            return false;
        }

        return password_verify($salt . $password, $storedHash);
    }

    private function recordSuccessfulLogin(string $email): void
    {
        $this->executeLoginTransaction($email, function ($link, $mail) {
            $userId = $this->findUserIdByEmail($link, $mail);
            if ($userId === null) {
                throw new RuntimeException('Benutzer konnte nicht ermittelt werden.');
            }

            $stmt = mysqli_prepare($link, 'CALL increment_user_login(?)');
            if ($stmt === false) {
                throw new RuntimeException('Konnte Prozedur increment_user_login nicht vorbereiten.');
            }

            mysqli_stmt_bind_param($stmt, 'i', $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            while (mysqli_more_results($link)) {
                mysqli_next_result($link);
            }
        });
    }

    private function recordFailedLogin(string $email): void
    {
        $this->executeLoginTransaction($email, static function ($link, $mail) {
            $sql = 'UPDATE benutzer SET letzterfehler = NOW(), anzahlfehler = anzahlfehler + 1 WHERE email = ?';
            $stmt = mysqli_prepare($link, $sql);

            if ($stmt === false) {
                throw new RuntimeException('Konnte Fehler-Statement nicht vorbereiten.');
            }

            mysqli_stmt_bind_param($stmt, 's', $mail);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        });
    }

    private function executeLoginTransaction(string $email, callable $operation): void
    {
        if ($email === '') {
            return;
        }

        $link = connectdb();
        if ($link === false) {
            return;
        }

        mysqli_begin_transaction($link);

        try {
            $operation($link, $email);
            mysqli_commit($link);
        } catch (Throwable $e) {
            mysqli_rollback($link);
        } finally {
            mysqli_close($link);
        }
    }

    private function findUserIdByEmail($link, string $email): ?int
    {
        $stmt = mysqli_prepare($link, 'SELECT id FROM benutzer WHERE email = ? LIMIT 1');
        if ($stmt === false) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userId);

        $result = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($result === true && $userId !== null) {
            return (int)$userId;
        }

        return null;
    }
}
