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

// employee sees only their tasks
if ($user['role_id'] == 3) {
    $stmt = $pdo->prepare("
        SELECT 
            tasks.name AS task_name,
            tasks.description,
            tasks.status,
            tasks.deadline,
            projects.name AS project_name,
            users.first_name,
            users.last_name
        FROM tasks
        JOIN projects ON tasks.project_id = projects.id
        JOIN users ON tasks.assigned_user_id = users.id
        WHERE tasks.assigned_user_id = ?
        ORDER BY tasks.id DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    // admin and project manager see all tasks
    $stmt = $pdo->query("
        SELECT 
            tasks.name AS task_name,
            tasks.description,
            tasks.status,
            tasks.deadline,
            projects.name AS project_name,
            users.first_name,
            users.last_name
        FROM tasks
        JOIN projects ON tasks.project_id = projects.id
        JOIN users ON tasks.assigned_user_id = users.id
        ORDER BY tasks.id DESC
    ");
}

$tasks = $stmt->fetchAll();
?>

<h2>Tasks</h2>

<?php if ($user['role_id'] == 1 || $user['role_id'] == 2): ?>
    <a href="create_task.php">Create new task</a>
    <br><br>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Task name</th>
        <th>Description</th>
        <th>Status</th>
        <th>Deadline</th>
        <th>Project</th>
        <th>Assigned user</th>
    </tr>

    <?php foreach ($tasks as $task): ?>
        <tr>
            <td><?php echo $task['task_name']; ?></td>
            <td><?php echo $task['description']; ?></td>
            <td><?php echo $task['status']; ?></td>
            <td><?php echo $task['deadline']; ?></td>
            <td><?php echo $task['project_name']; ?></td>
            <td><?php echo $task['first_name'] . ' ' . $task['last_name']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>