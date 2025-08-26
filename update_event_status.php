<?php
$conn = new mysqli("localhost", "root", "", "flashnews_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_POST['cancel']) && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("UPDATE flash_news SET event_status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Failed to cancel event.";
    }
}
?>
