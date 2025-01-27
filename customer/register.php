<?php
    header("Content-Type: text/html");

    // 前端已添加required屬性
    $username = $_POST['user_name'];
    $account = $_POST['user_account']; // account is user email
    $password = $_POST['user_password'];
    $fullname = $_POST['user_fullname'];
    $address = $_POST['user_address'];
    $phone = $_POST['user_phone'];

    // password_hash() 生成的哈希值會包含「鹽」（salt）。鹽是隨機生成的，會使相同的輸入每次產生不同的哈希結果
    // 驗證密碼時使用 password_verify() : password_verify($user_password, $stored_hash)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    echo "username :  $username </br>";
    echo "account : $account </br>";
    echo "hash password : $hashed_password </br>";
    echo "fullname : $fullname </br>";
    echo "address : $address </br>";
    echo "phone : $phone </br>";
 ?>