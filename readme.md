Laravel Boilerplate
===================

Build laravel backend rapidly.

## Requirements 

- Check requirements here https://laravel.com/docs/5.8#installation

### Installation

*Commands*

```bash
git clone https://github.com/kindlebit-web/laravel-boilerplate.git app-name
cd app-name
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

- Import the The Database ( get he DB schema from database/lb.sql )
- Make Database and SMTP configuration changes in .env file

```bash
php artisan serve
```

- Goto http://localhost:8000

Admin Panel login details

```
http://localhost:8000/admin
Email : admin@admin.com
Password : password
```

## Laravel Builder Usage

( Note : This link be accessed by admin only )

Goto : http://localhost:8000/generator_builder
