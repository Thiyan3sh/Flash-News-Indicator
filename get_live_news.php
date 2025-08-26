<?php
date_default_timezone_set("Asia/Kolkata");

$conn = new mysqli("localhost", "root", "", "flashnews_db");
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);

$today = date("Y-m-d");
$now = date("Y-m-d H:i:s");

// Fetch approved and published news for today
$stmt = $conn->prepare("SELECT message, username, department, event_datetime FROM flash_news 
    WHERE DATE(publish_datetime) = ? 
    AND publish_datetime <= ? 
    AND approval_status = 'Approved' 
    AND event_status = 'Scheduled'
    ORDER BY publish_datetime ASC");

$stmt->bind_param("ss", $today, $now);
$stmt->execute();
$result = $stmt->get_result();

$newsArray = [];
while ($row = $result->fetch_assoc()) {
    $newsArray[] = $row;
}

header("Content-Type: application/json");
echo json_encode($newsArray);
?>
