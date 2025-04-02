<?php

include 'includes/db_connect.php';
include 'includes/header.php';

// Get the total number of artworks from the database
$artworks_count = $conn->query("SELECT COUNT(*) FROM artworks")->fetchColumn();

// Get the total number of warehouses from the database
$warehouses_count = $conn->query("SELECT COUNT(*) FROM warehouses")->fetchColumn();

// Get the 5 most recent artworks with their title, artist, and warehouse name
$recent_artworks = $conn->query("
    SELECT a.title, a.artist_name, w.name AS warehouse_name 
    FROM artworks a 
    LEFT JOIN warehouses w ON a.warehouse_id = w.id 
    ORDER BY a.id DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Get all warehouses and count how many artworks are in each one
$warehouses_stats = $conn->query("
    SELECT w.name, COUNT(a.id) AS artwork_count 
    FROM warehouses w 
    LEFT JOIN artworks a ON a.warehouse_id = w.id 
    GROUP BY w.id, w.name
")->fetchAll(PDO::FETCH_ASSOC);
?>
