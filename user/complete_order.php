<?php

    include "../database_connect.php";

    //使用者收到貨品完成訂單 將STATUS改為COMPLETE 並記錄COMPLETED_AT時間
    $order_id = $_POST["order_id"];
    $order_status = "COMPLETED";
    date_default_timezone_set('Asia/Taipei');
    $current_datetime = (new DateTime())->format('Y-m-d H:i:s');

    $update_order_sql = "UPDATE orders 
                        SET STATUS=?, COMPLETED_AT=?
                        WHERE ORDER_ID=?";
    $update_order_stmt = $conn->prepare($update_order_sql);
    $update_order_stmt->bind_param("sss", $order_status, $current_datetime, $order_id);
    


    if($update_order_stmt->execute()){
        echo "<script>
            alert('Thank you for your purchase! Your order has been completed.');
            window.location.href = 'http://shop_system.com/user/user_info.php#my_purchase'; 
        </script>";
    }

?>