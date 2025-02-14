<?php

    include "./database_connect.php";


    $product = json_decode($_POST["product"], true);

    // 先查看買賣雙方訂單是否有存在於購物車
    $search_shopping_cart_sql = "
                                    SELECT * 
                                    FROM ORDERS 
                                    WHERE BUYER_ID = ? AND SELLER_ID = ? AND STATUS = 'IN_CART'
                                ";

    $search_shopping_cart_stmt = $conn -> prepare($search_shopping_cart_sql);
    $search_shopping_cart_stmt -> bind_param("ss", $product["buyerId"], $product["sellerId"]);
    $search_shopping_cart_stmt -> execute();
    $search_shopping_cart_result = $search_shopping_cart_stmt -> get_result();


    if ($search_shopping_cart_result -> num_rows == 1) {
        // 雙方買賣已經在購物車中
        $search_shopping_cart_row = $search_shopping_cart_result -> fetch_assoc();
        $old_product_list = json_decode( $search_shopping_cart_row["ORDER_PRODUCTS"], true );

        // echo $old_product_list[0]["productId"];

        // 先檢查該商品是否有在購物車內
        $product_exists_in_cart = false;
        foreach ($old_product_list as &$old_product) {
            
            if ($old_product["productId"] == $product["productId"]) { // 使用引用(&)，這樣可以直接修改陣列中的元素


                //檢查購買限制
                $check_purchase_limit_sql = "SELECT IN_STOCK FROM products WHERE PRODUCT_ID = ?";
                $check_purchase_limit_stmt = $conn -> prepare($check_purchase_limit_sql);
                $check_purchase_limit_stmt -> bind_param("s", $old_product["productId"]);
                $check_purchase_limit_stmt -> execute();
                $check_purchase_limit_result = $check_purchase_limit_stmt->get_result();
                $check_purchase_limit_row = $check_purchase_limit_result -> fetch_assoc();
                $purchase_limit = $check_purchase_limit_row["IN_STOCK"];

                if($old_product["quantity"] + (int)$product["quantity"] > $purchase_limit){
                    echo "<script>
                        alert('Exceeded purchase limit');
                        window.history.back();
                    </script>";
                    exit(); 
                }


                $old_product["quantity"] = $old_product["quantity"] + $product["quantity"];
                $product_exists_in_cart = true;
            }
        }

        // 該商品沒有在購物車內，新物品JSON加入原本的product_list
        if (!$product_exists_in_cart) {

            // 新物品JSON
            $product_detail = [
                "productId" => $product["productId"],
                "quantity" => (int)$product["quantity"]
            ];
            array_push($old_product_list, $product_detail);
        }


        // 將修改後的 product_list 轉換為 JSON 字串
        $new_product_list_json = json_encode($old_product_list);

        // 將修改後的 product_list 更新資料表
        $update_order_sql = "  UPDATE orders SET ORDER_PRODUCTS=? WHERE ORDER_ID=?";
        $update_order_stmt = $conn -> prepare($update_order_sql);
        $update_order_stmt -> bind_param("ss", $new_product_list_json, $search_shopping_cart_row["ORDER_ID"]);
        $update_order_stmt -> execute();
        echo "<script>
                alert('product add to shopping cart successfully');
                window.history.back();
        </script>";


    } 
    else if($search_shopping_cart_result -> num_rows == 0){
        // 雙方的買賣未在購物車中
        $order_id = bin2hex(random_bytes(16));

        $product_detail = [
            "productId" => $product["productId"],
            "quantity" => (int) $product["quantity"]
        ];

        
        // 初始化商品列表陣列
        $product_list = [];

        // 將商品詳細資料物件加入商品列表
        array_push($product_list, $product_detail);

        // 將商品列表轉換為 JSON 字串
        $product_list_json = json_encode($product_list);



        $new_order_sql = "
                            INSERT INTO orders (ORDER_ID, BUYER_ID, SELLER_ID, ORDER_PRODUCTS, STATUS) 
                            VALUES (?,?,?,?, 'IN_CART')
                        ";
        $new_order_stmt = $conn -> prepare($new_order_sql);
        $new_order_stmt -> bind_param("ssss", $order_id, $product["buyerId"], $product["sellerId"], $product_list_json );

        $new_order_stmt -> execute();
        echo "<script>
                alert('product add to shopping cart successfully');
                window.history.back();
        </script>";
    }
    else {
        echo "<script>
            alert('Something went wrong');
            window.history.back();
        </script>";
    }


?>