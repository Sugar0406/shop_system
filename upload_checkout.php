<?php

    include "./database_connect.php";
    
    $total_price = isset($_POST["total_price"])  ? $_POST["total_price"] : null;
    $order_id = isset($_POST["order_id"])      ? $_POST["order_id"] : null;
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


    // 更新該筆訂單
    $update_order_sql = "UPDATE orders 
                        SET ORDER_TOTAL=?, PAY_METHOD=?, BUYER_FULLNAME=?, BUYER_PHONE=?, BUYER_ADDRESS=?, CREATED_AT=?
                        WHERE ORDER_ID=?";


    $update_order_stmt = $conn->prepare($update_order_sql);
    $update_order_stmt->bind_param("issssss", $total_price, $pay_method, $buyer_fullname, $buyer_phone, $buyer_address, $current_datetime, $order_id);
    $update_order_stmt->execute();

    echo "<script>
        alert('Your order has been successfully placed! Thank you for your purchase.');
        window.location.href = './user/user_info.php#my_purchase';
    </script>";


?>