# Expense Tracker

A small Laravel web application for tracking daily expenses, organizing them by category, and viewing monthly reports.

## Features

- User registration, login, and logout
- Profile details and password reset pages
- Smart dashboard insights with projected spend, top category, and largest transaction
- Category management with active/inactive status
- Expense CRUD for authenticated users
- Expense filtering by description, category, and month
- CSV export for the current expense filters
- Monthly report with:
  - Total expenses per category for the selected month
  - Average daily expense for the selected month
  - Total expenses per category for the signed-in user
- Bar-chart style visualizations for category and daily spending
- SQLite persistence, so no separate database server is required

## Requirements

- PHP 8.3 or newer
- Composer

Node/npm is only needed if you want to rebuild frontend assets. The app UI is self-contained and runs without a Vite dev server.

## Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
touch database/database.mysql
php artisan migrate --seed
php artisan serve
```

Open the app:

```text
http://127.0.0.1:8000
```

Seeded login:

```text
Email: test@example.com
Password: password
```

You can also create a new account from the registration page.

## Running Tests

```bash
php artisan test
```

Current automated coverage checks:

- guests are redirected to login
- users can register
- invalid login credentials show validation errors
- authenticated users can create expenses
- users cannot edit another user's expense
- expenses can be filtered and exported
- reports show monthly category totals
- dashboard shows smart insights

## Data Model

- `users`: Laravel authentication users
- `categories`: predefined expense categories with a `status` flag
- `expenses`: user-owned expenses with amount, description, category, and timestamp

Expenses use `decimal(10, 2)` instead of floating point storage to avoid currency rounding issues.

## Optimization Notes

- Report totals are calculated with grouped database queries instead of looping in the view.
- Dashboard insights are calculated with aggregate queries, keeping display logic out of Blade templates.
- Expense listings eager-load categories to avoid repeated category queries.
- CSV export streams rows in chunks so large exports do not need to load every expense into memory.
- Indexes on `expenses.user_id`, `expenses.category_id`, and `expenses.spent_at` support the common listing and report filters.
- Form request classes keep validation rules outside the controller actions.

## Manual Test Plan

1. Register a new user.
2. Create a few categories or use the seeded categories.
3. Add expenses for different categories and dates.
4. Edit and delete one expense.
5. Toggle a category inactive and confirm it no longer appears in the expense category dropdown.
6. Open Reports, select a month, and verify category totals and average daily expense.
7. Check dashboard smart insights and chart bars after adding expenses.
8. Log out and confirm authenticated pages redirect to login.
