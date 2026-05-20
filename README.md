# Sembark URL Shortener

A multi-company URL shortener built as part of the Sembark backend assignment.

The application is built using Laravel 13 with Inertia.js and Vue 3.  
It supports role-based access control, invitation management, company-based URL visibility, and hit tracking for shortened URLs.

---

# Tech Stack

## Backend
- Laravel 13
- Laravel Fortify
- Eloquent ORM
- SQLite / MySQL

## Frontend
- Vue 3
- Inertia.js v3
- Tailwind CSS v4
- Vite

## Testing
- Pest PHP

---


# Features

- Multi-company architecture
- Role-based authorization
- URL shortening and redirect tracking
- Invitation management
- Company-level data isolation
- Hit counting
- Dashboard and profile settings
- Optimized Eloquent queries using aggregates

---

# User Roles

## Super Admin
- View all companies
- View all short URLs
- Invite users to any company

## Admin
- Create short URLs
- View URLs inside own company
- Invite users inside own company

## Member
- Create short URLs
- View only self-created URLs

---

# Installation

## Clone the repository

```bash
git clone <repository-url>
cd sembark-url-shortener
```

### Install dependencies

```bash
composer install
npm install
```

### Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use `copy .env.example .env` instead of `cp`.

### Database setup

SQLite is the default connection in `.env.example`.

Create the SQLite database file:

**Linux / macOS**

```bash
touch database/database.sqlite
```

**Windows PowerShell**

```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

Update `.env` if you want to use MySQL instead.

### Run migrations and seeders

```bash
php artisan migrate --seed
```

`RoleSeeder` must run before `SuperAdminSeeder` (order is set in `DatabaseSeeder`).

### Start development server

```bash
composer run dev
```

This command runs:

- Laravel development server
- Queue worker
- Vite dev server

Open: [http://127.0.0.1:8000](http://127.0.0.1:8000)

### Default Super Admin

| Field | Value |
|-------|-------|
| Email | `superadmin@sembark.test` |
| Password | `password` |

---

# Short URL resolution

Short URLs are resolved using:

```
GET /s/{code}
```

The endpoint:

- validates the short code
- increments hit count
- redirects to the original URL

---

# Authorization

Authorization is handled using:

- Laravel Policies
- `RoleAbility` helper class
- Query scopes for visibility filtering

Main files:

- `app/Policies/ShortUrlPolicy.php`
- `app/Policies/InvitationPolicy.php`
- `app/Support/RoleAbility.php`

| Role slug | Create short URLs | View short URL list |
|-----------|-------------------|---------------------|
| `super_admin` | No | All URLs |
| `admin` | Yes | URLs in own company |
| `member` | Yes | URLs created by self |

---

# Invitations

Admins and Super Admins can invite users based on role permissions.

## Invitation rules

| Inviter | Scope | Allowed roles |
|---------|-------|---------------|
| Super Admin | Any company | `admin`, `member` |
| Admin | Own company only | `admin`, `member` |

Invitations:

- create the user immediately
- store invitation records
- assign company and role relationships

---

# Query optimization

The project uses Eloquent aggregates to reduce unnecessary queries.

Examples:

- `withCount()`
- `withSum()`
- eager loading
- scoped visibility queries

This helps avoid N+1 query issues and improves dashboard performance.

---

# Testing

Run tests using:

```bash
php artisan test
```

or

```bash
composer test
```

## Main test coverage

| File | Purpose |
|------|---------|
| `UrlShortenerTest.php` | URL creation, visibility, redirects |
| `InvitationTest.php` | Invitation authorization rules |

Additional suites cover Fortify auth, dashboard, and settings.

---

# Project structure

```
app/
├── Http/Controllers
├── Models
├── Policies
├── Support

database/
├── migrations
├── seeders

resources/js/
├── pages

routes/
├── web.php

tests/
├── Feature
```

---

# Useful commands

**Run formatter**

```bash
composer lint
```

**Run CI checks**

```bash
composer ci:check
```

---

# Notes

- Policies are used for authorization handling
- Role logic is centralized inside `RoleAbility.php`
- URL visibility is filtered using query scopes
- Fortify handles authentication and security features
- Inertia.js is used to keep frontend and backend integration simple

---

# AI usage

# AI Usage

AI-assisted development tools were used during the implementation of this assignment to improve development speed, debugging, and code refinement.

The overall application architecture, database structure, feature flow, and authorization logic were planned and validated manually during development. AI tools were primarily used as an implementation assistant for repetitive coding tasks, query optimization suggestions, UI refinements, and documentation support.

Examples of AI-assisted work include:
- Generating boilerplate code and repetitive implementations
- Assisting with Vue/Inertia UI enhancements
- Reviewing and improving Eloquent queries
- Helping optimize migrations and indexes
- Debugging integration and validation issues
- Assisting with README/documentation formatting

The final implementation, feature integration, testing, debugging, and architectural decisions were reviewed and adjusted manually throughout the project.