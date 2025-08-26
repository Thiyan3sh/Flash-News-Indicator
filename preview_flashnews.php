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
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);

// Delete only expired approved news
$conn->query("DELETE FROM flash_news WHERE approval_status='Approved' AND event_datetime < NOW()");

// Handle cancel/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $message = $_POST['message'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_status = $_POST['event_status'];

    $stmt = $conn->prepare("UPDATE flash_news SET message=?, event_date=?, event_time=?, event_status=? WHERE id=?");
    $stmt->bind_param("ssssi", $message, $event_date, $event_time, $event_status, $id);
    $stmt->execute();
    header("Location: preview_flashnews.php");
    exit();
}

// Filters
$newsList = [];
$today = date("Y-m-d");
$endDate = $today;
$filter = $_GET['filter'] ?? '3days';

if ($filter === "3days") {
    $endDate = date("Y-m-d", strtotime("+3 days"));
} elseif ($filter === "1month") {
    $endDate = date("Y-m-d", strtotime("+1 month"));
} elseif ($filter === "custom" && isset($_GET['to_date'])) {
    $endDate = $_GET['to_date'];
}

// Fetch: approved news, cancelled events (still pending), rejected by owner/admin
$stmt = $conn->prepare("
    SELECT * FROM flash_news 
    WHERE 
        (
            approval_status = 'Approved' AND event_date BETWEEN ? AND ? 
        )
        OR (
            approval_status = 'Rejected' AND userid = ?
        )
        OR (
            approval_status = 'Rejected' AND ? = 'Administration'
        )
        OR (
            event_status = 'Cancelled' AND event_datetime >= NOW()
        )
    ORDER BY event_date ASC, event_time ASC
");
$stmt->bind_param("ssss", $today, $endDate, $userid, $department);
$stmt->execute();
$result = $stmt->get_result();
$newsList = $result->fetch_all(MYSQLI_ASSOC);

// Edit mode
$editData = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $res = $conn->query("SELECT * FROM flash_news WHERE id=$edit_id AND userid = '$userid'");
    if ($res && $res->num_rows > 0) {
        $editData = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Preview Flash News</title>
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

        .header .title-block {
            text-align: center;
            flex: 1;
        }

        .header img { height: 90px; margin: 0 20px; }

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

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-right: 30px;
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

        .form-container {
            max-width: 800px;
            margin: auto;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        form input[type="date"],
        form input[type="time"],
        form textarea,
        form select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .news-box {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 8px;
            position: relative;
        }

        .news-box .status {
            position: absolute;
            top: 10px;
            right: 20px;
            font-weight: bold;
            color: green;
        }

        .news-box .status.cancelled {
            color: red;
        }

        .news-box .status.rejected {
            color: #ff6600;
        }

        .news-box p {
            margin: 6px 0;
        }

        .filter-form {
            text-align: center;
            margin-bottom: 20px;
        }

        .filter-form select,
        .filter-form input[type="date"],
        .filter-form button {
            padding: 8px 12px;
            font-size: 15px;
            margin-left: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .filter-form button {
            background-color: #1e90ff;
            color: white;
            cursor: pointer;
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

<div style="text-align:center; margin: 20px;">
    <a href="view_live_preview.php" target="_blank">
        <button style="padding: 10px 20px; font-size: 16px; background: #007BFF; color: white; border: none; border-radius: 5px;">
            📄 View News Log
        </button>
    </a>
</div>

<div class="form-container">
    <h2><?= $editData ? 'Edit Flash News' : 'Flash News List' ?></h2>

    <div class="filter-form">
        <form method="GET">
            <label><strong>📅 View Flash News for:</strong></label>
            <select name="filter" onchange="this.form.submit()">
                <option value="3days" <?= $filter === '3days' ? 'selected' : '' ?>>Next 3 Days</option>
                <option value="1month" <?= $filter === '1month' ? 'selected' : '' ?>>Next 1 Month</option>
                <option value="custom" <?= $filter === 'custom' ? 'selected' : '' ?>>Custom Date</option>
            </select>
            <?php if ($filter === 'custom'): ?>
                <input type="date" name="to_date" value="<?= htmlspecialchars($endDate) ?>" min="<?= date('Y-m-d') ?>" required>
                <button type="submit">Apply</button>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($department === 'Administration'): ?>
        <h3>🟡 Pending News for Approval</h3>
        <?php
        $pending = $conn->query("SELECT * FROM flash_news WHERE approval_status = 'Pending'");
        while ($row = $pending->fetch_assoc()):
        ?>
        <div class="news-box" style="background:#f5f5f5;">
            <p><strong>User:</strong> <?= $row['username'] ?> (<?= $row['department'] ?>)</p>
            <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($row['message'])) ?></p>
            <p><strong>Event:</strong> <?= $row['event_date'] ?> <?= $row['event_time'] ?></p>
            <a href="approve_news.php?id=<?= $row['id'] ?>&action=approve">✅ Approve</a> |
            <a href="approve_news.php?id=<?= $row['id'] ?>&action=reject">❌ Reject</a>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <?php if ($editData): ?>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
            <label>Message:</label>
            <textarea name="message" required><?= htmlspecialchars($editData['message']) ?></textarea>
            <label>Event Date:</label>
            <input type="date" name="event_date" value="<?= $editData['event_date'] ?>" required>
            <label>Event Time:</label>
            <input type="time" name="event_time" value="<?= $editData['event_time'] ?>" required>
            <label>Event Status:</label>
            <select name="event_status" required>
                <option value="Scheduled" <?= $editData['event_status'] == 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                <option value="Cancelled" <?= $editData['event_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
            <button type="submit">Update News</button>
        </form>
    <?php endif; ?>

    <?php foreach ($newsList as $news): ?>
        <div class="news-box">
            <?php if ($news['event_status'] === 'Cancelled'): ?>
                <div class="status cancelled">Cancelled</div>
            <?php elseif ($news['approval_status'] === 'Rejected'): ?>
                <div class="status rejected">Rejected</div>
            <?php else: ?>
                <div class="status"><?= htmlspecialchars($news['event_status']) ?></div>
            <?php endif; ?>

            <p><strong>User ID:</strong> <?= htmlspecialchars($news['userid']) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($news['username']) ?></p>
            <p><strong>Department:</strong> <?= htmlspecialchars($news['department']) ?></p>
            <p><strong>Message:</strong><br><?= nl2br(htmlspecialchars($news['message'])) ?></p>
            <p><strong>Event Date:</strong> <?= htmlspecialchars($news['event_date']) ?></p>
            <p><strong>Event Time:</strong> <?= htmlspecialchars($news['event_time']) ?></p>
            <p><strong>Submitted At:</strong> <?= htmlspecialchars($news['sent_timestamp']) ?></p>

            <?php if ($news['userid'] === $userid && $news['approval_status'] !== 'Rejected'): ?>
                <a href="?edit_id=<?= $news['id'] ?>">✏️ Edit</a>
                <form method="POST" onsubmit="return confirm('Cancel this event?');" style="display:inline;">
                    <input type="hidden" name="edit_id" value="<?= $news['id'] ?>">
                    <input type="hidden" name="message" value="<?= htmlspecialchars($news['message']) ?>">
                    <input type="hidden" name="event_date" value="<?= $news['event_date'] ?>">
                    <input type="hidden" name="event_time" value="<?= $news['event_time'] ?>">
                    <input type="hidden" name="event_status" value="Cancelled">
                    <button type="submit">❌ Cancel</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
