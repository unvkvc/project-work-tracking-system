<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

// get current user
$stmt = $pdo->prepare("SELECT id, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// employees see only their tasks, managers/admins see all tasks
if ($user['role_id'] == 3) {
    $stmt = $pdo->prepare("
        SELECT 
            tasks.id AS task_id,
            tasks.name AS task_name,
            projects.name AS project_name
        FROM tasks
        JOIN projects ON tasks.project_id = projects.id
        WHERE tasks.assigned_user_id = ?
        ORDER BY projects.name, tasks.name
    ");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->query("
        SELECT 
            tasks.id AS task_id,
            tasks.name AS task_name,
            projects.name AS project_name
        FROM tasks
        JOIN projects ON tasks.project_id = projects.id
        ORDER BY projects.name, tasks.name
    ");
}

$tasks = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $work_date = $_POST['work_date'];

    $hours = (int) $_POST['hours'];
    $minutes = (int) $_POST['minutes'];
    $hours_worked = $hours + ($minutes / 60);

    $description = trim($_POST['description']);

    if (empty($task_id) || empty($work_date) || ($hours == 0 && $minutes == 0)) {
        $message = "Task, date, and time are required.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO time_entries (user_id, task_id, work_date, hours_worked, description)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION['user_id'],
            $task_id,
            $work_date,
            $hours_worked,
            $description
        ]);

        $message = "Time entry saved.";
    }
}
?>

<h2>Log Time</h2>

<p><?php echo $message; ?></p>

<form method="POST">
    <label>Project / Task:</label><br>
    <select name="task_id">
        <option value="">Select task</option>
        <?php foreach ($tasks as $task): ?>
            <option value="<?php echo $task['task_id']; ?>">
                <?php echo $task['project_name'] . ' - ' . $task['task_name']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Date:</label><br>
    <input type="date" name="work_date"><br><br>

    <label>Hours worked:</label><br>
    <label>Hours:</label><br>
<select name="hours">
    <?php for ($i = 0; $i <= 12; $i++): ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
    <?php endfor; ?>
</select>

<label>Minutes:</label><br>
<select name="minutes">
    <option value="0">00</option>
    <option value="15">15</option>
    <option value="30">30</option>
    <option value="45">45</option>
</select>
<br><br>

    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>

    <button type="submit">Save Time</button>
</form>

<br>
<a href="time_entries.php">View Time Entries</a><br>
<a href="dashboard.php">Back to Dashboard</a>