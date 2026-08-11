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



In Service Entry, remove Qty, Unit price and only add Amount
When clicking in Amount add next row

Remove: Monthly Revenue, Today's Revenue, Weekly Revenue in dashboard	

Add BRN, VAT Reg, Address, Tel, email in application settings

Payment Date must not be after today date

For all date input use format dd/mm/YYYY

Add in customer form and invoice: BRN(Business Registration Number), VAT
In Invoice add: VAT INVOICE in middle, No: 000001, Signature in bottom left, Customer signature in bottom right, also display the logo manage by the admin,
in invoice only display Description and amount, display spare parts in a column and Repairs / Labour in other column so side bar side
Only add Next Service when it's an invoice
In Quotation add Remarks value down, separate quotation from Invoice manage them in different tables and only generate an invoice when the admin click on print


Add invoices in Management display in the list (id, date, Customer name, Vehicule registration) also add research by (id, customer name or vehicule registration)

In service entry put Type below Discount (Rs), only generate an invoice when the user click on print

Now I want to use Mysql as database configure accordingly, remove everything concerning docker and setup I will just execute the sql scripts manually