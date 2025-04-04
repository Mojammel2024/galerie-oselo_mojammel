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
        // Add or update an artwork
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null; // Get ID if it exists
        $title = trim($_POST['title'] ?? ''); // Get title and remove extra spaces
        $artist_name = trim($_POST['artist_name'] ?? ''); // Get artist name
        $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT) ?: null; // Get year as a number
        $width = filter_input(INPUT_POST, 'width', FILTER_VALIDATE_FLOAT) ?: null; // Get width as a number
        $height = filter_input(INPUT_POST, 'height', FILTER_VALIDATE_FLOAT) ?: null; // Get height as a number
 
        if (empty($title)) {
            $error_message = "Title is required"; // Error if no title
        } else {
            try {
                if ($id) {
                    // Update an existing artwork
                    $sql = "UPDATE artworks SET title = ?, artist_name = ?, year = ?, width = ?, height = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$title, $artist_name, $year, $width, $height, $id]);
                    $success_message = "Artwork updated successfully";
                } else {
                    // Add a new artwork
                    $sql = "INSERT INTO artworks (title, artist_name, year, width, height) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$title, $artist_name, $year, $width, $height]);
                    $success_message = "Artwork added successfully";
                }
            } catch (PDOException $e) {
                $error_message = "Failed to save artwork: " . $e->getMessage(); // Show error if something goes wrong
            }
        }
    } elseif ($action === 'assign') {
        // Assign an artwork to a warehouse
        $artwork_id = filter_input(INPUT_POST, 'artwork_id', FILTER_VALIDATE_INT); // Get artwork ID
        $warehouse_id = filter_input(INPUT_POST, 'warehouse_id', FILTER_VALIDATE_INT) ?: null; // Get warehouse ID

        if ($artwork_id === false || $artwork_id <= 0) {
            $error_message = "Invalid artwork ID"; // Error if ID is wrong
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE artworks SET warehouse_id = ? WHERE id = ?");
                $stmt->execute([$warehouse_id, $artwork_id]);
                $success_message = "Artwork assigned successfully";
            } catch (PDOException $e) {
                $error_message = "Failed to assign artwork: " . $e->getMessage(); // Show error if it fails
            }
        }
    }
}

// Handle deleting an artwork (from URL)
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $artwork_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Get ID from URL
    if ($artwork_id === false || $artwork_id <= 0) {
        $error_message = "Invalid artwork ID"; // Error if ID is wrong
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM artworks WHERE id = ?");
            $stmt->execute([$artwork_id]);
            $success_message = "Artwork deleted successfully";
        } catch (PDOException $e) {
            $error_message = "Failed to delete artwork: " . $e->getMessage(); // Show error if it fails
        }
    }
}

// Load artwork details for editing
$edit_artwork = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit') {
    $artwork_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Get ID from URL
    if ($artwork_id !== false && $artwork_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM artworks WHERE id = ?");
        $stmt->execute([$artwork_id]);
        $edit_artwork = $stmt->fetch(PDO::FETCH_ASSOC); // Get artwork details
        if (!$edit_artwork) {
            $error_message = "Artwork not found"; // Error if artwork doesn’t exist
        }
    }
}

// Get all artworks with their warehouse names
$stmt = $pdo->query("
    SELECT a.*, w.name AS warehouse_name 
    FROM artworks a 
    LEFT JOIN warehouses w ON a.warehouse_id = w.id
");
$artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all warehouses for the assign dropdown
$warehouses = $pdo->query("SELECT * FROM warehouses")->fetchAll();
?>

<!-- Main section of the page -->
<main>
    <h2>Manage Artworks</h2>

    <!-- Show success message in green if there is one -->
    <?php if ($success_message): ?>
        <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>

    <!-- Show error message in red if there is one -->
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <!-- Button to open the add artwork form -->
    <button onclick="openModal()">Add Artwork</button>

    <!-- Table to show all artworks -->
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Artist</th>
                <th>Year</th>
                <th>Dimensions</th>
                <th>Warehouse</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through each artwork -->
            <?php foreach ($artworks as $artwork): ?>
            <tr>
                <td><?= htmlspecialchars($artwork['title']) ?></td>
                <td><?= htmlspecialchars($artwork['artist_name']) ?></td>
                <td><?= htmlspecialchars($artwork['year']) ?></td>
                <td><?= htmlspecialchars($artwork['width']) ?> x <?= htmlspecialchars($artwork['height']) ?> cm</td>
                <td><?= htmlspecialchars($artwork['warehouse_name'] ?? 'Not assigned') ?></td>
                <td>
                    <a href="?action=edit&id=<?= $artwork['id'] ?>">Edit</a> |
                    <a href="?action=delete&id=<?= $artwork['id'] ?>" onclick="return confirm('Delete this artwork?')">Delete</a> |
                    <button onclick="openAssignModal(<?= $artwork['id'] ?>)">Assign Warehouse</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<!-- Popup form for adding or editing an artwork -->
<div id="artworkModal" class="modal" style="display: <?= $edit_artwork ? 'block' : 'none'; ?>;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <h2 id="modalTitle"><?= $edit_artwork ? 'Edit Artwork' : 'Add Artwork' ?></h2>
        <form action="" method="POST">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" id="artworkId" value="<?= $edit_artwork['id'] ?? '' ?>">
            <label>Title: <input type="text" name="title" id="title" value="<?= htmlspecialchars($edit_artwork['title'] ?? '') ?>" required></label>
            <label>Artist: <input type="text" name="artist_name" id="artist" value="<?= htmlspecialchars($edit_artwork['artist_name'] ?? '') ?>"></label>
            <label>Year: <input type="number" name="year" id="year" value="<?= $edit_artwork['year'] ?? '' ?>"></label>
            <label>Width (cm): <input type="number" name="width" id="width" value="<?= $edit_artwork['width'] ?? '' ?>"></label>
            <label>Height (cm): <input type="number" name="height" id="height" value="<?= $edit_artwork['height'] ?? '' ?>"></label>
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<!-- Popup form for assigning a warehouse -->
<div id="assignModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAssignModal()">×</span>
        <h2>Assign Warehouse</h2>
        <form action="" method="POST">
            <input type="hidden" name="action" value="assign">
            <input type="hidden" name="artwork_id" id="modal_artwork_id">
            <label for="warehouse">Select Warehouse:</label>
            <select name="warehouse_id">
                <option value="">Not assigned</option>
                <!-- Loop through warehouses to fill the dropdown -->
                <?php foreach ($warehouses as $warehouse): ?>
                    <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Assign</button>
        </form>
    </div>
</div>

<!-- JavaScript to control the popups -->
<script>
function openModal(id = null) {
    if (id) {
        window.location.href = '?action=edit&id=' + id; // Go to edit mode with ID
    } else {
        document.getElementById('modalTitle').innerText = 'Add Artwork';
        document.getElementById('artworkId').value = '';
        document.getElementById('title').value = '';
        document.getElementById('artist').value = '';
        document.getElementById('year').value = '';
        document.getElementById('width').value = '';
        document.getElementById('height').value = '';
        document.getElementById('artworkModal').style.display = 'block'; // Show the add form
    }
}

function closeModal() {
    document.getElementById('artworkModal').style.display = 'none'; // Hide the form
    window.location.href = 'artworks.php'; // Go back to the list
}

function openAssignModal(artworkId) {
    document.getElementById('modal_artwork_id').value = artworkId; // Set the artwork ID
    document.getElementById('assignModal').style.display = 'block'; // Show the assign form
}

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none'; // Hide the assign form
}
</script>