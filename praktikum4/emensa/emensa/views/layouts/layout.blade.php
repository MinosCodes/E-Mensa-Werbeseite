<!doctype html>
<html lang="DE">
    <head>
        <meta charset="utf-8">
        <title>E-Mensa Routing Script</title>
        <meta name="description" content="yet another small MVC script">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/default.min.css">

        @yield("cssextra")

        <meta name="theme-color" content="#dadada">
        <!-- good developers check the markup ;) -->
    </head>
    <body>
        <div class="container">
            <div class="row">
                @yield("content")
            </div>
        </div>

    @yield("jsextra")

    </body>
</html>
