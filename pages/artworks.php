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
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$title, $artist_name, $year, $width, $height, $id]);
                    $success_message = "Artwork updated successfully";
                } else {
                    // Add a new artwork
                    $sql = "INSERT INTO artworks (title, artist_name, year, width, height) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
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
                $stmt = $conn->prepare("UPDATE artworks SET warehouse_id = ? WHERE id = ?");
                $stmt->execute([$warehouse_id, $artwork_id]);
                $success_message = "Artwork assigned successfully";
            } catch (PDOException $e) {
                $error_message = "Failed to assign artwork: " . $e->getMessage(); // Show error if it fails
            }
        }
    }
}


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
