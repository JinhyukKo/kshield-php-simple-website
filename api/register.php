<?php
session_start();



// POST 데이터 가져오기
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password'] ?? '';
// 로그인 검증
if(isset($username) && $password === $password_confirm){
    $_SESSION['username'] = $username;
    header("Location: /index.php");
    exit;
}else{
    $_SESSION['error'] = "아이디 또는 비밀번호가 틀렸습니다.";
    header("Location: /login_form.php");
    exit;
}
