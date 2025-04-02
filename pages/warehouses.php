<?php
// Bring in the database connection and header files
include '../includes/db_connect.php';
include '../includes/header.php';

// Set up messages to show if something works or fails
$success_message = '';
$error_message = '';

// Check if a form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? ''; // Get the action from the form

    if ($action === 'save') {
        // Add or update a warehouse
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null; // Get ID if it exists
        $name = trim($_POST['name'] ?? ''); // Get name and remove extra spaces
        $address = trim($_POST['address'] ?? ''); // Get address and remove extra spaces


<!-- Main section of the page -->
<main>
    <h2>Manage Warehouses</h2>

    <!-- Show success message in green if there is one -->
    <?php if ($success_message): ?>
        <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>

    <!-- Show error message in red if there is one -->
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <!-- Button to open the add warehouse form -->
    <button onclick="openModal('warehouseModal', 'add')">Add Warehouse</button>

    <!-- Table to show all warehouses -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through each warehouse -->
            <?php foreach ($warehouses as $warehouse): ?>
            <tr>
                <td><?= htmlspecialchars($warehouse['name']) ?></td>
                <td><?= htmlspecialchars($warehouse['address']) ?></td>
                <td>
                    <!-- Button to edit warehouse -->
                    <button onclick="openModal('warehouseModal', 'edit', <?= $warehouse['id'] ?>, '<?= htmlspecialchars($warehouse['name']) ?>', '<?= htmlspecialchars($warehouse['address']) ?>')">Edit</button>
                    <!-- Link to delete warehouse -->
                    <a href="?action=delete&id=<?= $warehouse['id'] ?>" onclick="return confirm('Delete this warehouse?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Artworks by Warehouse</h2>
    <!-- Table to show artworks and their warehouses -->
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Artist</th>
                <th>Year</th>
                <th>Warehouse</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through each artwork -->
            <?php foreach ($artworks as $artwork): ?>
            <tr>
                <td><?= htmlspecialchars($artwork['title']) ?></td>
                <td><?= htmlspecialchars($artwork['artist_name']) ?></td>
                <td><?= htmlspecialchars($artwork['year']) ?></td>
                <td><?= htmlspecialchars($artwork['warehouse_name'] ?? 'Not assigned') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
