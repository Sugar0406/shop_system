<?php

    include "../database_connect.php";

    session_start();
    $user_id = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : null;

    // 先檢查使用者是否有尚未完成的訂單
    $check_order_sql = "SELECT * FROM orders WHERE (SELLER_ID=? OR BUYER_ID=?) AND STATUS!='COMPLETED' ";
    $check_order_stmt = $conn->prepare($check_order_sql);
    $check_order_stmt->bind_param("ss", $user_id, $user_id);
    $check_order_stmt->execute();
    $check_order_result = $check_order_stmt->get_result();

    if($check_order_result->num_rows > 0){
        echo "<script>
                alert('You have unfinished order. Please complete the order first.');
                window.location.href='http://shop_system.com/user/user_info#my_purchase';
            </script>";
        exit();
    }
    else{

        //刪除帳號將 (EMAIL(user account) 以及 USER_NAME 改為 NULL ) STATUS 改為DELETE
        $delete_account_sql = "UPDATE users SET EMAIL=NULL, USER_NAME=NULL, STATUS='DELETE' WHERE USER_ID=?";
        $delete_account_stmt = $conn->prepare($delete_account_sql);
        $delete_account_stmt->bind_param("s", $user_id);
        if($delete_account_stmt->execute()){
            //刪除成功 清空session
            session_destroy();
            echo "<script>
                    alert('Your account has been successfully deleted.');
                    window.location.href='http://shop_system.com';
                </script>";
        }

    }

?>