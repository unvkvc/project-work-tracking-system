<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Task ID missing.";
    exit;
}

$task_id = $_GET['id'];

// get current user
$stmt = $pdo->prepare("SELECT id, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// get task
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch();

if (!$task) {
    echo "Task not found.";
    exit;
}

// permission check
if ($user['role_id'] == 3 && $task['assigned_user_id'] != $_SESSION['user_id']) {
    echo "Access denied.";
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    if ($status === $task['status'] && $description === $task['description']) {
        $message = "No changes were made.";
    } else {
        $stmt = $pdo->prepare("
            UPDATE tasks
            SET status = ?, description = ?
            WHERE id = ?
        ");

        $stmt->execute([$status, $description, $task_id]);

        $message = "Task updated.";

        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
        $task = $stmt->fetch();
    }
}
?>

<h2>Edit Task</h2>

<p><?php echo $message; ?></p>

<form method="POST">
    <label>Status:</label><br>
    <select name="status">
        <option value="todo" <?php if ($task['status'] == 'todo') echo 'selected'; ?>>To do</option>
        <option value="in_progress" <?php if ($task['status'] == 'in_progress') echo 'selected'; ?>>In progress</option>
        <option value="done" <?php if ($task['status'] == 'done') echo 'selected'; ?>>Done</option>
    </select><br><br>

    <label>Description / completed work:</label><br>
    <textarea name="description"><?php echo $task['description']; ?></textarea><br><br>

    <button type="submit">Update Task</button>
</form>

<br>
<a href="tasks.php">Back to tasks</a>