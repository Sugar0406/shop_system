<?php

    include "../database_connect.php";
    $product_id = isset($_GET["product_id"]) ? $_GET["product_id"] : null;


    if( $product_id == null ){
        echo "<script>
            alert('Product ID is missing. Back to the main page');
            window.location.href = 'http://shop_system.com';
        </script>";
    }
    else{
        // $get_product_detail_sql = "";
    }

?>