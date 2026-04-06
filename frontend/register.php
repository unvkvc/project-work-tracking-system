<?php
require '../backend/db.php';

session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($first) || empty($last) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password, role_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$first, $last, $email, $hashed, 3]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        header("Location: dashboard.php");
        exit;
    }
}
?>

<form method="POST">
    <input name="first_name" placeholder="First name"><br>
    <input name="last_name" placeholder="Last name"><br>
    <input name="email" placeholder="Email"><br>
    <input name="password" type="password" placeholder="Password"><br>
    <button type="submit">Register</button>
</form>

<p><?php echo $message; ?></p>