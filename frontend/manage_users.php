<?php
require '../backend/db.php';
session_start();

// must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get current user
$stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

// only admin allowed
if ($currentUser['role_id'] != 1) {
    echo "Access denied.";
    exit;
}

$message = '';

// handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role_id'];

    $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);

    $message = "Role updated.";
}

// fetch all users + role names
$stmt = $pdo->query("
    SELECT users.id, users.first_name, users.last_name, users.email, roles.role_name, users.role_id
    FROM users
    JOIN roles ON users.role_id = roles.id
    ORDER BY users.id
");

$users = $stmt->fetchAll();

// fetch roles for dropdown
$stmt = $pdo->query("SELECT id, role_name FROM roles");
$roles = $stmt->fetchAll();
?>

<h2>Manage Users</h2>

<p><?php echo $message; ?></p>

<table border="1" cellpadding="8">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Current Role</th>
        <th>Change Role</th>
    </tr>

    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['role_name']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                    <select name="role_id">
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id']; ?>"
                                <?php if ($role['id'] == $user['role_id']) echo 'selected'; ?>>
                                <?php echo $role['role_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="dashboard.php">Back to dashboard</a>