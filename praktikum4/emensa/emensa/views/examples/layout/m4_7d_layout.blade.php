<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Layout-Demo' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            color-scheme: light;
            font-family: 'Source Sans Pro', Arial, sans-serif;
            --accent: #00796b;
            --bg: #f4f7f8;
        }
        body {
            margin: 0;
            background: var(--bg);
            color: #1f2933;
        }
        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header, footer {
            background: var(--accent);
            color: #fff;
            padding: 1.5rem;
        }
        main {
            flex: 1;
            padding: 2rem 1.5rem;
        }
    </style>
</head>
<body>
    <div class="page">
        <header>
            @yield('header')
        </header>
        <main>
            @yield('main')
        </main>
        <footer>
            @yield('footer')
        </footer>
    </div>
</body>
</html>
