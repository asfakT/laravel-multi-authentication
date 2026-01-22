<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
# Laravel Multi Authentication (Breeze User + Custom Admin)

This project uses:
- **Laravel Breeze** for normal user authentication
- **Custom Admin authentication** (separate admin guard + admin routes)
- Admin features: Login, Dashboard, Logout, Forgot Password, Reset Password (token-based)

---

## What you need (Requirements)

- PHP 8.2+
- Composer
- MySQL + phpMyAdmin (or any DB client)
- Node.js + npm
- Mailtrap account (for testing email)

---

## Environment setup (IMPORTANT)

Create `.env` from `.env.example` and set:
- `APP_URL`
- Database settings (`DB_*`)
- Mail settings (`MAIL_*`)
- `SESSION_DRIVER=database`

Then run:
- `php artisan key:generate`

---

## Installation Process (Step-by-step)

1) Clone project
- `git clone <repo_url>`
- `cd <project_folder>`

2) Install backend dependencies
- `composer install`

3) Install frontend dependencies
- `npm install`

4) Create env + key
- Copy `.env.example` → `.env`
- `php artisan key:generate`

5) Database setup
- Create database in phpMyAdmin: `multi-auth`
- Confirm DB port (example: `3307` if you use that)
- Run migrations:
  - `php artisan migrate`
- Create session table (if not created):
  - `php artisan session:table`
  - `php artisan migrate`

6) Build frontend assets (Breeze UI)
- `npm run dev`

7) Run the project
- `php artisan serve`

App URL:
- `http://localhost:8000`

---

## Routes you must have (Admin)

Admin public routes:
- `GET  /admin/login`
- `POST /admin/login`
- `POST /admin/logout`
- `GET  /admin/forget-password`
- `POST /admin/forget-password-submit`
- `GET  /admin/reset-password/{token}/{email}`
- `POST /admin/reset-password-submit`

Admin protected route (middleware admin):
- `GET /admin/dashboard`

---

## Admin pages you must have (Views)

Create these Blade files:
- `resources/views/admin/login.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/forget-password.blade.php`
- `resources/views/admin/reset-password.blade.php`

Email template:
- `resources/views/email.blade.php`

---

## Controller methods you must have (AdminController)

Your `AdminController` must contain these methods:
- `dashboard()`
- `login()`
- `login_submit(Request $request)`
- `logout(Request $request)`
- `forget_password()`
- `forget_password_submit(Request $request)`
- `reset_password($token, $email)`
- `reset_password_submit(Request $request)`

---

## Database requirements

In your `admins` table you must have these columns:
- `id`
- `name`
- `email`
- `password` (hashed)
- `token` (nullable)
- timestamps

---

## Mail requirements (Forgot password email)

To send reset link:
- Mail config must be correct in `.env` (Mailtrap for local)
- A Mail class should exist (example: `Websitemail`)
- Email blade should show the link that you send

---

## Fix tips (Common problems)

### 1) “Invalid credentials” in another browser
Check these:
- In `admins` table, confirm the password is updated (hashed string changes)
- Make sure you don’t have duplicate admin rows with same email
- Clear session/cache:
  - `php artisan optimize:clear`
- Clear browser cookies for the site (especially admin guard sessions)

### 2) Password reset works but login fails
Most common causes:
- Password not hashed properly in DB
- You updated wrong user/admin row
- Auth guard config mismatch

---

## Admin URLs (Quick list)

- Admin Login: `http://localhost:8000/admin/login`
- Admin Dashboard: `http://localhost:8000/admin/dashboard`
- Forgot Password: `http://localhost:8000/admin/forget-password`

---
