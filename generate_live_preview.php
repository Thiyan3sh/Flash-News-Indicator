<?php
date_default_timezone_set("Asia/Kolkata");

// DB connection
$conn = new mysqli("localhost", "root", "", "flashnews_db");
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);

// Delete expired news (event over)
$conn->query("DELETE FROM flash_news WHERE event_datetime < NOW()");

// Select approved news where publish_datetime <= NOW()
$stmt = $conn->prepare("SELECT * FROM flash_news 
    WHERE approval_status = 'Approved' 
    AND publish_datetime <= NOW()
    AND event_datetime >= NOW()
    ORDER BY event_datetime ASC");

$stmt->execute();
$result = $stmt->get_result();
$newsList = $result->fetch_all(MYSQLI_ASSOC);

// Prepare log content
$log = "";
foreach ($newsList as $news) {
    $log .= "Message: " . $news['message'] . "\n";
    $log .= "Event DateTime: " . $news['event_datetime'] . "\n";
    $log .= "User: " . $news['username'] . " (" . $news['userid'] . ")\n";
    $log .= "Department: " . $news['department'] . "\n";
    $log .= "Published At: " . $news['publish_datetime'] . "\n";
    $log .= "Status: " . $news['event_status'] . "\n";
    $log .= "-----------------------------\n";
}

// Write log only if there's news
file_put_contents("news_log.txt", $log);

echo "News log updated successfully.";
?>
