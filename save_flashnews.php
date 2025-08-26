<?php
session_start();
date_default_timezone_set("Asia/Kolkata");

// Security check
if (!isset($_POST['userid'], $_POST['username'], $_POST['department'])) {
    die("Invalid Access");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "flashnews_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Sanitize inputs
$userid = $_POST['userid'];
$username = $_POST['username'];
$department = $_POST['department'];
$message = trim($_POST['message']);
$event_date_raw = $_POST['event_date'];
$event_time = $_POST['event_time'];
$publish_date_raw = $_POST['publish_date'];
$publish_time = $_POST['publish_time'];
$sent_timestamp = date("Y-m-d H:i:s");

// Reformat dates to Y-m-d if in d-m-Y
function convertToYMD($dateStr) {
    if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateStr)) {
        $parts = explode("-", $dateStr);
        return $parts[2] . "-" . $parts[1] . "-" . $parts[0];
    }
    return $dateStr; // Assume already correct
}

$event_date = convertToYMD($event_date_raw);
$publish_date = convertToYMD($publish_date_raw);

$event_datetime = "$event_date $event_time:00";
$publish_datetime = "$publish_date $publish_time:00";

// Validate datetime
$now = new DateTime();
$publish_dt = DateTime::createFromFormat('Y-m-d H:i:s', $publish_datetime);
$event_dt = DateTime::createFromFormat('Y-m-d H:i:s', $event_datetime);

if (!$publish_dt || !$event_dt) {
    die("Invalid date/time format.");
}

if ($publish_dt < $now) {
    die("Publish date/time must be today or future.");
}

if ($event_dt <= $publish_dt) {
    die("Event date/time must be after publish date/time.");
}

if ($publish_dt->format('w') == 0 || $event_dt->format('w') == 0) {
    die("Publish or Event date cannot be on a Sunday.");
}

// Status defaults
$event_status = 'Scheduled';
$approval_status = ($department === 'Administration') ? 'Approved' : 'Pending';

// Insert into DB
$stmt = $conn->prepare("INSERT INTO flash_news 
(userid, username, department, message, event_date, event_time, sent_timestamp, publish_datetime, event_datetime, event_status, approval_status) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sssssssssss",
    $userid,
    $username,
    $department,
    $message,
    $event_date,
    $event_time,
    $sent_timestamp,
    $publish_datetime,
    $event_datetime,
    $event_status,
    $approval_status
);

if ($stmt->execute()) {
    // Append to log if publish time is now or in future
    $logMessage = "Flash News Log Entry\n";
    $logMessage .= "---------------------------\n";
    $logMessage .= "User ID     : $userid\n";
    $logMessage .= "Name        : $username\n";
    $logMessage .= "Department  : $department\n";
    $logMessage .= "Message     : $message\n";
    $logMessage .= "Event       : $event_date at $event_time\n";
    $logMessage .= "Publish     : $publish_date at $publish_time\n";
    $logMessage .= "Submitted   : $sent_timestamp\n";
    $logMessage .= "Status      : $approval_status\n";
    $logMessage .= "---------------------------\n\n";

    // Ensure logs directory exists
    if (!is_dir('logs')) {
        mkdir('logs', 0777, true);
    }

    file_put_contents("logs/news_log.txt", $logMessage, FILE_APPEND);

    header("Location: dashboard.php?success=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
