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

if ($user['role_id'] == 3) {
    $stmt = $pdo->prepare("
        SELECT 
            time_entries.id AS time_entry_id,
            time_entries.work_date,
            time_entries.hours_worked,
            time_entries.description,
            tasks.name AS task_name,
            projects.name AS project_name,
            users.first_name,
            users.last_name
        FROM time_entries
        JOIN tasks ON time_entries.task_id = tasks.id
        JOIN projects ON tasks.project_id = projects.id
        JOIN users ON time_entries.user_id = users.id
        WHERE time_entries.user_id = ?
        ORDER BY time_entries.work_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->query("
        SELECT 
            time_entries.id AS time_entry_id,
            time_entries.work_date,
            time_entries.hours_worked,
            time_entries.description,
            tasks.name AS task_name,
            projects.name AS project_name,
            users.first_name,
            users.last_name
        FROM time_entries
        JOIN tasks ON time_entries.task_id = tasks.id
        JOIN projects ON tasks.project_id = projects.id
        JOIN users ON time_entries.user_id = users.id
        ORDER BY time_entries.work_date DESC
    ");
}

$entries = $stmt->fetchAll();
?>

<h2>Time Entries</h2>

<a href="log_time.php">Log Time</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Date</th>
        <th>Project</th>
        <th>Task</th>
        <th>User</th>
        <th>Hours</th>
        <th>Description</th>
        <th>Action</th>
    </tr>

    <?php foreach ($entries as $entry): ?>
        <tr>
            <td><?php echo $entry['work_date']; ?></td>
            <td><?php echo $entry['project_name']; ?></td>
            <td><?php echo $entry['task_name']; ?></td>
            <td><?php echo $entry['first_name'] . ' ' . $entry['last_name']; ?></td>
            <td><?php echo $entry['hours_worked']; ?></td>
            <td><?php echo $entry['description']; ?></td>
            <td>
                <a href="edit_time_entry.php?id=<?php echo $entry['time_entry_id']; ?>">Edit</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>