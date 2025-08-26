<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "flashnews_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department = $_POST['department'];
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE department = ? AND userid = ? AND password = ?");
    $stmt->bind_param("sss", $department, $userid, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['userid'] = $userid;
        $_SESSION['department'] = $department;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Flash News</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary: #2b6cb0;
        --primary-dark: #245796;
        --bg: #f4f6f8;
        --text: #333;
        --white: #ffffff;
        --shadow: rgba(0, 0, 0, 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background-color: var(--bg);
    }

    .header {
        background: linear-gradient(135deg, #1e90ff, #32cd32);
        color: white;
        padding: 30px 20px;
        position: fixed;
        width: 100%;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 1000;
        height: 120px;
    }

    .title-block {
        text-align: center;
        flex: 1;
    }

    .header img {
        height: 80px;
        margin: 0 20px;
    }

    .header h1 {
        font-size: 28px;
        margin: 4px 0;
        color: #ffffff;
        font-weight: 600;
    }

    .header h2 {
        font-size: 20px;
        margin: 2px 0;
        color: #ffffcc;
        font-weight: 500;
    }

    .header h3 {
        font-size: 20px;
        margin: 2px 0;
        color: #ccffff;
        font-weight: 500;
    }

    .login-box {
        margin: 180px auto;
        max-width: 400px;
        padding: 30px;
        background: var(--white);
        border-radius: 10px;
        box-shadow: 0 4px 20px var(--shadow);
        transition: box-shadow 0.3s ease-in-out;
    }

    .login-box:hover {
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
    }

    .login-box h2 {
        text-align: center;
        margin-bottom: 25px;
        color: var(--primary);
        font-weight: 600;
    }

    label {
        display: block;
        margin-top: 12px;
        font-weight: 500;
        color: var(--text);
    }

    input, select {
        width: 100%;
        padding: 12px;
        margin-top: 5px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.2s;
    }

    input:focus, select:focus {
        border-color: var(--primary);
        outline: none;
    }

    input[type="submit"] {
        background-color: var(--primary);
        color: white;
        border: none;
        cursor: pointer;
        font-size: 17px;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: var(--primary-dark);
    }

    .error {
        color: red;
        text-align: center;
        margin-bottom: 10px;
    }

    @media (max-width: 480px) {
        .login-box {
            margin: 160px 20px;
            padding: 20px;
        }

        .header {
            flex-direction: column;
            height: auto;
            padding: 20px;
        }

        .header img {
            height: 50px;
            margin-bottom: 10px;
        }
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

<div class="login-box">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" action="">
        <label for="department">Department</label>
        <select name="department" required>
    <option value="">Select Department</option>
    <option value="Administration">Administration</option>
    <option value="ITC">ITC</option>
    <option value="Manufacturing">Manufacturing</option>
    <option value="Research & Development (R&D)">Research & Development (R&D)</option>
    <option value="Quality Control">Quality Control</option>
    <option value="Engineering">Engineering</option>
    <option value="Finance">Finance</option>
    <option value="Material Management">Material Management</option>
</select>


        <label for="userid">User ID</label>
        <input type="text" name="userid" required>

        <label for="password">Password</label>
        <input type="password" name="password" required>

        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>
