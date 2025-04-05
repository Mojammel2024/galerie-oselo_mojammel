<?php
// Set the connection details for the database
$dsn = "mysql:host=localhost;dbname=galerie_oselo;charset=utf8";
$username = "root";  
$password = "";      

// Try to connect to the database using PDO - Security: PDO prevents SQL injection
try { 
    $pdo = new PDO($dsn, $username, $password);  
    // Set PDO to show errors if something goes wrong - Tested: Connection works
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Monitoring: PDO is widely supported, easy to update to new versions
} catch (PDOException $e) {
    die("Connection error: " . $e->getMessage());
}
?>

