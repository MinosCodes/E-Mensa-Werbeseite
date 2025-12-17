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
            header('Location: /');
            exit;
        }

        $this->recordFailedLogin($email);
        header('Location: /anmeldung?error=Anmeldung%20fehlgeschlagen');
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
        if ($email === '') {
            return;
        }

        $link = connectdb();
        if ($link === false) {
            return;
        }

        $sql = 'UPDATE benutzer SET anzahlanmeldungen = anzahlanmeldungen + 1, letzteanmeldung = NOW() WHERE email = ?';
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt === false) {
            mysqli_close($link);
            return;
        }

        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($link);
    }

    private function recordFailedLogin(string $email): void
    {
        if ($email === '') {
            return;
        }

        $link = connectdb();
        if ($link === false) {
            return;
        }

        $sql = 'UPDATE benutzer SET letzterfehler = NOW() WHERE email = ?';
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt === false) {
            mysqli_close($link);
            return;
        }

        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($link);
    }
}
