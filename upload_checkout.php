<?php

    include "./database_connect.php";
    
    $total_price = isset($_POST["total_price"])  ? $_POST["total_price"] : null;
    $order_id = isset($_POST["order_id"])      ? $_POST["order_id"] : null;
    $order_product = isset($_POST["order_product"]) ? json_decode($_POST["order_product"], true) : null;
    $pay_method = isset($_POST["payment_method"]) ? $_POST["payment_method"] : null;
    $buyer_fullname = isset($_POST["fullname"]) ? $_POST["fullname"] : null;
    $buyer_address = isset($_POST["address"]) ? $_POST["address"] : null;
    $buyer_phone = isset($_POST["phone"]) ? $_POST["phone"] : null;
    $order_status = "SUBMITTED";
    $current_datetime = (new DateTime())->format('Y-m-d H:i:s');

    // echo all test
    // echo "<br>Total Price: $total_price";
    // echo "<br>Order ID: $order_id";
    // echo "<br>Payment Method: $pay_method";
    // echo "<br>Full Name: $buyer_fullname";
    // echo "<br>Address: $buyer_address";
    // echo "<br>Phone: $buyer_phone";
    // echo $order_product[0]["product_name"];

    // 更新該筆訂單
    $update_order_sql = "UPDATE orders 
                        SET ORDER_TOTAL=?, PAY_METHOD=?, BUYER_FULLNAME=?, BUYER_PHONE=?, BUYER_ADDRESS=?, STATUS=?, CREATED_AT=?
                        WHERE ORDER_ID=?";


    $update_order_stmt = $conn->prepare($update_order_sql);
    $update_order_stmt->bind_param("isssssss", $total_price, $pay_method, $buyer_fullname, $buyer_phone, $buyer_address, $order_status, $current_datetime, $order_id);
    if( $update_order_stmt->execute() ){

        
        foreach($order_product as $product){
            //結帳成功將商品庫存扣除 並增加銷售量
            $update_stock_sql = "UPDATE products SET IN_STOCK=IN_STOCK-?, SOLD=SOLD+? WHERE PRODUCT_ID=?";
            $update_stock_stmt = $conn->prepare($update_stock_sql);
            $update_stock_stmt->bind_param("iis", $product["quantity"], $product["quantity"], $product["productId"]);
            $update_stock_stmt->execute();
            $update_stock_stmt->close();
        }
    }
    else{
        echo "<script>
                alert('Something went wrong');
                window.location.href = 'http://shop_system.com/shopping_cart.php';
            </script>";
    }

    echo "<script>
        alert('Your order has been successfully placed! Thank you for your purchase.');
        window.location.href = './user/user_info.php#my_purchase';
    </script>";


?>