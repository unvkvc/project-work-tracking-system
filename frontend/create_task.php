<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get current user
$stmt = $pdo->prepare("SELECT id, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// only admin and project manager can create tasks
if ($user['role_id'] != 1 && $user['role_id'] != 2) {
    echo "Access denied.";
    exit;
}

$message = '';

// fetch projects
$stmt = $pdo->query("SELECT id, name FROM projects ORDER BY name");
$projects = $stmt->fetchAll();

// fetch employees
$stmt = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role_id = 3 ORDER BY first_name, last_name");
$employees = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $deadline = $_POST['deadline'];
    $project_id = $_POST['project_id'];
    $assigned_user_id = $_POST['assigned_user_id'];

    if (empty($name) || empty($status) || empty($project_id) || empty($assigned_user_id)) {
        $message = "Name, status, project, and assigned user are required.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tasks (name, description, status, deadline, project_id, assigned_user_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([$name, $description, $status, $deadline, $project_id, $assigned_user_id]);
        $message = "Task created successfully.";
    }
}
?>

<h2>Create Task</h2>

<form method="POST">
    <label>Task name:</label><br>
    <input type="text" name="name"><br><br>

    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>

    <label>Status:</label><br>
    <select name="status">
        <option value="">Select status</option>
        <option value="todo">To do</option>
        <option value="in_progress">In progress</option>
        <option value="done">Done</option>
    </select><br><br>

    <label>Deadline:</label><br>
    <input type="date" name="deadline"><br><br>

    <label>Project:</label><br>
    <select name="project_id">
        <option value="">Select project</option>
        <?php foreach ($projects as $project): ?>
            <option value="<?php echo $project['id']; ?>">
                <?php echo $project['name']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Assign to employee:</label><br>
    <select name="assigned_user_id">
        <option value="">Select employee</option>
        <?php foreach ($employees as $employee): ?>
            <option value="<?php echo $employee['id']; ?>">
                <?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Create Task</button>
</form>

<p><?php echo $message; ?></p>

<br>
<a href="tasks.php">View all tasks</a>