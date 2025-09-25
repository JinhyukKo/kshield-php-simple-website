<?php
session_start();
// require 'db.php';


// POST 데이터 가져오기
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password'] ?? '';

# 비번 일치확인
if($password !== $password_confirm){
    $_SESSION['error'] = "비밀번호가 일치하지 않습니다.";
    header("Location: register_form.php");
    exit;
}

$hash = password_hash ($password, PASSWORD_DEFAULT);

// try {
//     $stmt = $pdo->prepare("INSERT INTO user (username, password) VALUES (?, ?)");
//     $stmt->execute([$user_id, $hash, $name]);
//     $_SESSION['success'] = "회원가입 완료!";
//     header("Location: login_form.php");
//     exit;
// } catch (PDOException $e) {
//     $_SESSION['error'] = "회원가입 실패: " . $e->getMessage();
//     header("Location: register_form.php");
//     exit;
// }