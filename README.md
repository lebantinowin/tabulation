# Tabulation System

A Laravel-based system for managing events, contestants, and judging scores.

## Features
- Admin dashboard for managing events, criteria, contestants, and judges.
- Judge portal for entering scores (with isolated events and auto-save).
- Public view for showing live or completed event results.
- Automated score weighting and rank calculation.
- Tabulation override functionality for tie-breakers and adjustments.

## Setup Requirements
- PHP 8.2+
- Composer
- Node.js & npm
- SQLite / MySQL / PostgreSQL (default is SQLite)

## Installation

1. **Clone the repository and install dependencies**
```bash
git clone <repository-url>
cd tabulation
composer install
npm install
```

2. **Environment Setup**
Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env
```
Update the `.env` file with your database credentials (by default it uses SQLite).
Generate the application key:
```bash
php artisan key:generate
```

3. **Database Migration & Seeding**
Run the database migrations:
```bash
php artisan migrate
```
*(Optional)* Run seeders if available:
```bash
php artisan db:seed
```

4. **Asset Compilation**
Build frontend assets:
```bash
npm run dev
# or for production: npm run build
```

5. **Start the application**
```bash
php artisan serve
```

## Security Notice
Please ensure your `.env` file is NOT committed to version control. If deploying to production, verify that `APP_DEBUG` is set to `false`.
