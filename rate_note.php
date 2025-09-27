    <?php
include "includes/db.php";

if (!isset($_SESSION['user'])) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$rating = floatval($_POST['rating'] ?? 0);

if ($id <= 0 || $rating < 1 || $rating > 5) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Check if note exists and is approved
$note_check = $conn->query("SELECT id, rating, rating_count FROM notes WHERE id=$id AND status='approved'");
if ($note_check->num_rows == 0) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Note not found']);
    exit;
}

$note = $note_check->fetch_assoc();
$current_rating = $note['rating'];
$current_count = $note['rating_count'];

// Calculate new average
$new_count = $current_count + 1;
$new_rating = (($current_rating * $current_count) + $rating) / $new_count;

// Update the note
$update_sql = "UPDATE notes SET rating=$new_rating, rating_count=$new_count WHERE id=$id";
if ($conn->query($update_sql)) {
    header("Content-Type: application/json");
    echo json_encode(['success' => true, 'new_rating' => round($new_rating, 1)]);
} else {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
