<?php
include "includes/db.php";
include "includes/header.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$msg = "";
$msg_type = "error"; // success or error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($conn->real_escape_string($_POST['title']));
    $subject = trim($conn->real_escape_string($_POST['subject']));
    $category = trim($conn->real_escape_string($_POST['category'] ?? 'General'));
    $desc    = trim($conn->real_escape_string($_POST['description']));
    $user_id = $_SESSION['user']['id'];

    // Validation
    if (empty($title) || empty($subject)) {
        $msg = "❌ Title and subject are required.";
    } elseif (isset($_FILES['note']) && $_FILES['note']['error'] == 0) {
        $file = $_FILES['note'];
        $allowed_types = ['application/pdf'];
        $max_size = 10 * 1024 * 1024; // 10MB

        if (!in_array($file['type'], $allowed_types)) {
            $msg = "❌ Only PDF files are allowed.";
        } elseif ($file['size'] > $max_size) {
            $msg = "❌ File size must be less than 10MB.";
        } else {
            $fileName = time() . "_" . basename($file['name']);
            $target = "uploads/" . $fileName;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $sql = "INSERT INTO notes (user_id,title,subject,category,description,filename) 
                        VALUES ($user_id,'$title','$subject','$category','$desc','$fileName')";
                if ($conn->query($sql)) {
                    $msg = "✅ Note uploaded successfully (waiting for approval).";
                    $msg_type = "success";
                } else {
                    $msg = "❌ Database error: " . $conn->error;
                }
            } else {
                $msg = "❌ File upload failed.";
            }
        }
    } else {
        $msg = "❌ Please select a PDF file.";
    }
}
?>
<h2>Upload Notes</h2>
<?php if ($msg): ?>
    <p style="color: <?php echo $msg_type == 'success' ? 'green' : 'red'; ?>; font-weight: bold;"><?php echo $msg; ?></p>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <label for="title">Title *</label>
    <input type="text" id="title" name="title" placeholder="Enter note title" required value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">

    <label for="subject">Subject *</label>
    <input type="text" id="subject" name="subject" placeholder="Enter subject" required value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">

    <label for="category">Category</label>
    <select id="category" name="category">
        <option value="General" <?php echo (isset($_POST['category']) && $_POST['category'] == 'General') ? 'selected' : ''; ?>>General</option>
        <option value="Math" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Math') ? 'selected' : ''; ?>>Math</option>
        <option value="Science" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Science') ? 'selected' : ''; ?>>Science</option>
        <option value="History" <?php echo (isset($_POST['category']) && $_POST['category'] == 'History') ? 'selected' : ''; ?>>History</option>
        <option value="English" <?php echo (isset($_POST['category']) && $_POST['category'] == 'English') ? 'selected' : ''; ?>>English</option>
        <option value="Computer Science" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
        <option value="Other" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
    </select>

    <label for="description">Description</label>
    <textarea id="description" name="description" placeholder="Enter description"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>

    <label for="note">Select PDF File *</label>
    <input type="file" id="note" name="note" accept=".pdf" required>

    <button type="submit">Upload Note</button>
</form>
<script>
// Basic client-side validation (integrated with main.js toasts)
document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('note');
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (file.type !== 'application/pdf') {
            alert('Please select a PDF file.');
            e.preventDefault();
        } else if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB.');
            e.preventDefault();
        }
    }
});
</script>
<?php include "includes/footer.php"; ?>
