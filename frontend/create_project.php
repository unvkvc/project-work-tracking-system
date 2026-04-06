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
        $message = "Name, start date, and manager are required.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO projects (name, description, start_date, end_date, manager_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$name, $description, $start_date, $end_date, $manager_id]);
        $message = "Project created successfully.";
    }
}
?>

<h2>Create Project</h2>

<form method="POST">
    <label>Project name:</label><br>
    <input type="text" name="name"><br><br>

    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>

    <label>Start date:</label><br>
    <input type="date" name="start_date"><br><br>

    <label>End date:</label><br>
    <input type="date" name="end_date"><br><br>

    <label>Project manager:</label><br>
    <select name="manager_id">
        <option value="">Select manager</option>
        <?php foreach ($managers as $manager): ?>
            <option value="<?php echo $manager['id']; ?>">
                <?php echo $manager['first_name'] . ' ' . $manager['last_name']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Create Project</button>
</form>

<p><?php echo $message; ?></p>

<br>
<a href="projects.php">View all projects</a>