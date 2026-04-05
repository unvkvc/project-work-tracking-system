
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

echo "You are logged in. User ID: " . $_SESSION['user_id'];
?>

<br>
<a href="logout.php">Logout</a>