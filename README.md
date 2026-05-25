# ISM Data Science Summit

A Symfony-based summit registration and profile management web application for the ISM Data Science Summit.

## Project Overview

This project allows users to create an account, manage their profile, register for summit locations, view bookings, and cancel upcoming bookings. It also includes admin pages for managing summit locations and registrations.

## Main Features

- User registration and login
- Profile display and profile editing
- Profile change logging
- Summit location management for admins
- Summit booking system for logged-in users
- Capacity check before booking
- My Bookings page with upcoming and previous bookings
- Booking cancellation for upcoming active bookings
- Admin registration management
- Excel-compatible registration export
- Responsive ISM-style university event UI

## Tech Stack

- PHP 8.2+
- Symfony 7.4
- Doctrine ORM
- Twig
- Bootstrap 5
- MySQL or MariaDB

## Important Routes

| Page | Route |
| --- | --- |
| Login | `/login` |
| Register | `/register` |
| Profile | `/profile` |
| Edit Profile | `/profile/edit` |
| Summit Register | `/summit/register` |
| My Bookings | `/my-bookings` |
| Admin Summit Locations | `/admin/summit-location` |
| Admin Registrations | `/admin/registrations` |
| Admin Export | `/admin/registrations/export` |

## Installation

Clone the repository:

```bash
git clone https://github.com/tahabutt2004/summit-app.git
cd summit-app
```

Install dependencies:

```bash
composer install
```

Create and configure your local environment file:

```bash
cp .env .env.local
```

Update `DATABASE_URL` in `.env.local` according to your local database.

Create or update the database schema:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

If migrations are already applied locally and only schema sync is needed:

```bash
php bin/console doctrine:schema:update --force
```

Clear cache:

```bash
php bin/console cache:clear
```

Run the project:

```bash
php -S 127.0.0.1:8001 -t public
```

Open in browser:

```text
http://127.0.0.1:8001/login
```

## Admin Access

Admin pages require a user with `ROLE_ADMIN`. Assign the admin role in the database for the required user account.

Example role value:

```json
["ROLE_ADMIN"]
```

## Useful Commands

Check Doctrine mapping:

```bash
php bin/console doctrine:mapping:info
```

Clear cache:

```bash
php bin/console cache:clear
```

Run local development server:

```bash
php -S 127.0.0.1:8001 -t public
```

## Notes

- The project uses the `Taha` entity as the user entity.
- The `vendor/` and `var/` folders are ignored and should not be uploaded to GitHub.
- Use `composer install` after cloning to restore dependencies.
