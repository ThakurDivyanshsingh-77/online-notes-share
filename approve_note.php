<?php
include "../includes/db.php";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: ../login.php");
    exit;
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $conn->query("UPDATE notes SET status='approved' WHERE id=$id");
}

header("Location: dashboard.php");
exit;
