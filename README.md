# Food-Ordering-System-
Musubi Food Ordering System
A full-featured online ordering platform for a Spam musubi and onigiri store. Customers can browse products, add items to cart, place orders via COD, GCash, or card, and track order status. Admins have a dedicated dashboard to manage menu items, view all orders, update order statuses, and manage customer accounts.

Features

User authentication (customer / admin roles)

Product catalog with image upload

Shopping cart and checkout

Order placement with multiple payment methods

Order status tracking (pending, completed, cancelled)

Admin dashboard:

Add / edit / delete products (soft delete to preserve order history)

View all orders with customer details

Update order statuses

Promote customers to admins or delete users

Responsive front-end (HTML/CSS/JS)

Secure password hashing (bcrypt)

Tech Stack
PHP (native), MySQL/MariaDB, HTML5, CSS3, JavaScript, phpMyAdmin (for DB management)

How to Run Locally

Clone this repository

Import food_db.sql into MySQL (phpMyAdmin or CLI)

Update database credentials in includes/db.php

Run on XAMPP/WAMP/MAMP (Apache + MySQL)

Access via http://localhost/your-folder/

Sample Admin Account
Register a new account and manually set role = 'admin' in the users table, or use the provided admin promotion feature.

You can shorten it if needed, but this gives a complete overview for anyone visiting your repo.

