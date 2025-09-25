<?php
session_start();
// require 'db.php';


$users = [
    "admin" => "1234",
    "user" => "abcd"
];

// POST 데이터 가져오기
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';



$stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if($user && password_verify($password, $user['password'])){
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_id'] = $user['id'];
    header("Location: /index.php");
    exit;
} else {
    $_SESSION['error'] = "아이디 또는 비밀번호가 틀렸습니다.";
    header("Location: /login_form.php");
    exit;
}


// // 로그인 검증
// if(isset($users[$username]) && $users[$username] === $password){
//     $_SESSION['username'] = $username;
//     header("Location: /index.php");
//     exit;
// }else{
//     $_SESSION['error'] = "아이디 또는 비밀번호가 틀렸습니다.";
//     header("Location: /login_form.php");
//     exit;
// }
