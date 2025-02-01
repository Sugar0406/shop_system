<?php
    include "../database_connect.php";
    session_start();
    
    $user_id = $_SESSION['USER_ID'];
    $old_photo_name = $_SESSION['USER_PICTURE'];
    $old_photo_path = "../customer/customer_img/" . $old_photo_name;



    $username = $_POST['user_name'];
    $account = $_POST['user_account']; // account is user email
    $fullname = $_POST['user_fullname'];
    $address = $_POST['user_address'];
    $phone = $_POST['user_phone'];

    // 檢查是否有上傳檔案
    if (isset($_FILES['user_photo']) && $_FILES['user_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['user_photo']['tmp_name'];
        $fileName = $_FILES['user_photo']['name'];
        $fileSize = $_FILES['user_photo']['size'];
        $fileType = $_FILES['user_photo']['type'];

        $fileNameCmps = pathinfo($fileName);
        $fileExtension = strtolower($fileNameCmps['extension']);

        // 允許上傳的檔案類型
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // 須為允許的檔案才可以上傳，才建立新檔名
        if (in_array($fileExtension, $allowedExtensions)){
            // 生成唯一圖片檔名
            
            $newFileName = uniqid('', true);  // 生成唯一ID
            $newFileName = preg_replace_callback('/[^\w\-]/', function($matches) {
                // 生成一个随机字母或数字，避免特殊字符
                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                return $chars[rand(0, strlen($chars) - 1)];  // 随机选择一个字符
            }, $newFileName);
        
            $newFileName = $newFileName . '.' . $fileExtension;  // 添加文件扩展名
        }
        else{
            echo "<script>
                alert('Unsupported photo file type. Only the following upload types are allowed: " . implode(", ", $allowedExtensions) . "');
                window.location.href = 'http://shop_system.com/user/user_info.php'; 
            </script>";

            exit();
        }



        
    } 
    else {
        $fileTmpPath = null;
        $fileName = null;
        $fileSize = null;
        $fileType = null;
    }

    // verify form post content

    // echo "$username<br>";
    // echo "$account<br>";
    // echo "$fullname<br>";
    // echo "$address<br>";
    // echo "$phone<br>";
    // echo isset($fileTmpPath) ? $fileTmpPath . "<br>": 'null' . "<br>";
    // echo isset($fileName) ? $fileName . "<br>": 'null' . "<br>";
    // echo isset($fileSize) ? $fileSize . "<br>": 'null' . "<br>";
    // echo isset($fileType) ? $fileType . "<br>": 'null' . "<br>";

    // 依是否有上傳圖片 分別設置sql語句和stml
    if( $fileSize === null ){
        $update_user_info_sql = "UPDATE users SET USER_NAME = ?, EMAIL = ?, FULL_NAME = ?, ADDRESS = ?, PHONE = ? WHERE USER_ID = ?";
    }    
    else{
        $update_user_info_sql = "UPDATE users SET USER_NAME = ?, EMAIL = ?, USER_PICTURE = ?, FULL_NAME = ?, ADDRESS = ?, PHONE = ? WHERE USER_ID = ?";
    }

    $update_stmt = $conn->prepare($update_user_info_sql);
    
    if( $fileSize === null ){
        $update_stmt -> bind_param("ssssss", $username, $account, $fullname, $address, $phone, $user_id);
    }    
    else{
        $update_stmt -> bind_param("sssssss", $username, $account, $newFileName, $fullname, $address, $phone, $user_id);
    }


    if ($update_stmt->execute()) {


        //更新session
        $_SESSION["USER_NAME"] = $username;
        $_SESSION["EMAIL"] = $account;

        if( $fileSize === null ){

        }    
        else{
            // 如果照片被更新且新增成功，先上傳新照片，然後刪除已上傳的用戶舊照片檔案，最終更新session
            $newFilePath = "../customer/customer_img/" . $newFileName;
            move_uploaded_file($fileTmpPath, $newFilePath);

            $old_photo_path = "../customer/customer_img/" . $old_photo_name;
            unlink($old_photo_path);  
            
            $_SESSION["USER_PICTURE"] = $newFileName;
        }

        $_SESSION["FULL_NAME"] = $fullname;
        $_SESSION["ADDRESS"] = $address;
        $_SESSION["PHONE"] = $phone;


        // 成功後跳轉並顯示成功訊息
        echo "<script>
            alert('Profile Updated successfully!');
            window.location.href = 'http://shop_system.com/user/user_info.php';
        </script>";
    } else {
        echo "<script>
            alert('Update failed. Please try again.');
            window.location.href = 'http://shop_system.com/user/user_info.php';
        </script>";
    }

    
    
?>