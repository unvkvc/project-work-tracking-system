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

$stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role_id'] != 1 && $user['role_id'] != 2) {
    echo "Access denied.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    echo "Project not found.";
    exit;
}

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

        // check if anything actually changed
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

            // reload updated project
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            $project = $stmt->fetch();
        }
    }
}
?>

<h2>Edit Project</h2>

<p><?php echo $message; ?></p>

<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo $project['name']; ?>"><br><br>

    <label>Description:</label><br>
    <textarea name="description"><?php echo $project['description']; ?></textarea><br><br>

    <label>Start date:</label><br>
    <input type="date" name="start_date" value="<?php echo $project['start_date']; ?>"><br><br>

    <label>End date:</label><br>
    <input type="date" name="end_date" value="<?php echo $project['end_date']; ?>"><br><br>

    <label>Manager:</label><br>
    <select name="manager_id">
        <?php foreach ($managers as $manager): ?>
            <option value="<?php echo $manager['id']; ?>"
                <?php if ($manager['id'] == $project['manager_id']) echo 'selected'; ?>>
                <?php echo $manager['first_name'] . ' ' . $manager['last_name']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Status:</label><br>
    <select name="status">
        <option value="active" <?php if ($project['status'] == 'active') echo 'selected'; ?>>Active</option>
        <option value="inactive" <?php if ($project['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
    </select><br><br>

    <button type="submit">Update Project</button>
</form>

<br>
<a href="projects.php">Back to Projects</a>