<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get current user
$stmt = $pdo->prepare("SELECT id, first_name, last_name, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// allow only admin (1) and project manager (2)
if ($user['role_id'] != 1 && $user['role_id'] != 2) {
    echo "Access denied.";
    exit;
}

$message = '';

$stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE role_id = 2");
$stmt->execute();
$managers = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $manager_id = $_POST['manager_id'];

    if (empty($name) || empty($start_date) || empty($manager_id)) {
        $message = "Name, start date and manager are required.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO projects (name, description, start_date, end_date, manager_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $description,
            $start_date,
            $end_date,
            $manager_id
        ]);

        $message = "Project created successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Project</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Create Project</h2>
        <a href="projects.php" class="btn btn-secondary">Back to projects</a>
    </div>

    <div class="card shadow">
        <div class="card-body">

            <?php if ($message): ?>
                <div class="alert alert-info">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Project name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">End date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Project manager</label>
                    <select name="manager_id" class="form-select" required>
                        <option value="">Select manager</option>
                        <?php foreach ($managers as $manager): ?>
                            <option value="<?php echo $manager['id']; ?>">
                                <?php echo $manager['first_name'] . ' ' . $manager['last_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Create Project
                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>