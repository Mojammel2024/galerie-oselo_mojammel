<?php

include 'includes/db_connect.php';
include 'includes/header.php';

// Get the total number of artworks from the database
$artworks_count = $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn();

// Get the total number of warehouses from the database
$warehouses_count = $pdo->query("SELECT COUNT(*) FROM warehouses")->fetchColumn();

// Get the 5 most recent artworks with their title, artist, and warehouse name
$recent_artworks = $pdo->query("
    SELECT a.title, a.artist_name, w.name AS warehouse_name 
    FROM artworks a 
    LEFT JOIN warehouses w ON a.warehouse_id = w.id 
    ORDER BY a.id DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Get all warehouses and count how many artworks are in each one
$warehouses_stats = $pdo->query("
    SELECT w.name, COUNT(a.id) AS artwork_count 
    FROM warehouses w 
    LEFT JOIN artworks a ON a.warehouse_id = w.id 
    GROUP BY w.id, w.name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <!-- Header section of the dashboard -->
    <section class="dashboard-header">
        <h2>Dashboard</h2>
        <p>Welcome to the Oselo Gallery Admin Panel</p>
    </section>

    <!-- Section to show statistics (number of artworks and warehouses) -->
    <section class="dashboard-stats">
        <!-- Card for artworks -->
        <div class="stat-card">
            <h3>Artworks</h3>
            <!-- Show the total number of artworks -->
            <p><?= $artworks_count ?> registered</p>
            <a href="pages/artworks.php" class="btn">Manage Artworks</a>
        </div>
        <!-- Card for warehouses -->
        <div class="stat-card">
            <h3>Warehouses</h3>
            <p><?= $warehouses_count ?> registered</p>
            <a href="pages/warehouses.php" class="btn">Manage Warehouses</a>
        </div>
    </section>

    <!-- Section to show the 5 most recent artworks -->
    <section class="dashboard-recent">
        <h3>Recent Artworks</h3>
        <!-- Check if there are no recent artworks -->
        <?php if (empty($recent_artworks)): ?>

            <p>No artworks added yet.</p>
        <?php else: ?>
            <!-- Table to show recent artworks -->
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Artist</th>
                        <th>Warehouse</th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody>
                    <!-- Loop through each recent artwork -->
                    <?php foreach ($recent_artworks as $artwork): ?>
                        <tr>
                            <!-- Show the artwork title -->
                            <td><?= htmlspecialchars($artwork['title']) ?></td>
                            <td><?= htmlspecialchars($artwork['artist_name'] ?? 'Unknown') ?></td>
                            <td><?= htmlspecialchars($artwork['warehouse_name'] ?? 'Not assigned') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <!-- Section to show statistics for each warehouse -->
    <section class="dashboard-warehouse-stats">
        <h3>Warehouse Statistics</h3>
        <!-- Check if there are no warehouses -->
        <?php if (empty($warehouses_stats)): ?>
            <p>No warehouses added yet.</p>
        <?php else: ?>
            <!-- Grid to show warehouse stats -->
            <div class="warehouse-stats-grid">
                <?php foreach ($warehouses_stats as $stat): ?>
                    <div class="warehouse-stat-card">
                        <h4><?= htmlspecialchars($stat['name']) ?></h4>
                        <!-- Show the number of artworks in this warehouse -->
                        <p><?= $stat['artwork_count'] ?> artwork<?= $stat['artwork_count'] !== 1 ? 's' : '' ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

