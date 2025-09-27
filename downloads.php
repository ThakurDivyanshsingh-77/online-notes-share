<?php
include "includes/db.php";

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM notes WHERE id=$id AND status='approved'");
if ($res->num_rows == 1) {
    $note = $res->fetch_assoc();
    $file = "uploads/" . $note['filename'];

    if (file_exists($file)) {
        $conn->query("UPDATE notes SET downloads=downloads+1 WHERE id=$id");

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);
        exit;
    } else {
        echo "❌ File not found.";
    }
} else {
    echo "❌ Note not found or not approved.";
}
