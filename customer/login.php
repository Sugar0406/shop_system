<?php
    header("Content-Type: text/html");

    // 前端已添加required屬性
    $account = $_POST['user_account'];
    $password = $_POST['user_password'];


    // password_hash() 生成的哈希值會包含「鹽」（salt）。鹽是隨機生成的，會使相同的輸入每次產生不同的哈希結果
    // 驗證密碼時使用 password_verify() : password_verify($user_password, $stored_hash)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT); 

 ?>


