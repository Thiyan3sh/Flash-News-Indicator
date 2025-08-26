<?php
session_start();
date_default_timezone_set("Asia/Kolkata");

if (!isset($_SESSION['userid'], $_SESSION['username'], $_SESSION['department'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$department = $_SESSION['department'];

$conn = new mysqli("localhost", "root", "", "flashnews_db");
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);
$conn->query("DELETE FROM flash_news WHERE event_datetime < NOW()");


$selectedDate = $_GET['date'] ?? date("Y-m-d");
$stmt = $conn->prepare("SELECT * FROM flash_news WHERE event_date = ? ORDER BY event_time ASC");
$stmt->bind_param("s", $selectedDate);
$stmt->execute();
$result = $stmt->get_result();
$newsList = $result->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['download']) && $_GET['download'] === 'txt') {
    header('Content-Type: text/plain');
    header("Content-Disposition: attachment; filename=FlashNews_$selectedDate.txt");
    foreach ($newsList as $news) {
        echo "User ID: {$news['userid']}\n";
        echo "Name: {$news['username']}\n";
        echo "Department: {$news['department']}\n";
        echo "Message: {$news['message']}\n";
        echo "Event Time: {$news['event_time']}\n";
        echo "Submitted At: {$news['sent_timestamp']}\n";
        echo "-------------------------\n";
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>News by Date</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding-top: 280px;
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

        .title-block {
            flex: 1;
            text-align: center;
        }

        .header img {
            height: 90px;
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
            width: 99%;
            top: 150px;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 999;
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

        .nav-header {
            background-color: #e0e0e0;
            padding: 15px 20px;
            position: fixed;
            width: 99%;
            top: 220px;
            text-align: center;
            z-index: 998;
        }

        .nav-header a {
            margin: 0 20px;
            font-weight: bold;
            color: #007BFF;
            text-decoration: none;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        .news-box {
            background-color: #fff3cd;
            padding: 15px;
            border: 1px solid #ffeeba;
            border-radius: 8px;
            margin-top: 20px;
        }

        form {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        form input[type="date"] {
            padding: 8px 12px;
            font-size: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        form button {
            padding: 8px 16px;
            font-size: 15px;
            background-color: #1e90ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form button[name="download"] {
            background-color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/logo-left.png" alt="Left Logo">
        <div class="title-block">
            <h1>Heavy Vehicles Factory</h1>
            <h2>भारी वाहन निर्माणी</h2>
            <h3>ஹெவி வாகன தொழிற்சாலை</h3>
        </div>
        <img src="images/logo-right.jpg" alt="Right Logo">
    </div>

    <div class="sub-header">
        <div>
            Welcome, <strong><?= htmlspecialchars($username) ?></strong> — Department: <strong><?= htmlspecialchars($department) ?></strong>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="nav-header">
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="preview_flashnews.php">📰 Preview Flash News</a>
        <a href="get_news_by_date.php">📅 Get News by Date</a>
    </div>

    <div class="container">
        <form method="GET">
            <label for="date"><strong>Select Date:</strong></label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>" required>
            <button type="submit">🔍 View</button>
            <button type="submit" name="download" value="txt">⬇️ Download</button>
        </form>

        <?php if (empty($newsList)): ?>
            <p>No news found for the selected date.</p>
        <?php else: ?>
            <?php foreach ($newsList as $news): ?>
                <div class="news-box">
                    <p><strong>User ID:</strong> <?= htmlspecialchars($news['userid']) ?></p>
                    <p><strong>Name:</strong> <?= htmlspecialchars($news['username']) ?></p>
                    <p><strong>Department:</strong> <?= htmlspecialchars($news['department']) ?></p>
                    <p><strong>Message:</strong><br><?= nl2br(htmlspecialchars($news['message'])) ?></p>
                    <p><strong>Event Time:</strong> <?= htmlspecialchars($news['event_time']) ?></p>
                    <p><strong>Submitted At:</strong> <?= htmlspecialchars($news['sent_timestamp']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
