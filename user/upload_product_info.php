<?php

    // seller_id 就是商品建立者的 user_id
    session_start();
    $user_id = $_SESSION['USER_ID'];


    // 商品基本資訊
    // 生成商品ID
    $product_id = bin2hex(random_bytes(16));
    
    $product_name = $_POST["product_name"];
    $price = $_POST["price"];
    $in_stock = $_POST["in_stock"];
    $description = $_POST["description"];
    $category = $_POST["category"];

    // 驗證庫存和價格均需要大於0
    if ($in_stock <= 0) {
        echo "<script>
            alert('Stock quantity must be greater than 0');
            window.location.href = 'http://shop_system.com/user/add_product.php';
        </script>";
    }
    if ($price <= 0) {
        echo "<script>
            alert('Price must be greater than 0');
            window.location.href = 'http://shop_system.com/user/add_product.php';
        </script>";
    }

    // 測試
    // echo "product_id=" . $product_id . "<br>";
    // echo "&product_name=". $product_name . "<br>";
    // echo "&price=". $price . "<br>";
    // echo "&in_stock=". $in_stock . "<br>";
    // echo "&description=". $description . "<br>";
    // echo "&category=". $category . "<br>";





    // 所有上傳圖片資訊
    $fileTmpPath_array = $_FILES['product_image']['tmp_name'];
    $fileName_array = $_FILES['product_image']['name'];
    $fileSize_array = $_FILES['product_image']['size'];
    $fileType_array = $_FILES['product_image']['type'];


    // // 測試
    // for ($i = 0; $i < count($fileTmpPath_array); $i++) {
    //     // 獲取單個檔案的相關資料
    //     $fileTmpPath = $fileTmpPath_array[$i];
    //     $fileName = $fileName_array[$i];
    //     $fileSize = $fileSize_array[$i];
    //     $fileType = pathinfo($fileName)['extension'];
    
    //     echo "fileTmpPath : " . $fileTmpPath . "<br>";
    //     echo "fileName : " . $fileName . "<br>";
    //     echo "fileSize : " . $fileSize . "<br>";
    //     echo "fileType : " . $fileType . "<br>";
    // }




    // 允許的檔案類型
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    // 假設 $_FILES['product_image'] 這是多個檔案的輸入欄位
    $fileTmpPath_array = $_FILES['product_image']['tmp_name'];
    $fileName_array = $_FILES['product_image']['name'];
    
    for ($i = 0; $i < count($fileTmpPath_array); $i++) {
        // 獲取檔案副檔名
        $fileName = $fileName_array[$i];
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
    
        // 檢查檔案類型
        if (in_array(strtolower($fileType), $allowedExtensions)) {
            // 生成新的檔案名稱 (以數字ID命名)
            $newFileName = $i . "." . $fileType;
    
            // 更新檔案名稱為新名稱
            $fileName_array[$i] = $newFileName;
        } else {
            echo "<script>
                alert('Some file type not allowed! Please choose jpg, jpeg, png, or gif.');
                window.location.href = 'http://shop_system.com/user/add_product.php';
            </script>";
            exit();
        }
    }
    
    // 建立商品圖片資料夾
    $photo_targetDir = "../product_img/" . $product_id . "/";
    if (!file_exists($photo_targetDir)) {
        mkdir($photo_targetDir, 0777, true);
    } else {
        // product ID 重複
        echo "<script>
            alert('There is something error! Please try again later');
            window.location.href = 'http://shop_system.com/user/add_product.php';
        </script>";
    }
    
    // 上傳圖片
    for ($i = 0; $i < count($fileTmpPath_array); $i++) {
        // 檢查檔案是否成功上傳
        if (move_uploaded_file($fileTmpPath_array[$i], $photo_targetDir . $fileName_array[$i])) {
        
        } else {
            echo "Error uploading file: " . $fileName_array[$i];
        }
    }

    // 圖片檔名打包為JSON上傳資料庫
    $product_photos_json = json_encode($fileName_array);
    // $photo = json_decode($photo_json);
    // echo $photo[0]; // fileName.jpg


    //連接資料庫
    include "../database_connect.php";

    // 新增檔案失敗時 刪除資料夾
    function deleteFolder($folderPath) {
        // 確保資料夾存在
        if (!is_dir($folderPath)) {
            echo "The specified directory does not exist.";
            return false;
        }
    
        // 取得資料夾內的所有檔案與子目錄
        $files = array_diff(scandir($folderPath), array('.', '..'));
    
        // 遍歷資料夾內的所有檔案
        foreach ($files as $file) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
    
            // 若是目錄，則遞迴刪除
            if (is_dir($filePath)) {
                deleteFolder($filePath);
            } else {
                unlink($filePath); // 刪除檔案
            }
        }
    
        // 刪除資料夾本身
        return rmdir($folderPath);
    }


    // 資料表中 SOLD預設為0,  CREATE_AT預設為CURRENT_TIMESTAMP, UPDATE_AT預設為CURRENT_TIMESTAMP
    $upload_product_sql = "
        INSERT INTO products (
            PRODUCT_ID,
            PRODUCT_NAME,
            CATEGORY,
            SELLER_ID,
            DESCRIPTION,
            PRODUCT_IMAGE,
            IN_STOCK,
            PRICE
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?);
    ";

    $upload_product_stmt = $conn->prepare($upload_product_sql);
    $upload_product_stmt -> bind_param("ssssssii", $product_id, $product_name, $category, $user_id, $description, $product_photos_json, $in_stock, $price);
    
    if($upload_product_stmt -> execute()){
        // 新增成功
        $conn->close();
        echo "<script>
            alert('Product created successfully!');
            window.location.href = 'http://shop_system.com/user/user_info.php#my_product';
        </script>";
    }
    else{
        // 新增失敗
        // 獲取 MySQL 錯誤碼
        $error_code = $upload_product_stmt->errno;
        $error_msg = addslashes($upload_product_stmt->error);

        // 1062為UNIQUE error 代號
        if ($error_code == 1062) {
            // 檢查錯誤訊息是否包含 USER_NAME 或 EMAIL
            if (strpos($error_msg, 'PRODUCT_NAME') !== false) {
                $error_message = "Product name already exists!";
            }
        }
        else {
            $error_message = "Add product failed!\\nERROR: $error_msg";
        }
        $conn->close();

        // 上傳失敗 刪除圖片資料夾
        deleteFolder($photo_targetDir);

        echo "<script>
            alert('$error_message, Please try again later.');
            window.location.href = 'http://shop_system.com/user/add_product.php#my_product';
        </script>";
        
    }




?>