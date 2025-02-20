<?php

    include "./database_connect.php";

    $order_id = $_POST["hidden_order_id"];
    echo "delete cart order " . $order_id;

    $delete_cart_order_sql = "DELETE FROM orders WHERE order_id=? AND STATUS = 'IN_CART'";
    $delete_cart_order_stmt = $conn->prepare($delete_cart_order_sql);
    $delete_cart_order_stmt->bind_param("s", $order_id);
    $delete_cart_order_stmt->execute();
    $delete_cart_order_stmt->close();

    echo "<script>window.location.href = document.referrer;</script>";

?>