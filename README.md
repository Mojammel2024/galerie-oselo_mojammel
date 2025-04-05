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






## Tests fonctionnels
- Ajouter une œuvre avec le titre "Mona Lisa", artiste "Da Vinci", année 1503 → Apparaît dans le tableau.
- Modifier le nom de l’entrepôt de "Stock A" à "Stock B" → Le nom change dans la liste.
- Supprimer l’œuvre ID 1 → Disparaît du tableau.
- Assigner une œuvre à un entrepôt → Le nom de l’entrepôt apparaît dans le tableau des œuvres.

## Tests de sécurité
- Testé XSS : Entré `<script>` dans le titre → S’affiche comme texte, pas de popup.
- Testé injection SQL : Entré `' OR 1=1` → Pas de fuite de données, erreur affichée.



## Plan de veille
- Vérifier les sites de PHP et MySQL tous les mois pour les mises à jour ou correctifs de sécurité.
- Regarder les tendances d’interface sur des sites comme W3Schools pour améliorer l’application.
- Corriger les problèmes de sécurité trouvés dans les nouvelles ou forums.