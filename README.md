PHP Web Portal

This repository contains a small PHP-based web portal application. The main technologies used in the project are:

- **Backend**: PHP (Composer)
- **Frontend**: HTML, JavaScript, CSS (Tailwind)
- **Web server**: nginx
- **CI/CD**: GitHub Actions

The application fetches data from a remote API and displays it in a table, with basic search/filter functionality and a modal for selecting and previewing an image.

Directory highlights:

- `public/` — static assets and public entry points (`index.php`, `login.php`, `logout.php`)
- `src/` — application logic, controllers and components
- `storage/sessions/` — example session files
- `vendor/` — dependencies (composer)

Getting started (local):

1. Clone the repository:

```bash
git clone https://github.com/end-y/php_web_portal.git
cd php_web_portal
```

2. Install PHP dependencies:

```bash
composer install
```

3. For local development you can use the built-in PHP server:

```bash
php -S localhost:8000 -t public router.php
```

Main features:

- Authorized requests to a remote API and fetching a task list
- Table rendering of `task`, `title`, `description`, `colorCode`
- Search and filtering
- Automatic refresh every 60 minutes (data fetched via backend)
- Modal: select a file and preview the selected image

Notes and next steps:

- Consider using `.env` for environment variables (e.g. API credentials) and `vlucas/phpdotenv` for local development.
- Harden production configuration with HTTPS, security headers and proper PHP-FPM tuning.

Enjoy! 🎉
