<?php
session_start();
require '../backend/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get current user
$stmt = $pdo->prepare("
    SELECT id, first_name, last_name, role_id
    FROM users
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get projects managed by current user
$managedProjects = [];

if ($user['role_id'] == 2) {
    $stmt = $pdo->prepare("
        SELECT id, name, status, start_date, end_date
        FROM projects
        WHERE manager_id = ?
        ORDER BY start_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $managedProjects = $stmt->fetchAll();
}

// Get tasks assigned to current employee
$myTasks = [];

if ($user['role_id'] == 3) {
    $stmt = $pdo->prepare("
        SELECT 
            tasks.id,
            tasks.name,
            tasks.status,
            tasks.deadline,
            projects.name AS project_name
        FROM tasks
        JOIN projects ON tasks.project_id = projects.id
        WHERE tasks.assigned_user_id = ?
        ORDER BY tasks.deadline ASC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $myTasks = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: #f4f6f9; }

        .navbar { box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); }

        .dashboard-card {
            border: 0;
            border-radius: 20px;
            transition: 0.3s;
        }

        .dashboard-card:hover { transform: translateY(-2px); }

        .dashboard-btn {
            padding: 14px;
            border-radius: 14px;
            font-weight: 600;
            transition: 0.2s;
            border: none;
        }

        .dashboard-btn:hover { transform: scale(1.02); }

        .project-item:hover {
            background-color: #f8f9fa;
            transition: 0.2s;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-kanban"></i> Project Tracker
        </a>

        <div class="ms-auto d-flex align-items-center gap-2">

            <a href="manual.php"
               class="text-light text-decoration-none"
               title="Help & Documentation">
                <i class="bi bi-question-circle"></i>
            </a>

            <a href="logout.php" class="btn btn-danger btn-sm rounded-3">
                Logout
            </a>

</div>
        </div>
    </div>
</nav>

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card dashboard-card shadow-sm">
                <div class="card-body text-center p-5">

                    <h2 class="fw-bold mb-3 text-primary">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </h2>

                    <p class="text-secondary mb-5">
                        Logged in as
                        <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                    </p>

                    <div class="d-grid gap-3">

                        <a href="projects.php" class="btn dashboard-btn text-white" style="background-color:#198754">
                            <i class="bi bi-folder2-open"></i> View Projects
                        </a>

                        <a href="create_project.php" class="btn dashboard-btn" style="background-color:#90EE90">
                            <i class="bi bi-folder-plus"></i> Create Project
                        </a>

                        <a href="tasks.php" class="btn dashboard-btn" style="background-color:#FFC107">
                            <i class="bi bi-list-task"></i> View Tasks
                        </a>

                        <a href="create_task.php" class="btn dashboard-btn" style="background-color:#FFF59D">
                            <i class="bi bi-plus-circle"></i> Create Task
                        </a>

                        <a href="time_entries.php" class="btn dashboard-btn text-white" style="background-color:#0d6efd">
                            <i class="bi bi-clock-history"></i> View Time Entries
                        </a>

                        <a href="log_time.php" class="btn dashboard-btn" style="background-color:#ADD8E6">
                            <i class="bi bi-stopwatch"></i> Log Time
                        </a>

                        <?php if ($user['role_id'] == 1): ?>
                            <a href="manage_users.php" class="btn dashboard-btn text-white" style="background-color:#212529">
                                <i class="bi bi-people"></i> Manage Users
                            </a>
                        <?php endif; ?>

                    </div>

                </div>
            </div>

        </div>
    </div>

    <?php if ($user['role_id'] == 2): ?>
        <div class="row justify-content-center mt-4">
            <div class="col-md-7">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body">

                        <h5 class="mb-3">My Projects</h5>

                        <?php if (count($managedProjects) === 0): ?>
                            <p class="text-muted mb-0">You are not managing any projects yet.</p>
                        <?php else: ?>

                            <?php foreach ($managedProjects as $project): ?>
                                <a href="edit_projects.php?id=<?php echo $project['id']; ?>" class="text-decoration-none text-dark">
                                    <div class="project-item d-flex justify-content-between align-items-center border-bottom py-2 px-2 rounded">
                                        <div>
                                            <strong><?php echo htmlspecialchars($project['name']); ?></strong><br>
                                            <small class="text-muted">
                                                <?php echo $project['start_date']; ?> - <?php echo $project['end_date']; ?>
                                            </small>
                                        </div>

                                        <?php if ($project['status'] == 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($user['role_id'] == 3): ?>
        <div class="row justify-content-center mt-4">
            <div class="col-md-7">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body">

                        <h5 class="mb-3">My Tasks</h5>

                        <?php if (count($myTasks) === 0): ?>
                            <p class="text-muted mb-0">You have no assigned tasks.</p>
                        <?php else: ?>

                            <?php foreach ($myTasks as $task): ?>
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="text-decoration-none text-dark">
                                    <div class="project-item d-flex justify-content-between align-items-center border-bottom py-2 px-2 rounded">
                                        <div>
                                            <strong><?php echo htmlspecialchars($task['name']); ?></strong><br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($task['project_name']); ?>
                                                <?php if (!empty($task['deadline'])): ?>
                                                    | Deadline: <?php echo $task['deadline']; ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>

                                        <?php if ($task['status'] == 'done' || $task['status'] == 'completed'): ?>
                                            <span class="badge bg-success">Done</span>
                                        <?php elseif ($task['status'] == 'in_progress'): ?>
                                            <span class="badge bg-warning text-dark">In Progress</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">To Do</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>