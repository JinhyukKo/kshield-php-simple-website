<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login_form.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    Hello <?php echo $_SESSION['username'] ?>
    <a href="/api/logout.php" > Logout </a>
</body>
</html>
