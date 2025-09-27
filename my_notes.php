<?php
include "includes/db.php";
include "includes/header.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $note_id = (int)$_GET['delete'];
    // Check if note belongs to user
    $check = $conn->query("SELECT id FROM notes WHERE id=$note_id AND user_id=$user_id");
    if ($check->num_rows > 0) {
        $conn->query("DELETE FROM notes WHERE id=$note_id");
        header("Location: my_notes.php");
        exit;
    }
}

// Pagination settings
$notes_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $notes_per_page;

// Search and filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$category_filter = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

// Build query
$where_clauses = ["user_id=$user_id"];
if ($search) {
    $where_clauses[] = "(title LIKE '%$search%' OR subject LIKE '%$search%' OR description LIKE '%$search%' OR category LIKE '%$search%')";
}
if ($status_filter) {
    $where_clauses[] = "status = '$status_filter'";
}
if ($category_filter) {
    $where_clauses[] = "category = '$category_filter'";
}
$where = implode(' AND ', $where_clauses);

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM notes WHERE $where";
$count_res = $conn->query($count_query);
$total_notes = $count_res->fetch_assoc()['total'];
$total_pages = ceil($total_notes / $notes_per_page);

// Get notes
$query = "SELECT * FROM notes WHERE $where ORDER BY uploaded_at DESC LIMIT $notes_per_page OFFSET $offset";
$res = $conn->query($query);

// Get distinct categories for filter
$categories_query = "SELECT DISTINCT category FROM notes WHERE user_id=$user_id AND category IS NOT NULL AND category != '' ORDER BY category";
$categories_res = $conn->query($categories_query);
$categories = [];
while ($cat = $categories_res->fetch_assoc()) {
    $categories[] = $cat['category'];
}
?>
<h2>My Notes</h2>

<!-- Search and Filter Form -->
<div class="search-filter">
    <form method="GET" action="my_notes.php">
        <input type="text" name="search" placeholder="Search my notes..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
        </select>
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category_filter == $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
    </form>
</div>

<?php if ($res->num_rows > 0): ?>
    <?php while ($row = $res->fetch_assoc()): ?>
      <div class="note-card">
        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
        <p><b>Subject:</b> <?php echo htmlspecialchars($row['subject']); ?></p>
        <p><b>Category:</b> <?php echo htmlspecialchars($row['category'] ?? 'General'); ?></p>
        <p><b>Status:</b> <span class="status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></p>
        <p><b>Downloads:</b> <?php echo $row['downloads']; ?></p>
        <p><b>Rating:</b> <?php echo number_format($row['rating'], 1); ?>/5</p>
        <a href="uploads/<?php echo htmlspecialchars($row['filename']); ?>" target="_blank"><i class="fas fa-eye"></i> View</a>
        <div class="note-actions">
            <a href="edit_note.php?id=<?php echo $row['id']; ?>" class="edit"><i class="fas fa-edit"></i> Edit</a>
            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirmDelete(<?php echo $row['id']; ?>)" class="delete"><i class="fas fa-trash"></i> Delete</a>
        </div>
      </div>
    <?php endwhile; ?>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&category=<?php echo urlencode($category_filter); ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&category=<?php echo urlencode($category_filter); ?>" <?php echo $i == $page ? 'class="current"' : ''; ?>><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&category=<?php echo urlencode($category_filter); ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <p>No notes found matching your criteria.</p>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
