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

        if (empty($name)) {
            $error_message = "Name is required"; // Error if no name
        } else {
            try {
                if ($id) {
                    // Update an existing warehouse
                    $stmt = $pdo->prepare("UPDATE warehouses SET name = ?, address = ? WHERE id = ?");
                    $stmt->execute([$name, $address, $id]);
                    $success_message = "Warehouse updated successfully";
                } else {
                    // Check if the warehouse name already exists
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM warehouses WHERE name = ?");
                    $stmt->execute([$name]);
                    if ($stmt->fetchColumn() > 0) {
                        $error_message = "Warehouse name already exists"; // Error if name is taken
                    } else {
                        // Add a new warehouse
                        $stmt = $pdo->prepare("INSERT INTO warehouses (name, address) VALUES (?, ?)");
                        $stmt->execute([$name, $address]);
                        $success_message = "Warehouse added successfully";
                    }
                }
            } catch (PDOException $e) {
                $error_message = "Failed to save warehouse: " . $e->getMessage(); // Show error if something goes wrong
            }
        }
    }
}

// Handle deleting a warehouse (from URL)
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $warehouse_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Get ID from URL
    if ($warehouse_id === false || $warehouse_id <= 0) {
        $error_message = "Invalid warehouse ID"; // Error if ID is wrong
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM warehouses WHERE id = ?");
            $stmt->execute([$warehouse_id]);
            $success_message = "Warehouse deleted successfully";
        } catch (PDOException $e) {
            $error_message = "Failed to delete warehouse: " . $e->getMessage(); // Show error if it fails
        }
    }
}

// Load warehouse details for editing
$edit_warehouse = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit') {
    $warehouse_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Get ID from URL
    if ($warehouse_id !== false && $warehouse_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM warehouses WHERE id = ?");
        $stmt->execute([$warehouse_id]);
        $edit_warehouse = $stmt->fetch(PDO::FETCH_ASSOC); // Get warehouse details
        if (!$edit_warehouse) {
            $error_message = "Warehouse not found"; // Error if warehouse doesn’t exist
        }
    }
}

// Get all warehouses
$warehouses = $pdo->query("SELECT * FROM warehouses")->fetchAll();

// Get all artworks with their warehouse names
$artworks = $pdo->query("
    SELECT artworks.*, warehouses.name AS warehouse_name 
    FROM artworks 
    LEFT JOIN warehouses ON artworks.warehouse_id = warehouses.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

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

<!-- Popup form for adding or editing a warehouse -->
<div id="warehouseModal" class="modal" style="display: <?= $edit_warehouse ? 'block' : 'none'; ?>;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('warehouseModal')">×</span>
        <h2 id="warehouseModalTitle"><?= $edit_warehouse ? 'Edit Warehouse' : 'Add Warehouse' ?></h2>
        <form action="" method="POST">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" id="warehouseId" value="<?= $edit_warehouse['id'] ?? '' ?>">
            <label for="name">Name:</label>
            <input type="text" name="name" id="warehouseName" value="<?= htmlspecialchars($edit_warehouse['name'] ?? '') ?>" required>
            <label for="address">Address:</label>
            <input type="text" name="address" id="warehouseAddress" value="<?= htmlspecialchars($edit_warehouse['address'] ?? '') ?>">
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<!-- JavaScript to control the popup -->
<script>
function openModal(modalId, action, id = null, name = '', address = '') {
    if (action === 'edit' && id) {
        window.location.href = '?action=edit&id=' + id; // Go to edit mode with ID
    } else {
        const modal = document.getElementById(modalId);
        const title = document.getElementById('warehouseModalTitle');
        const warehouseId = document.getElementById('warehouseId');
        const warehouseName = document.getElementById('warehouseName');
        const warehouseAddress = document.getElementById('warehouseAddress');

        title.innerText = 'Add Warehouse';
        warehouseId.value = '';
        warehouseName.value = '';
        warehouseAddress.value = '';
        modal.style.display = 'block'; // Show the form
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none'; // Hide the form
    window.location.href = 'warehouses.php'; // Go back to the list
}
</script>