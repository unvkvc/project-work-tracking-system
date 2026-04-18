
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

echo "You are logged in. User ID: " . $_SESSION['user_id'];
?>

<br>
<a href="create_project.php">Create Project</a><br>
<a href="projects.php">View Projects</a><br>
<a href="create_task.php">Create Task</a><br>
<a href="tasks.php">View Tasks</a><br>
<a href="logout.php">Logout</a>