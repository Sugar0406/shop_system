<?php

    include "../database_connect.php";

    $order_id = $_POST["order_id"];
    $order_status = "SHIPPED";

    // 使用者(賣家)寄出商品 更新訂單狀態
    $update_order_sql = "UPDATE orders SET STATUS=? WHERE ORDER_ID=?";
    $update_order_stmt = $conn->prepare($update_order_sql);
    $update_order_stmt->bind_param("ss", $order_status, $order_id);
    $update_order_stmt->execute();
    $update_order_stmt->close();

    echo "<script>
            alert('The system has received your shipment notification.');
            window.location.href = 'http://shop_system.com/user/user_info.php#my_purchase'; 
        </script>";
?>