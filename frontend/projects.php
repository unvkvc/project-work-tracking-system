<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("
    SELECT projects.*, users.first_name, users.last_name
    FROM projects
    JOIN users ON projects.manager_id = users.id
    ORDER BY projects.id DESC
");

$projects = $stmt->fetchAll();
?>

<h2>Projects</h2>

<a href="create_project.php">Create new project</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Start date</th>
        <th>End date</th>
        <th>Manager</th>
    </tr>

    <?php foreach ($projects as $project): ?>
        <tr>
            <td><?php echo $project['name']; ?></td>
            <td><?php echo $project['description']; ?></td>
            <td><?php echo $project['start_date']; ?></td>
            <td><?php echo $project['end_date']; ?></td>
            <td><?php echo $project['first_name'] . ' ' . $project['last_name']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>