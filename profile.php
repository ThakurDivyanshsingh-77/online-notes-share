<?php
include "includes/db.php";
include "includes/header.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['name'];

// Get stats
$total_notes = $conn->query("SELECT COUNT(*) as count FROM notes WHERE user_id=$user_id")->fetch_assoc()['count'];
$approved_notes = $conn->query("SELECT COUNT(*) as count FROM notes WHERE user_id=$user_id AND status='approved'")->fetch_assoc()['count'];
$pending_notes = $conn->query("SELECT COUNT(*) as count FROM notes WHERE user_id=$user_id AND status='pending'")->fetch_assoc()['count'];
$rejected_notes = $conn->query("SELECT COUNT(*) as count FROM notes WHERE user_id=$user_id AND status='rejected'")->fetch_assoc()['count'];

$total_downloads = $conn->query("SELECT SUM(downloads) as total FROM notes WHERE user_id=$user_id AND status='approved'")->fetch_assoc()['total'] ?? 0;
$avg_rating = $conn->query("SELECT AVG(rating) as avg FROM notes WHERE user_id=$user_id AND status='approved'")->fetch_assoc()['avg'] ?? 0;
$avg_rating = round($avg_rating, 1);
?>
<h2>My Profile</h2>
<div class="profile-stats">
    <div class="stat-card">
        <h3>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h3>
        <p>Here's an overview of your notes:</p>
    </div>
    <div class="stat-card">
        <h4>Total Notes Uploaded</h4>
        <p class="stat-number"><?php echo $total_notes; ?></p>
    </div>
    <div class="stat-card approved">
        <h4>Approved Notes</h4>
        <p class="stat-number"><?php echo $approved_notes; ?></p>
    </div>
    <div class="stat-card pending">
        <h4>Pending Approval</h4>
        <p class="stat-number"><?php echo $pending_notes; ?></p>
    </div>
    <div class="stat-card rejected">
        <h4>Rejected Notes</h4>
        <p class="stat-number"><?php echo $rejected_notes; ?></p>
    </div>
    <div class="stat-card downloads">
        <h4>Total Downloads</h4>
        <p class="stat-number"><?php echo $total_downloads; ?></p>
    </div>
    <div class="stat-card rating">
        <h4>Average Rating</h4>
        <p class="stat-number"><?php echo $avg_rating; ?>/5</p>
        <div class="star-rating" data-rating="<?php echo $avg_rating; ?>">
            <?php for($i = 1; $i <= 5; $i++): ?>
                <i class="fas fa-star <?php echo $i <= round($avg_rating) ? 'filled' : ''; ?>"></i>
            <?php endfor; ?>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>
