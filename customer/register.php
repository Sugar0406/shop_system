<?php
    // database connect


    header("Content-Type: text/html");

// 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊

    // 唯一 ID生成
    $user_id = bin2hex(random_bytes(16));

    // 接收表單
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
// 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 處理註冊資訊 




// 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 
    $photo_targetDir = "./customer_img/";

    $fileTmpPath = $_FILES['user_photo']['tmp_name'];
    $fileName = $_FILES['user_photo']['name'];
    $fileSize = $_FILES['user_photo']['size'];
    $fileType = $_FILES['user_photo']['type'];

    // 生成唯一圖片檔名
    $fileNameCmps = pathinfo($fileName);
    $fileExtension = strtolower($fileNameCmps['extension']);
    
    $newFileName = uniqid('', true);  // 生成唯一ID
    $newFileName = preg_replace_callback('/[^\w\-]/', function($matches) {
        // 生成一个随机字母或数字，避免特殊字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return $chars[rand(0, strlen($chars) - 1)];  // 随机选择一个字符
    }, $newFileName);

    $newFileName = $newFileName . '.' . $fileExtension;  // 添加文件扩展名

    


    // 允許上傳的檔案類型
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];


    if (in_array($fileExtension, $allowedExtensions)) {
        
        // 移動圖片到目標資料夾
        $customer_photo_destination = $photo_targetDir . $newFileName;

        // alert("...") 網頁彈窗警告
        // window.location.href = 'http:...' 輸出彈窗後 回到指定網頁;

        if (move_uploaded_file($fileTmpPath, $customer_photo_destination)) {
            
        } else {
            echo "<script>alert('Failed to move the file to the destination folder. Please check the permissions.'); window.location.href = 'http://shop_system.com/customer/register.html'; </script> ";
        }
    } 
    else {
        echo "<script>
                alert('Unsupported photo file type. Only the following upload types are allowed: " . implode(", ", $allowedExtensions) . "'); 
                window.location.href = 'http://shop_system.com/customer/register.html'; 
            </script>";
    } 

// 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 處理上傳圖片 



// sql_injection_防護 sql_injection_防護 sql_injection_防護 sql_injection_防護 sql_injection_防護 sql_injection_防護 sql_injection_防護 

// sql_injection_防護 sql_injection_防護 sql_injection_防護 sql_injection_防護 sql_injection_防護 sql_injection_防護 sql_injection_防護 



// 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 


// sql 語句
$sql = "INSERT INTO users (USER_ID, USER_NAME, EMAIL, HASH_PASSWORD, USER_PICTURE, FULL_NAME, ADDRESS, PHONE) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

include "../database_connect.php";
// 準備語句, 返回一個預處理語句對象（$stmt）
// SQL 語句中的佔位符 ? 就會被留空，等待後續綁定參數
$stmt = $conn->prepare($sql);

// 依照$sql的順序綁定參數 : 字串符->s 整數->i 符點數->d
$stmt->bind_param("ssssssss", $user_id, $username, $account, $hashed_password, $newFileName, $fullname, $address, $phone);


if ($stmt->execute()) {
    // 成功後跳轉並顯示成功訊息
    echo "<script>
            alert('Successful registration!\\nBack to Login page');
            window.location.href = 'http://shop_system.com/customer/login.html';
          </script>";
} else {

    unlink($customer_photo_destination);  // 刪除已上傳的用戶照片檔案

    // 失敗後跳轉並顯示錯誤訊息
    echo "<script>
            alert('Registration failed!\\nERROR: " . addslashes($stmt->error) . "');
            window.location.href = 'http://shop_system.com/customer/register.html';
          </script>";
}


// 關閉語句和資料庫連線
$stmt->close();
$conn->close();


// 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 連接mysql 

?>