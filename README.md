# DevTicket

A lightweight issue/ticket tracker built on a LAMP stack (Linux, Apache, MySQL, PHP), 
developed locally on a Debian VM and deployed to AWS EC2.

## Features
- Create tickets with title, description, priority
- View all tickets ordered by creation date
- Status and priority indicators

## Stack
- PHP (mysqli)
- MariaDB
- Apache2
- Deployed on AWS EC2 (Debian)

## Setup
1. Copy `db.example.php` to `db.php` and fill in your DB credentials
2. Import the schema from `schema.sql`
3. Point Apache's document root to this folder
