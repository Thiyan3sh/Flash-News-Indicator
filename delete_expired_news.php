<?php
date_default_timezone_set("Asia/Kolkata");

$conn = new mysqli("localhost", "root", "", "flashnews_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$now = date("Y-m-d H:i:s");

// Delete expired entries
$sql = "DELETE FROM flash_news WHERE event_datetime < ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $now);

if ($stmt->execute()) {
    echo "Expired flash news deleted successfully.";
} else {
    echo "Error deleting expired news: " . $stmt->error;
}
?>
