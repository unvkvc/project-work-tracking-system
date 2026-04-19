<?php
require 'C:\xampp\htdocs\project-work-tracking\backend/db.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (!$user) {
            $error = "Email does not exist.";
        } elseif (!password_verify($password, $user['password'])) {
            $error = "Wrong password.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 350px;">
        
        <h3 class="text-center mb-4">Login</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" placeholder="Enter email">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control" placeholder="Enter password">
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Login
            </button>
        </form>

    </div>
</div>

</body>
</html>