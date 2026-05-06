<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Project ID missing.";
    exit;
}

$project_id = $_GET['id'];
$message = '';

// check user role
$stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role_id'] != 1 && $user['role_id'] != 2) {
    echo "Access denied.";
    exit;
}

// get project
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    echo "Project not found.";
    exit;
}

// get managers
$stmt = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role_id = 2");
$managers = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $manager_id = $_POST['manager_id'];
    $status = $_POST['status'];

    if (empty($name) || empty($start_date) || empty($manager_id) || empty($status)) {
        $message = "Name, start date, manager, and status are required.";
    } else {

        if (
            $name == $project['name'] &&
            $description == $project['description'] &&
            $start_date == $project['start_date'] &&
            $end_date == $project['end_date'] &&
            $manager_id == $project['manager_id'] &&
            $status == $project['status']
        ) {
            $message = "No changes made.";
        } else {

            $stmt = $pdo->prepare("
                UPDATE projects
                SET name = ?, description = ?, start_date = ?, end_date = ?, manager_id = ?, status = ?
                WHERE id = ?
            ");

            $stmt->execute([$name, $description, $start_date, $end_date, $manager_id, $status, $project_id]);

            $message = "Project updated.";

            $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            $project = $stmt->fetch();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow">
                <div class="card-body">

                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">Edit Project</h2>
                        <a href="projects.php" class="btn btn-outline-dark btn-sm">Back</a>
                    </div>

                    <!-- Message -->
                    <?php if ($message): ?>
                        <div class="alert alert-info">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?php echo htmlspecialchars($project['name']); ?>">
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($project['description']); ?></textarea>
                        </div>

                        <!-- Dates -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control"
                                       value="<?php echo $project['start_date']; ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control"
                                       value="<?php echo $project['end_date']; ?>">
                            </div>
                        </div>

                        <!-- Manager -->
                        <div class="mb-3">
                            <label class="form-label">Manager</label>
                            <select name="manager_id" class="form-select">
                                <?php foreach ($managers as $manager): ?>
                                    <option value="<?php echo $manager['id']; ?>"
                                        <?php if ($manager['id'] == $project['manager_id']) echo 'selected'; ?>>
                                        <?php echo $manager['first_name'] . ' ' . $manager['last_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?php if ($project['status'] == 'active') echo 'selected'; ?>>
                                    Active
                                </option>
                                <option value="inactive" <?php if ($project['status'] == 'inactive') echo 'selected'; ?>>
                                    Inactive
                                </option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                Update Project
                            </button>

                            <a href="projects.php" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

