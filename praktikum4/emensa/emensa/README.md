# Routingscript MVC (E-Mensa)

Mini-MVC playground for the DBWT labs, now featuring newsletter signup, authentication, logging, dish photos, SQL views and stored procedures.

## Requirements

- PHP >= 8.2 with `mysqli`
- Composer (BladeOne + Monolog)
- MySQL/MariaDB database `emensawerbeseite`

## Quick Start

1. Install dependencies (first run only):
	 ```bash
	 cd praktikum4/emensa/emensa
	 php bin/composer.phar install
	 ```
2. Launch the built-in server:
	 ```bash
	 php -S 127.0.0.1:9000 -t public
	 ```
	 *(Windows users can run `start_server.bat` instead.)*
3. Open http://127.0.0.1:9000/ in your browser.

## Database Setup

1. Import the base dataset from `praktikum3/werbeseite/werbeseite_daten.sql`.
2. Run the additional SQL helpers in `beispiele/`:
	 - `m5_4.sql` → creates the report views `view_suppengerichte`, `view_anmeldungen`, `view_kategoriegerichte_vegetarisch`.
	 - `m5_5.sql` → creates the stored procedure `increment_user_login` used by the login workflow.
3. Insert the admin user with the salted hash from `config/auth.php` (see `beispiele/passwort.php` to generate hashes).

## Features

- **Home page** (`/`)
	- Dish list (sorting, allergen badges, real photos from `public/img/gerichte/` with fallback `00_image_missing.jpg`).
	- Newsletter signup with CSRF protection and validation.
	- Visitor stats, wish-dish link, accessibility tweaks.
	- Shows "Angemeldet als …" + logout link when a session exists.
- **Authentication**
	- `/anmeldung` form, `/anmeldung_verfizieren` handler, `/abmeldung` logout.
	- Passwords salted via `config/auth.php` and verified with `password_verify`.
	- Login success increments `anzahlanmeldungen` via stored procedure `increment_user_login`; failures update `anzahlfehler`/`letzterfehler`.
- **Logging (Monolog)**
	- Helper `logger()` in `public/index.php` writes to `storage/logs/emensa.log`.
	- Logs: homepage access (`info`), logins/logouts (`info`), failed logins (`warning`).
- **Blade exercises & legacy pages**
	- Routes `/m4_*`, `/wunschgericht`, demo controllers, etc., preserved for the practicum tasks.

## Folder Overview

- `bin/` – local Composer binary/bootstrapper.
- `config/` – DB + auth configuration.
- `controllers/` – application controllers (Home, Demo, Example, Auth, Legacy, ...).
- `models/` – database helper functions.
- `public/` – document root (front controller, assets, dish images, JS/CSS).
- `routes/` – simple route-to-controller map.
- `storage/` – Blade cache + runtime assets (`logs/`).
- `views/` – Blade templates.
- `beispiele/` – SQL scripts, CLI helpers (password hashing, view/procedure definitions).

## Verification Tips

- Tail the log while browsing:
	```bash
	tail -f storage/logs/emensa.log
	```
- Check SQL views:
	```sql
	SELECT * FROM view_suppengerichte;
	SELECT * FROM view_anmeldungen LIMIT 10;
	SELECT * FROM view_kategoriegerichte_vegetarisch LIMIT 20;
	```
- Test stored procedure manually:
	```sql
	CALL increment_user_login(<user_id>);
	```
	Confirm that `anzahlanmeldungen` and `letzteanmeldung` change for that user.

## FAQ

- **Where are the dish photos stored?** `public/img/gerichte/`. Filenames follow `<id>_anything.ext`. Missing images fall back automatically.
- **How do I add another admin?** Insert into `benutzer` with `anzahlanmeldungen = 0`, then use `beispiele/passwort.php <plaintext>` to generate a salted hash for `passwort`.
- **Can I extend logging?** Yes—call `logger()->info(...)` or other levels anywhere after including `public/index.php`.

