<?php

    // seller_id 就是商品建立者的 user_id
    session_start();
    $user_id = $_SESSION['USER_ID'];


    // 商品基本資訊
    // 取得商品ID
    $product_id = $_GET['product_id'];
    
    $product_name = $_POST["product_name"];
    $price = $_POST["price"];
    $in_stock = $_POST["in_stock"];
    $description = $_POST["description"];
    $category = $_POST["category"];

    // 驗證庫存和價格均需要大於0
    if ($in_stock <= 0) {
        echo "<script>
            alert('Stock quantity must be greater than 0');
            window.location.href = 'http://shop_system.com/product/edit_product_info.php?product_id='" . $product_id .
        "';</script>";
    }
    if ($price <= 0) {
        echo "<script>
            alert('Price must be greater than 0');
            window.location.href = 'http://shop_system.com/product/edit_product_info.php?product_id='" . $product_id .
        "';</script>";
    }

    // // 測試
    // echo "product_id=" . $product_id . "<br>";
    // echo "&product_name=". $product_name . "<br>";
    // echo "&price=". $price . "<br>";
    // echo "&in_stock=". $in_stock . "<br>";
    // echo "&description=". $description . "<br>";
    // echo "&category=". $category . "<br>";

    

    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'][0] == UPLOAD_ERR_NO_FILE){
        //若沒上傳新照片則不更動
    }
    else{
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
                    window.location.href = 'http://shop_system.com/product/edit_product_info.php?product_id='" . $product_id .
                "';</script>";
                exit();
            }
        }
        
        // 刪除所有原先資料夾的照片
        $photo_targetDir = "../product_img/" . $product_id . "/";
        function deleteFilesInDirectory($targetDir) {
            // 確保資料夾存在
            if (!is_dir($targetDir)) {
                // echo "The specified directory does not exist.";
                return;
            }
        
            // 開啟資料夾
            $files = array_diff(scandir($targetDir), array('.', '..')); // 排除 '.' 和 '..' 資料夾
        
            foreach ($files as $file) {
                $filePath = $targetDir . DIRECTORY_SEPARATOR . $file;
        
                // 檢查是否為檔案並刪除
                if (is_file($filePath)) {
                    unlink($filePath);
                    // echo "Deleted file: $file\n";
                }
            }
        }
        
        // 呼叫函式來刪除指定資料夾中的所有檔案
        deleteFilesInDirectory($photo_targetDir);
        
        
        // 上傳新圖片
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
    }





    //連接資料庫
    include "../database_connect.php";


    // 資料表中 SOLD預設為0,  CREATE_AT預設為CURRENT_TIMESTAMP, UPDATE_AT預設為CURRENT_TIMESTAMP
    $update_product_sql = "
        UPDATE products
        SET
            PRODUCT_NAME = ?,
            CATEGORY = ?,
            DESCRIPTION = ?,
            PRODUCT_IMAGE = ?,
            IN_STOCK = ?,
            PRICE = ?
        WHERE PRODUCT_ID = ?;
    ";

    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'][0] == UPLOAD_ERR_NO_FILE){
        //無更新照片
        $update_product_sql = "
            UPDATE products
            SET
                PRODUCT_NAME = ?,
                CATEGORY = ?,
                DESCRIPTION = ?,
                IN_STOCK = ?,
                PRICE = ?
            WHERE PRODUCT_ID = ?;
        ";

        $update_product_stmt = $conn->prepare($update_product_sql);
        $update_product_stmt -> bind_param("sssiis", $product_name, $category, $description, $in_stock, $price, $product_id);

    }
    else{
        //更新照片
        $update_product_sql = "
            UPDATE products
            SET
                PRODUCT_NAME = ?,
                CATEGORY = ?,
                DESCRIPTION = ?,
                PRODUCT_IMAGE = ?,
                IN_STOCK = ?,
                PRICE = ?
            WHERE PRODUCT_ID = ?;
        ";

        $update_product_stmt = $conn->prepare($update_product_sql);
        $update_product_stmt -> bind_param("ssssiis", $product_name, $category, $description, $product_photos_json, $in_stock, $price, $product_id);

    }

    
    if($update_product_stmt -> execute()){
        $conn->close();
        echo "<script>
            alert('Profile update successfully!');
            window.location.href = 'http://shop_system.com/user/user_info.php#my_product';
        </script>";
    }
    else{
        $conn->close();
        echo "<script>
            alert('Error update product! Please try again later.');
            window.location.href = 'http://shop_system.com/user/user_info.php#my_product';
        </script>";
    };


?>