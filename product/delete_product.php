<?php

    include "../database_connect.php";


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



    $delete_product_sql = "DELETE FROM products WHERE PRODUCT_ID = ?;";
    $delete_product_stmt = $conn->prepare($delete_product_sql);
    $delete_product_stmt->bind_param("s", $_GET['product_id']);
    
    if($delete_product_stmt->execute()){
        //成功刪除資料表的資料，刪除系統中的照片
        $folderToDelete = '../product_img/' . $_GET["product_id"] . "/";
        deleteFolder($folderToDelete);

        echo "<script>
            alert('Delete! product successfully');
            window.location.href = 'http://shop_system.com/user/user_info.php#my_product';
        </script>";
    }
    else{
        echo "<script>
            alert('Delete! product failed! Please try again later');
            window.location.href = 'http://shop_system.com/user/user_info.php#my_product';
        </script>";
    }



?>