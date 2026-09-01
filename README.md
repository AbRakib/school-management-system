# School Management System

A modern school management platform built with Laravel, Vue 3, Inertia.js, TypeScript, and Tailwind CSS. It provides role-based dashboards and core modules for managing academic, administrative, financial, transport, and library workflows.

## Features

- Role-based dashboards for super admin, admin, teacher, student, parent, and accountant users
- Student, guardian, teacher, staff, class, subject, department, and designation management
- Student and staff attendance marking with attendance reports
- Exam setup, subject assignment, marks entry, grading scales, publishing, and report cards
- Fee types, fee structures, invoice generation, collection, dues, expenses, and account reports
- Timetable period setup, timetable builder, and teacher schedule views
- Library book categories, books, issue, return, and due tracking
- Transport vehicles, routes, stops, and student transport assignment
- Notice management and reporting modules
- Authentication, profile settings, security settings, passkeys, and two-factor support

## Tech Stack

- Laravel 13
- PHP 8.3+
- Vue 3
- Inertia.js
- TypeScript
- Tailwind CSS
- Vite
- Spatie Laravel Permission
- Laravel Fortify

## Requirements

- PHP 8.3 or higher
- Composer
- Node.js and npm
- MySQL, MariaDB, PostgreSQL, or SQLite

## Installation

Clone the repository:

```bash
git clone <repository-url>
cd school-management-system
```

Install PHP dependencies:

```bash
composer install
```

Install JavaScript dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Update your database settings in `.env`, then run migrations and seeders:

```bash
php artisan migrate --seed
```

Build frontend assets:

```bash
npm run build
```

## Running Locally

Start the Laravel server:

```bash
php artisan serve
```

In another terminal, start Vite:

```bash
npm run dev
```

You can also run the combined development script:

```bash
composer run dev
```

## Default Login

After running the seeders, you can log in with:

```txt
Email: admin@school.test
Password: password
```

Change these credentials before using the project outside local development.

## Useful Commands

Run tests:

```bash
php artisan test
```

Run code style checks:

```bash
composer run lint:check
```

Fix PHP code style:

```bash
composer run lint
```

Check frontend formatting:

```bash
npm run format:check
```

Check TypeScript types:

```bash
npm run types:check
```

Create a production build:

```bash
npm run build
```

## License

This project is open-sourced software licensed under the MIT license.
