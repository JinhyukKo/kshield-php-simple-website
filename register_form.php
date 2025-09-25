<?php session_start();
if(isset($_SESSION["username"])){
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h2>Register Page</h2>
    <?php if(isset($_SESSION['error'])): ?>
        <p style="color:red"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <form action="./service/login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br><br>
        <label>Password:</label>
        <input type="password" name="password" required><br><br>
        <label>Password Confirm:</label>
        <input type="password" name="password_confirm" required><br><br>
        <button type="submit">Register</button>
    </form>
    <a href="login_form.php">Login</a>
</body>
</html>
