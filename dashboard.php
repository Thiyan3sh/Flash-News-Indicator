<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
include('generate_live_preview.php');

if (!isset($_SESSION['userid'], $_SESSION['username'], $_SESSION['department'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$department = $_SESSION['department'];
$userid = $_SESSION['userid'];

$conn = new mysqli("localhost", "root", "", "flashnews_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$conn->query("DELETE FROM flash_news WHERE event_datetime < NOW()");


$latestNews = $conn->query("SELECT * FROM flash_news ORDER BY id DESC LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Flash News</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding-top: 290px;
        }

        .header {
            background: linear-gradient(90deg, #1e90ff, #28a745);
            color: white;
            padding: 20px;
            position: fixed;
            width: 99%;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 9999;
            height: 120px;
        }

        .header .title-block {
            text-align: center;
            flex: 1;
        }

        .header img {
            height: 90px;
            width: auto;
            margin: 0 20px;
        }

        .header h1 {
            font-size: 30px;
            margin: 4px 0;
        }
        .header h2 {
            font-size: 22px;
            margin: 2px 0;
        }
        .header h3 {
            font-size: 20px;
            margin: 2px 0;
        }

        .sub-header {
            background-color: #2f4f4f;
            color: white;
            padding: 15px 30px;
            position: fixed;
            width: 100%;
            top: 150px;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 999;
        }

        .nav-header {
            background-color: #e0e0e0;
            padding: 15px 20px;
            position: fixed;
            width: 100%;
            top: 220px;
            text-align: center;
            z-index: 998;
        }

        .nav-header a {
            margin: 0 20px;
            font-weight: bold;
            color: #007BFF;
            text-decoration: none;
            transition: color 0.3s;
        }

        .nav-header a:hover {
            color: #0056b3;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-right :30px;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #bd2130;
        }

        .form-box, .preview-box {
            background: #fff;
            padding: 40px;
            width: 95%;
            max-width: 1200px;
            margin: 30px auto;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            text-align: left;
        }

        input, textarea {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            margin: 12px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .date-time-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
        .preview-box{
            background-color:rgb(252, 248, 190);
        }

        .preview-box p {
            margin: 10px 0;
        }

        .preview-box h3 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- First Header -->
<div class="header">
    <img src="images/logo-left.png" alt="Left Logo">
    <div class="title-block">
        <h1>Heavy Vehicles Factory</h1>
        <h2>भारी वाहन निर्माणी</h2>
        <h3>ஹெவி வாகன தொழிற்சாலை</h3>
    </div>
    <img src="images/logo-right.jpg" alt="Right Logo">
</div>

<!-- Second Header -->
<div class="sub-header">
    <div>
        Welcome, <strong><?= htmlspecialchars($username) ?></strong> — Department: <strong><?= htmlspecialchars($department) ?></strong>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<!-- Third Header -->
<div class="nav-header">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="preview_flashnews.php">📰 Preview Flash News</a>
    <a href="get_news_by_date.php">📅 Get News by Date</a>
</div>

<div class="form-box">
    <h1>Enter Flash News</h1>
    <form action="save_flashnews.php" method="POST" onsubmit="return validateDate()">
        <input type="hidden" name="userid" value="<?= htmlspecialchars($userid) ?>">
        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
        <input type="hidden" name="department" value="<?= htmlspecialchars($department) ?>">

        <label>Flash Message</label>
        <textarea name="message" required placeholder="Type your flash message here..."></textarea>

        <div class="date-time-row">
    <div style="flex: 1">
        <label>Event Date:</label>
<input type="date" id="event_date" name="event_date" required min="<?= date('Y-m-d') ?>">
    </div>
    <div style="flex: 1">
        <label>Event Time</label>
        <input type="time" name="event_time" id="event_time" required>
    </div>
</div>

<div class="date-time-row">
    <div style="flex: 1">
        <label>Publish Date:</label>
<input type="date" id="publish_date" name="publish_date" required min="<?= date('Y-m-d') ?>">
    </div>
    <div style="flex: 1">
        <label>Publish Time</label>
        <input type="time" name="publish_time" id="publish_time" required>
    </div>
</div>


        <button type="submit">Submit Flash News</button>
    </form>
</div>
<center>
<div style="text-align:center; margin: 20px; width:300px">
    <a href="view_live_preview.php" target="_blank">
        <button style="padding: 10px 20px; font-size: 16px; background: #007BFF; color: white; border: none; border-radius: 5px;">
            📄 View News Log
        </button>
    </a>
</div>
    </center>

<div class="preview-box" style="position: relative;">
    <div style="position: absolute; top: 20px; right: 30px; font-weight: bold; font-size: 18px; color: <?= ($latestNews['event_status'] == 'Cancelled' ? 'red' : 'green') ?>">
        <?= htmlspecialchars($latestNews['event_status']) ?>
    </div>

    <h1>Latest Flash News Preview</h1><br>
    <p><strong>User ID:</strong> <?= htmlspecialchars($latestNews['userid']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($latestNews['username']) ?></p>
    <p><strong>Department:</strong> <?= htmlspecialchars($latestNews['department']) ?></p>
    <p><strong>Message:</strong><br><?= nl2br(htmlspecialchars($latestNews['message'])) ?></p>
    <p><strong>Event Date:</strong> <?= $latestNews['event_date'] ?> | <strong>Time:</strong> <?= $latestNews['event_time'] ?></p>
    <p><strong>Submitted At:</strong> <?= $latestNews['sent_timestamp'] ?></p>

    <form action="update_event_status.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this event?');">
        <input type="hidden" name="id" value="<?= $latestNews['id'] ?>">
        <?php if ($latestNews['event_status'] !== 'Cancelled'): ?>
            <button type="submit" name="cancel" style="background-color: red; margin-top: 20px; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">
                ❌ Cancel Event
            </button>
        <?php endif; ?>
    </form>
</div>


<script>
function validateDate() {
    const now = new Date();
    now.setSeconds(0, 0); // Remove milliseconds

    const publishDate = document.getElementById("publish_date").value;
    const publishTime = document.getElementById("publish_time").value;
    const eventDate = document.getElementById("event_date").value;
    const eventTime = document.getElementById("event_time").value;

    if (!publishDate || !publishTime || !eventDate || !eventTime) {
        alert("All date and time fields are required.");
        return false;
    }

    const publishDateTime = new Date(publishDate + 'T' + publishTime);
    const eventDateTime = new Date(eventDate + 'T' + eventTime);

    if (isNaN(publishDateTime.getTime()) || isNaN(eventDateTime.getTime())) {
        alert("Invalid date or time format.");
        return false;
    }

    if (publishDateTime.getDay() === 0) {
        alert("Publish date cannot be on a Sunday.");
        return false;
    }

    if (eventDateTime.getDay() === 0) {
        alert("Event date cannot be on a Sunday.");
        return false;
    }

    if (publishDateTime.getTime() < now.getTime()) {
        alert("Publish date/time must be now or in the future.");
        return false;
    }

    if (eventDateTime.getTime() <= publishDateTime.getTime()) {
        alert("Event date/time must be after publish date/time.");
        return false;
    }

    return true;
}
</script>



</body>
</html>
