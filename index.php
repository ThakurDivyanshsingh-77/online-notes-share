<?php
include "includes/db.php";
include "includes/header.php";

// Pagination settings
$notes_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $notes_per_page;

// Search and filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$subject_filter = isset($_GET['subject']) ? $conn->real_escape_string($_GET['subject']) : '';
$category_filter = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$author_filter = isset($_GET['author']) ? $conn->real_escape_string($_GET['author']) : '';

// Build query
$where_clauses = ["notes.status='approved'"];
if ($search) {
    $where_clauses[] = "(notes.title LIKE '%$search%' OR notes.subject LIKE '%$search%' OR notes.description LIKE '%$search%' OR notes.category LIKE '%$search%')";
}
if ($subject_filter) {
    $where_clauses[] = "notes.subject = '$subject_filter'";
}
if ($category_filter) {
    $where_clauses[] = "notes.category = '$category_filter'";
}
if ($author_filter) {
    $where_clauses[] = "users.name = '$author_filter'";
}
$where = implode(' AND ', $where_clauses);

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM notes JOIN users ON notes.user_id = users.id WHERE $where";
$count_res = $conn->query($count_query);
$total_notes = $count_res->fetch_assoc()['total'];
$total_pages = ceil($total_notes / $notes_per_page);

// Get notes
$query = "SELECT notes.*, users.name FROM notes 
          JOIN users ON notes.user_id = users.id
          WHERE $where
          ORDER BY notes.uploaded_at DESC
          LIMIT $notes_per_page OFFSET $offset";
$res = $conn->query($query);

// Get distinct subjects for filter
$subjects_query = "SELECT DISTINCT subject FROM notes WHERE status='approved' ORDER BY subject";
$subjects_res = $conn->query($subjects_query);
$subjects = [];
while ($subj = $subjects_res->fetch_assoc()) {
    $subjects[] = $subj['subject'];
}

$categories_query = "SELECT DISTINCT category FROM notes WHERE status='approved' AND category IS NOT NULL AND category != '' ORDER BY category";
$categories_res = $conn->query($categories_query);
$categories = [];
while ($cat = $categories_res->fetch_assoc()) {
    $categories[] = $cat['category'];
}

// Get distinct authors for filter
$authors_query = "SELECT DISTINCT users.name FROM notes JOIN users ON notes.user_id = users.id WHERE notes.status='approved' ORDER BY users.name";
$authors_res = $conn->query($authors_query);
$authors = [];
while ($auth = $authors_res->fetch_assoc()) {
    $authors[] = $auth['name'];
}
?>
<h2>Available Notes</h2>

<!-- Search and Filter Form -->
<div class="search-filter">
    <form method="GET" action="index.php">
        <input type="text" name="search" placeholder="Search notes..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="subject">
            <option value="">All Subjects</option>
            <?php foreach ($subjects as $subj): ?>
                <option value="<?php echo htmlspecialchars($subj); ?>" <?php echo $subject_filter == $subj ? 'selected' : ''; ?>><?php echo htmlspecialchars($subj); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category_filter == $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="author">
            <option value="">All Authors</option>
            <?php foreach ($authors as $auth): ?>
                <option value="<?php echo htmlspecialchars($auth); ?>" <?php echo $author_filter == $auth ? 'selected' : ''; ?>><?php echo htmlspecialchars($auth); ?></option>
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
        <p><b>By:</b> <?php echo htmlspecialchars($row['name']); ?></p>
        <p><?php echo htmlspecialchars($row['description']); ?></p>
        <p><b>Downloads:</b> <?php echo $row['downloads']; ?></p>
        <div class="star-rating" data-note-id="<?php echo $row['id']; ?>" data-rating="<?php echo $row['rating']; ?>">
            <?php for($i = 1; $i <= 5; $i++): ?>
                <i class="fas fa-star <?php echo $i <= round($row['rating']) ? 'filled' : ''; ?>"></i>
            <?php endfor; ?>
        </div>
        <a href="download.php?id=<?php echo $row['id']; ?>"><i class="fas fa-download"></i> Download (<?php echo $row['downloads']; ?>)</a>
      </div>
    <?php endwhile; ?>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&subject=<?php echo urlencode($subject_filter); ?>&category=<?php echo urlencode($category_filter); ?>&author=<?php echo urlencode($author_filter); ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&subject=<?php echo urlencode($subject_filter); ?>&category=<?php echo urlencode($category_filter); ?>&author=<?php echo urlencode($author_filter); ?>" <?php echo $i == $page ? 'class="current"' : ''; ?>><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&subject=<?php echo urlencode($subject_filter); ?>&category=<?php echo urlencode($category_filter); ?>&author=<?php echo urlencode($author_filter); ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <p>No notes found matching your criteria.</p>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
