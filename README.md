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

The app can create the database tables automatically. If PHP cannot write a config file, it will guide you to create `config/db_config.php` manually.

Access the app in a browser after you start Apache/MySQL:
- http://localhost/local-flashcards/

### Option B: PHP built-in server (Linux/macOS)

You still need a running MySQL/MariaDB server accessible via `localhost`.

```
npm run serve
# or manually:
php -S localhost:8080 -t pages
```

Then open: http://localhost:8080/

### Database Configuration

On first run, the app checks for `config/db_config.php`. If missing, it redirects to `config/setup.php` which either:
- validates a manually created file, then
- attempts to connect and create the `user` table if it does not exist.

Manual config file template (create `config/db_config.php` if prompted, or copy from `config/db_config_template.php`):

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

Import Tailwind in your CSS (already present in `assets/css/input.css`):
```
@import "tailwindcss";
```

Build CSS (watch mode during development):
```
npm run watch-css
# or manually:
npx @tailwindcss/cli -i ./assets/css/input.css -o ./assets/css/output.css --watch
```

### Running the App

1. Start MySQL (XAMPP or local service)
2. Start the web server
   - XAMPP: browse to http://localhost/local-flashcards/
   - PHP built-in: `npm run serve` then open http://localhost:8080/
3. First visit will redirect to `config/setup.php` if configuration is missing. Follow the instructions to create `config/db_config.php` (auto or manual). On success you will be redirected to the app.
4. Sign up a user at `/pages/signup.php`, then sign in at `/pages/login.php`.
5. Create your first flashcard set at `/pages/create_flashcards.php`.

### App Structure (key files)

```
local-flashcards/
├── assets/css/                    # Stylesheets
│   ├── input.css                  # Tailwind source
│   └── output.css                 # Compiled CSS
├── config/                        # Configuration files  
│   ├── database_connection.php    # Database connection logic
│   ├── db_config_template.php     # Config template
│   ├── db_config.php             # Database credentials (create from template)
│   └── setup.php                 # Initial setup helper
├── includes/                      # Shared components
│   ├── header.php                # Navigation header
│   └── user_functions.php        # User-related database functions
├── pages/                         # Application pages
│   ├── index.php                 # Home screen (requires login)
│   ├── login.php / signup.php    # Authentication flows
│   ├── logout.php                # Logout functionality
│   ├── create_flashcards.php     # Create new flashcard sets
│   ├── flashcard_list.php        # View flashcard sets
│   ├── edit_flashcards.php       # Edit existing flashcards
│   └── profile.php               # User profile (placeholder)
└── index.html                     # Root redirect to pages/
```

### Troubleshooting

- 403/404 on XAMPP: ensure the project folder is inside `htdocs` and you are visiting the root path.
- Cannot write `db_config.php`: create the file manually using the template above, then click the verify button on `config/setup.php`.
- MySQL connection error: verify XAMPP MySQL is running, and that user/password match your local MySQL setup.
- CSS not applied: ensure Tailwind watcher built `assets/css/output.css` and the file exists.
- Path errors: make sure you're using the new directory structure with `pages/`, `config/`, etc.

### Security Notes (local only)

- Default `root` with empty password is acceptable only for local development. For any shared machine, set a strong password and update `config/db_config.php` accordingly.
- The `config/db_config.php` file is automatically excluded from git to protect your database credentials.