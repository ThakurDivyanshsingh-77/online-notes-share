<?php
include "../includes/db.php";
include "../includes/header.php";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: ../login.php");
    exit;
}

$res = $conn->query("SELECT notes.*, users.name FROM notes JOIN users ON notes.user_id = users.id WHERE notes.status='pending' ORDER BY notes.uploaded_at DESC");
?>

<h2>Admin Dashboard - Pending Notes</h2>
<?php while($row = $res->fetch_assoc()): ?>
  <div class="note-card">
    <h3><?php echo $row['title']; ?></h3>
    <p><b>Subject:</b> <?php echo $row['subject']; ?></p>
    <p><b>By:</b> <?php echo $row['name']; ?></p>
    <p><?php echo $row['description']; ?></p>
    <a href="../uploads/<?php echo $row['filename']; ?>" target="_blank">View</a> |
    <a href="approve_note.php?id=<?php echo $row['id']; ?>">✅ Approve</a> |
    <a href="reject_note.php?id=<?php echo $row['id']; ?>">❌ Reject</a>
  </div>
<?php endwhile; ?>
<?php include "../includes/footer.php"; ?>
