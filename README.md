## Local Flashcards — Setup & Usage

### Prerequisites

1. PHP 8.x
2. MySQL 8.x (or MariaDB) — easiest via XAMPP
3. Composer is NOT required (no external PHP deps)
4. Node.js 18+ (for Tailwind CSS build)

### Option A: XAMPP (Recommended on Windows/macOS)

1. Install XAMPP from: https://www.apachefriends.org/
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Place this project inside the XAMPP web root:
   - Windows: C:\\xampp\\htdocs\\local-flashcards
   - macOS: /Applications/XAMPP/xamppfiles/htdocs/local-flashcards
4. Ensure PHP can write inside the project (or you will create the config manually on first run).

Database defaults used by the app:
- host: localhost
- user: root (XAMPP default)
- pass: "" (empty password is default on many XAMPP installs)
- database: local_flashcards

The app can create the database tables automatically. If PHP cannot write a config file, it will guide you to create `src/db_config.php` manually.

Access the app in a browser after you start Apache/MySQL:
- http://localhost/local-flashcards/src/

### Option B: PHP built-in server (Linux/macOS)

You still need a running MySQL/MariaDB server accessible via `localhost`.

```
php -S localhost:8080 -t src
```

Then open: http://localhost:8080/

### Database Configuration

On first run, the app checks for `src/db_config.php`. If missing, it redirects to `src/setup.php` which either:
- validates a manually created file, then
- attempts to connect and create the `user` table if it does not exist.

Manual config file template (create `src/db_config.php` if prompted):

```php
<?php
return [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'name' => 'local_flashcards'
];
```

Notes:
- Use the XAMPP defaults (`root`/empty password) unless you have changed them.
- Ensure the `local_flashcards` database exists or that your MySQL user can create tables within it.

### Tailwind CSS

Install Tailwind CLI once (already listed in `package.json`):
```
npm install
```

Import Tailwind in your CSS (already present in `src/input.css`):
```
@import "tailwindcss";
```

Build CSS (watch mode during development):
```
npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --watch
```

### Running the App

1. Start MySQL (XAMPP or local service)
2. Start the web server
   - XAMPP: browse to http://localhost/local-flashcards/src/
   - PHP built-in: `php -S localhost:8080 -t src` then open http://localhost:8080/
3. First visit will redirect to `setup.php` if configuration is missing. Follow the instructions to create `src/db_config.php` (auto or manual). On success you will be redirected to the app.
4. Sign up a user at `/src/signup.php`, then sign in at `/src/login.php`.
5. Create your first flashcard set at `/src/create_flashcards.php`.

### App Structure (key files)

- `src/connect_db.php`: loads `src/db_config.php`, establishes PDO connection, redirects to `setup.php` if missing/invalid
- `src/setup.php`: verifies config and creates `user` table if needed
- `src/user_db.php`: helper functions `createUser(user_id, hashedPassword)` and `getUserByUserId(user_id)`
- `src/login.php` / `src/signup.php` / `src/logout.php`: authentication flows
- `src/index.php`: home screen (requires login)
- `src/create_flashcards.php`: creates tables named `set_<your_set>` and inserts terms
- `src/flashcard_list.php`: lists/view terms in a set
- `src/edit_flashcards.php`: edit existing set rows
- `src/header.php`: shared navigation
- `src/input.css` → `src/output.css`: Tailwind input/output

### Troubleshooting

- 403/404 on XAMPP: ensure the project folder is inside `htdocs` and you are visiting `/src/` path.
- Cannot write `db_config.php`: create the file manually using the template above, then click the verify button on `setup.php`.
- MySQL connection error: verify XAMPP MySQL is running, and that user/password match your local MySQL setup.
- CSS not applied: ensure Tailwind watcher built `src/output.css` and the file exists.

### Security Notes (local only)

- Default `root` with empty password is acceptable only for local development. For any shared machine, set a strong password and update `src/db_config.php` accordingly.