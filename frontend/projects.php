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

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand" href="dashboard.php">My System</a>

        <div class="ms-auto d-flex gap-2">

            <a href="tasks.php" class="btn btn-outline-light btn-sm">
                Tasks
            </a>

            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                Dashboard
            </a>

            <a href="logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>

        </div>

    </div>
</nav>

<!-- PAGE CONTENT -->
<div class="container py-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Projects</h2>

        <a href="create_project.php" class="btn btn-success">
            + Create Project
        </a>
    </div>

    <!-- CARD -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0 align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Manager</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (count($projects) === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No projects found
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><strong><?php echo $project['name']; ?></strong></td>
                                <td><?php echo $project['description']; ?></td>
                                <td><?php echo $project['start_date']; ?></td>
                                <td><?php echo $project['end_date']; ?></td>
                                <td>
                                    <?php echo $project['first_name'] . ' ' . $project['last_name']; ?>
                                </td>
                                <td>
                                    <a href="edit_projects.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
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