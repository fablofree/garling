# Garage A. Lingiah — Installation Guide

## Requirements

- PHP 8.1+
- PostgreSQL 13+
- Apache 2.4+ with `mod_rewrite` enabled
- PHP extensions: `pdo`, `pdo_pgsql`, `mbstring`, `json`

## Quick Setup

### 1. Create the PostgreSQL database

```sql
CREATE DATABASE garage_lingiah;
```

### 2. Configure the application

Edit `config/database.php` (or set environment variables):

```php
'host'     => '127.0.0.1',
'port'     => '5432',
'database' => 'garage_lingiah',
'username' => 'postgres',
'password' => 'your_password',
```

### 3. Run the database migrations

```bash
php setup.php
```

Or run the SQL files manually:
```bash
psql -U postgres -d garage_lingiah -f database/migrations/001_initial_schema.sql
psql -U postgres -d garage_lingiah -f database/seeds/001_seed_data.sql
```

### 4. Configure Apache Virtual Host

```apache
<VirtualHost *:80>
    ServerName garage.local
    DocumentRoot /path/to/garling/public

    <Directory /path/to/garling/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/garage_error.log
    CustomLog ${APACHE_LOG_DIR}/garage_access.log combined
</VirtualHost>
```

**Important:** Make sure `mod_rewrite` is enabled:
```bash
a2enmod rewrite
service apache2 restart
```

### 5. If running in a subdirectory (e.g., `/garling/public`)

Update `config/app.php`:
```php
'url' => 'http://localhost/garling/public',
```

### 6. Login

- URL: `http://your-domain/`
- Username: `admin`
- Password: `admin123`

**Change the password immediately after first login!**

### 7. Delete setup.php

After successful setup, delete `setup.php` for security:
```bash
rm setup.php
```

## File Structure

```
garling/
├── config/          # App and database config
├── database/        # Migrations and seed data
├── public/          # Web root (point Apache here)
│   ├── index.php    # Entry point
│   ├── .htaccess    # URL rewriting rules
│   └── assets/      # CSS, JS
├── src/
│   ├── Core/        # Router, Request, Response, Database, Session
│   ├── Controllers/ # HTTP Controllers
│   ├── Models/      # Value objects
│   ├── Repositories/# Data access layer
│   ├── Services/    # Business logic
│   └── Views/       # PHP templates
└── setup.php        # One-time setup script (delete after use)
```

## Troubleshooting

**500 error on first load:**
- Check `config/database.php` connection settings
- Ensure PostgreSQL is running
- Check Apache error log

**404 on all pages:**
- Ensure `mod_rewrite` is enabled
- Ensure `AllowOverride All` is set in Apache config
- Check `.htaccess` exists in `public/`

**Blank page / PHP errors:**
- Set `APP_DEBUG=true` in your environment
- Check PHP error log


Add a logo in PNG and add it to favicon, in the dashboard allow the admin to manage logo and other application settings 
When the user exit the input typing of Odometer Reading for add or edit Service Entry automatically calculate Next Servicing At by using the value Servicing Frequency entered in vehicule
Make all integer fields integer because now when we click on upper arrow we have 0.01 by example instead of 1
Add Batteries Types etc management and allow the admin to manage
Allow the connected user to edit his profile
Add user management to allow the admin to add another users
Allow the admin to manage Marke, Model, Vehicule Type and colour. When adding or modifying a Vehicule allow the user to select or search each of these if not exist allow the user
to create (open a small popup) and automatically select
In Vehicule on Servicing Frequency just display the select Distance Unit selected