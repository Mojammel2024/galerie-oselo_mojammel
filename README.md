# Oselo Gallery Admin Panel

This project is a web application to manage artworks and warehouses for the Oselo Gallery. It allows users to create, edit, delete, and assign artworks to warehouses.

## Requirements
To run this project, you need:
- A web server like XAMPP with PHP installed.
- A MySQL database.
- A browser (like Chrome or Firefox).

## How to Install
Follow these steps to set up the project on your computer:

1. **Clone the Repository**
   - Open your terminal or command prompt.
   - Run this command: git clone https://github.com/Mojammel2024/galerie-oselo_mojammel.git

   - This will download the project to your computer.

2. **Set Up the Database**
- Open your database tool (like phpMyAdmin or MySQL Workbench).
- Create a new database called `galerie_oselo` (or any name you like).
- Import the SQL file:
-  there’s a file like `database.sql` in the project, use it to set up the tables.

3. **Configure the Database Connection**
- Go to the `includes/` folder and find `db_connect.php`.
- Open it and update these lines with your database details:
```php
$host = 'localhost'; // Your database host
$dbname = 'galerie_oselo'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password