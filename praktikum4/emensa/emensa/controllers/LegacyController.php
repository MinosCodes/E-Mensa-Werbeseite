<?php

class LegacyController
{
    public function wunschgericht(): string
    {
        $legacyFile = dirname(__DIR__, 3) . '/werbeseite/wunschgericht.php';

        if (!file_exists($legacyFile)) {
            return view('notimplemented', [
                'request' => null,
                'url' => '/wunschgericht'
            ]);
        }

        ob_start();
        include $legacyFile;
        $output = ob_get_clean() ?: '';

        return str_replace('href="werbeseite.php"', 'href="/"', $output);
    }
}
