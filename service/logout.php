<?php
session_start();       // 세션 시작
session_unset();       // 세션 변수 모두 삭제
session_destroy();     // 세션 자체 삭제
setcookie(session_name(), '', time() - 3600); // 세션 쿠키 삭제
header('Location: ../login_form.php');

