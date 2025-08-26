<?php
session_start();
date_default_timezone_set("Asia/Kolkata");

if (!isset($_SESSION['department']) || $_SESSION['department'] !== 'Administration') {
    die("Access Denied");
}

$conn = new mysqli("localhost", "root", "", "flashnews_db");
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);

$id = intval($_GET['id']);
$action = $_GET['action'];

if ($action === 'approve') {
    $conn->query("UPDATE flash_news SET approval_status='Approved' WHERE id=$id");
} elseif ($action === 'reject') {
    $conn->query("UPDATE flash_news SET approval_status='Rejected' WHERE id=$id");
}

header("Location: preview_flashnews.php");
exit();
?>
