<?php
// Set the connection details for the database
$dsn = "mysql:host=localhost;dbname=galerie_oselo;charset=utf8";
$username = "root";  
$password = "";      

// Try to connect to the database using PDO
try { 
    $conn = new PDO($dsn, $username, $password);  
    // Set PDO to show errors if something goes wrong
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection error: " . $e->getMessage());
}
?>

