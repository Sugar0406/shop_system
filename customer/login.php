<?php
    header("Content-Type: text/html");

    // 前端已添加required屬性
    $email = $_POST['user_account'];
    $user_input_password = $_POST['user_password'];


    // password_hash() 生成的哈希值會包含「鹽」（salt）。鹽是隨機生成的，會使相同的輸入每次產生不同的哈希結果
    // 驗證密碼時使用 password_verify() : password_verify($user_password, $stored_hash), $user_password必須是明文
    // $user_input_hashed_password = password_hash($user_input_password, PASSWORD_BCRYPT);
    
    
    // 確認登入資訊
    include "../database_connect.php";
    $check_email_sql = "SELECT * FROM users WHERE EMAIL = ?";
    $check_email_stmt = $conn->prepare($check_email_sql);
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $check_email_result = $check_email_stmt->get_result();
    if ($check_email_result->num_rows <= 0){
        echo "<script> alert('This account has not been registered');
            window.location.href = 'http://shop_system.com/customer/login.html';
            </script>";
        $check_email_stmt->close();
    }
    else{
        $check_email_stmt->close();

        // compare password
        $compare_password_sql = "SELECT * FROM users WHERE EMAIL = ?";
        $compare_password_stmt = $conn->prepare($compare_password_sql);
        $compare_password_stmt->bind_param("s", $email);
        $compare_password_stmt->execute();
        $compare_password_result = $compare_password_stmt->get_result();

        $row = $compare_password_result->fetch_assoc();

        $user_id_from_db = $row['USER_ID'];
        $user_name_from_db = $row['USER_NAME'];
        $email_from_db = $row['EMAIL'];
        $hash_password_from_db = $row['HASH_PASSWORD'];
        $user_picture_from_db = $row['USER_PICTURE'];
        $full_name_from_db = $row['FULL_NAME'];
        $address_from_db = $row['ADDRESS'];
        $phone_from_db = $row['PHONE'];


        if (password_verify($user_input_password, $hash_password_from_db)){
            
            $compare_password_stmt->close();
            $conn->close();
            
            session_start();
            $_SESSION["USER_ID"] = $user_id_from_db;
            $_SESSION["USER_NAME"] = $user_name_from_db;
            $_SESSION["EMAIL"] = $email_from_db;
            $_SESSION["USER_PICTURE"] = $user_picture_from_db;
            $_SESSION["FULL_NAME"] = $full_name_from_db;
            $_SESSION["ADDRESS"] = $address_from_db;
            $_SESSION["PHONE"] = $phone_from_db;
            header("Location: ../main.php");
            exit; // 確保後續CODE不會執行

        }
        else{
            $compare_password_stmt->close();
            $conn->close();

            echo "<script> alert('Incorrect password!');
                window.location.href = 'http://shop_system.com/customer/login.html';
                </script>";
        }
    }
?>

