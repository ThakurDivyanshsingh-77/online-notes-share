<?php
include "includes/db.php";
include "includes/header.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$note_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if note exists and belongs to user
$note = $conn->query("SELECT * FROM notes WHERE id=$note_id AND user_id=$user_id")->fetch_assoc();
if (!$note) {
    header("Location: my_notes.php");
    exit;
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = $conn->real_escape_string($_POST['title']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $category = $conn->real_escape_string($_POST['category'] ?? $note['category'] ?? 'General');
    $desc    = $conn->real_escape_string($_POST['description']);

    $sql = "UPDATE notes SET title='$title', subject='$subject', category='$category', description='$desc' WHERE id=$note_id";
    if ($conn->query($sql)) {
        $msg = "✅ Note updated successfully.";
        // Refresh note data
        $note = $conn->query("SELECT * FROM notes WHERE id=$note_id")->fetch_assoc();
    } else {
        $msg = "❌ Update failed: " . $conn->error;
    }
}
?>
<h2>Edit Note</h2>
<p style="color:green;"><?php echo $msg; ?></p>
<form method="post" enctype="multipart/form-data">
    <label for="title">Title *</label>
    <input type="text" id="title" name="title" placeholder="Title" value="<?php echo htmlspecialchars($note['title']); ?>" required><br>

    <label for="subject">Subject *</label>
    <input type="text" id="subject" name="subject" placeholder="Subject" value="<?php echo htmlspecialchars($note['subject']); ?>" required><br>

    <label for="category">Category</label>
    <select id="category" name="category">
        <option value="General" <?php echo ($note['category'] == 'General') ? 'selected' : ''; ?>>General</option>
        <option value="Math" <?php echo ($note['category'] == 'Math') ? 'selected' : ''; ?>>Math</option>
        <option value="Science" <?php echo ($note['category'] == 'Science') ? 'selected' : ''; ?>>Science</option>
        <option value="History" <?php echo ($note['category'] == 'History') ? 'selected' : ''; ?>>History</option>
        <option value="English" <?php echo ($note['category'] == 'English') ? 'selected' : ''; ?>>English</option>
        <option value="Computer Science" <?php echo ($note['category'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
        <option value="Other" <?php echo ($note['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
    </select><br>

    <label for="description">Description</label>
    <textarea id="description" name="description" placeholder="Description"><?php echo htmlspecialchars($note['description']); ?></textarea><br>

    <button type="submit">Update Note</button>
    <a href="my_notes.php" style="margin-left: 10px; padding: 0.5rem; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px;">Cancel</a>
</form>
<?php include "includes/footer.php"; ?>
