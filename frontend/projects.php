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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Projects</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Projects</h2>

        <a href="create_project.php" class="btn btn-success">
            Create new project
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Manager</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?php echo $project['name']; ?></td>
                                <td><?php echo $project['description']; ?></td>
                                <td><?php echo $project['start_date']; ?></td>
                                <td><?php echo $project['end_date']; ?></td>
                                <td>
                                    <?php echo $project['first_name'] . ' ' . $project['last_name']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

</body>
</html>