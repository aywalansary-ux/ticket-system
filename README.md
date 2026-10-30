# Support Ticket System

A web-based IT support ticket system built with PHP and MySQL.

## Features
- User registration and login (passwords hashed with `password_hash`)
- Users create tickets with a title, description and priority
- Support agents see all tickets and update their status
- Comments between users and agents on each ticket
- Role-based access: users only see their own tickets

## Security
- Prepared statements (PDO) against SQL injection
- Output escaping with `htmlspecialchars` against XSS
- Server-side ownership and role checks

## Tech
PHP, MySQL, HTML, CSS

## Run locally
1. Install XAMPP and start Apache and MySQL.
2. Copy this folder to `htdocs`.
3. Import `schema.sql` in phpMyAdmin.
4. Open `localhost/ticket-system/register.php` and create an account.
5. To test the agent role, set that account's `role` to `agent` in the `users` table, then log in again.