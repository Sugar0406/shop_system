<?php

    include "./database_connect.php";

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 8; // 每頁顯示8個商品 (4*2 grid)
    $offset = ($page - 1) * $limit; // 偏移量

    // 分類預設為所有分類
    $product_category = isset($_GET['selected_category']) ? $_GET['selected_category'] : "All category";
    $product_keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
    if ($product_keyword != null){ $product_keyword = "%" . $product_keyword . "%"; };

    // 計算沒種情況的商品數
    $product_count = 0;




    // 未採用關鍵字 未採用關鍵字 未採用關鍵字 未採用關鍵字 未採用關鍵字 未採用關鍵字 未採用關鍵字 未採用關鍵字
    if($product_keyword == null){ 
        // 未登入時查詢所有商品
        if ($user_id == null){

            // 無指定分類
            if ($product_category == "All category") {

                // 計算商品數
                $product_count_sql = "
                    SELECT COUNT(*) 
                    FROM products p
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE IN_STOCK > 0;
                ";
                $product_count_stmt = $conn->prepare($product_count_sql);
                $product_count_stmt->execute();
                $product_count_result = $product_count_stmt->get_result();
                $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數


                // 查詢商品資訊
                $get_product_sql = "
                    SELECT p.*, u.USER_NAME 
                    FROM products p 
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE p.IN_STOCK > 0
                    ORDER BY p.UPDATE_AT DESC
                    LIMIT ? OFFSET ?
                ";
                $get_product_stmt = $conn->prepare($get_product_sql);
                $get_product_stmt->bind_param("ii", $limit, $offset); 
            } 
            // 有指定分類
            else {
                // 計算商品數
                $product_count_sql = "
                    SELECT COUNT(*) 
                    FROM products p
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE IN_STOCK > 0 AND p.CATEGORY = ?;
                ";
                $product_count_stmt = $conn->prepare($product_count_sql);
                $product_count_stmt->bind_param("s", $product_category); 
                $product_count_stmt->execute();
                $product_count_result = $product_count_stmt->get_result();
                $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數
                
                // 查詢商品
                $get_product_sql = "
                    SELECT p.*, u.USER_NAME 
                    FROM products p 
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE p.IN_STOCK > 0  AND p.CATEGORY = ?
                    ORDER BY p.UPDATE_AT DESC
                    LIMIT ? OFFSET ?
                ";
                $get_product_stmt = $conn->prepare($get_product_sql);
                $get_product_stmt->bind_param("sii", $product_category, $limit, $offset); 
            }

        }
        // 已登入時排除自己的商品
        else{

            // 無指定分類，並排除當前使用者的上架商品
            if ($product_category == "All category") {

                // 計算商品數
                $product_count_sql = "
                    SELECT COUNT(*) 
                    FROM products p
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE IN_STOCK > 0 AND p.SELLER_ID != ?;
                ";
                $product_count_stmt = $conn->prepare($product_count_sql);
                $product_count_stmt->bind_param("s", $user_id); 
                $product_count_stmt->execute();
                $product_count_result = $product_count_stmt->get_result();
                $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數


                // 查詢商品
                $get_product_sql = "
                    SELECT p.*, u.USER_NAME 
                    FROM products p 
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE p.IN_STOCK > 0 AND p.SELLER_ID != ?
                    ORDER BY p.UPDATE_AT DESC
                    LIMIT ? OFFSET ?
                ";
                $get_product_stmt = $conn->prepare($get_product_sql);
                $get_product_stmt->bind_param("sii", $user_id, $limit, $offset); 
            } 
            // 有指定分類，並排除當前使用者的上架商品
            else {

                // 計算商品數
                $product_count_sql = "
                    SELECT COUNT(*) 
                    FROM products p
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE IN_STOCK > 0 AND p.CATEGORY = ? AND p.SELLER_ID != ?;
                ";
                $product_count_stmt = $conn->prepare($product_count_sql);
                $product_count_stmt->bind_param("ss", $product_category, $user_id); 
                $product_count_stmt->execute();
                $product_count_result = $product_count_stmt->get_result();
                $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數

                // 查詢商品
                $get_product_sql = "
                    SELECT p.*, u.USER_NAME 
                    FROM products p 
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE p.IN_STOCK > 0  AND p.CATEGORY = ? AND p.SELLER_ID != ?
                    ORDER BY p.UPDATE_AT DESC
                    LIMIT ? OFFSET ?
                ";
                $get_product_stmt = $conn->prepare($get_product_sql);
                $get_product_stmt->bind_param("ssii", $product_category, $user_id, $limit, $offset); 
            }
        }
    }
    // 關鍵字查詢
    else{

        // 未登入時查詢所有商品
        if ($user_id == null){

            // 無指定分類
            if ($product_category == "All category") {

                // 計算商品數，加入關鍵字查詢 p.PRODUCT_NAME LIKE ?
                $product_count_sql = "
                    SELECT COUNT(*) 
                    FROM products p
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE IN_STOCK > 0 AND p.PRODUCT_NAME LIKE ? ; 
                ";
                $product_count_stmt = $conn->prepare($product_count_sql);
                $product_count_stmt -> bind_param("s", $product_keyword);
                $product_count_stmt->execute();
                $product_count_result = $product_count_stmt->get_result();
                $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數


                // 查詢商品資訊，加入關鍵字查詢 p.PRODUCT_NAME LIKE ?
                $get_product_sql = "
                    SELECT p.*, u.USER_NAME 
                    FROM products p 
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE p.IN_STOCK > 0 AND p.PRODUCT_NAME LIKE ?
                    ORDER BY p.UPDATE_AT DESC
                    LIMIT ? OFFSET ?
                ";
                $get_product_stmt = $conn->prepare($get_product_sql);
                $get_product_stmt->bind_param("sii", $product_keyword, $limit, $offset); 
            } 
            // 有指定分類
            else {
                // 計算商品數，加入關鍵字查詢 p.PRODUCT_NAME LIKE ?
                $product_count_sql = "
                    SELECT COUNT(*) 
                    FROM products p
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE IN_STOCK > 0 AND p.CATEGORY = ? AND p.PRODUCT_NAME LIKE ?;
                ";
                $product_count_stmt = $conn->prepare($product_count_sql);
                $product_count_stmt->bind_param("ss", $product_category, $product_keyword); 
                $product_count_stmt->execute();
                $product_count_result = $product_count_stmt->get_result();
                $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數
                
                // 查詢商品，加入關鍵字查詢 p.PRODUCT_NAME LIKE ?
                $get_product_sql = "
                    SELECT p.*, u.USER_NAME 
                    FROM products p 
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE p.IN_STOCK > 0  AND p.CATEGORY = ? AND p.PRODUCT_NAME LIKE ?
                    ORDER BY p.UPDATE_AT DESC
                    LIMIT ? OFFSET ?
                ";
                $get_product_stmt = $conn->prepare($get_product_sql);
                $get_product_stmt->bind_param("ssii", $product_category, $product_keyword, $limit, $offset); 
            }

        }
        // 已登入時排除自己的商品
        else{

            // 無指定分類，並排除當前使用者的上架商品
            if ($product_category == "All category") {

                // 計算商品數，加入關鍵字查詢 p.PRODUCT_NAME LIKE ?
                $product_count_sql = "
                    SELECT COUNT(*) 
                    FROM products p
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE IN_STOCK > 0 AND p.SELLER_ID != ? AND p.PRODUCT_NAME LIKE ?;
                ";
                $product_count_stmt = $conn->prepare($product_count_sql);
                $product_count_stmt->bind_param("ss", $user_id, $product_keyword); 
                $product_count_stmt->execute();
                $product_count_result = $product_count_stmt->get_result();
                $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數


                // 查詢商品，加入關鍵字查詢 p.PRODUCT_NAME LIKE ?
                $get_product_sql = "
                    SELECT p.*, u.USER_NAME 
                    FROM products p 
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE p.IN_STOCK > 0 AND p.SELLER_ID != ? AND p.PRODUCT_NAME LIKE ?
                    ORDER BY p.UPDATE_AT DESC
                    LIMIT ? OFFSET ?
                ";
                $get_product_stmt = $conn->prepare($get_product_sql);
                $get_product_stmt->bind_param("ssii", $user_id, $product_keyword, $limit, $offset); 
            } 
            // 有指定分類，並排除當前使用者的上架商品
            else {

                // 計算商品數，加入關鍵字查詢 p.PRODUCT_NAME LIKE ?
                $product_count_sql = "
                    SELECT COUNT(*) 
                    FROM products p
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE IN_STOCK > 0 AND p.CATEGORY = ? AND p.SELLER_ID != ? AND p.PRODUCT_NAME LIKE ?;
                ";
                $product_count_stmt = $conn->prepare($product_count_sql);
                $product_count_stmt->bind_param("sss", $product_category, $user_id, $product_keyword); 
                $product_count_stmt->execute();
                $product_count_result = $product_count_stmt->get_result();
                $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數

                // 查詢商品，加入關鍵字查詢 p.PRODUCT_NAME LIKE ?
                $get_product_sql = "
                    SELECT p.*, u.USER_NAME 
                    FROM products p 
                    LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                    WHERE p.IN_STOCK > 0  AND p.CATEGORY = ? AND p.SELLER_ID != ? AND p.PRODUCT_NAME LIKE ?
                    ORDER BY p.UPDATE_AT DESC
                    LIMIT ? OFFSET ?
                ";
                $get_product_stmt = $conn->prepare($get_product_sql);
                $get_product_stmt->bind_param("sssii", $product_category, $user_id, $product_keyword, $limit, $offset); 
            }
        }

    }






    //計算最大頁數
    //例如: 17個商品，頁數設定為1,2,3
    $max_pages = ceil($product_count / $limit);

    $get_product_stmt->execute();
    $get_product_result = $get_product_stmt->get_result();
    $products = [];
    //回傳結果不為空
    if($get_product_result){
        while ($row = $get_product_result->fetch_assoc()) {
            $products[] = $row;
        }
    }



    $get_product_stmt->close();
    $conn->close();


?>