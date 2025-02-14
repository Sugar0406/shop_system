<?php

    session_start();
    // 假設登入後，你將使用者的名稱和頭像儲存在 Session 中
    // 如果未登入，則這些 session 變數不會設置
    $user_id = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : null;
    $user_name = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : null;
    $user_picture = isset($_SESSION['USER_PICTURE']) ? $_SESSION['USER_PICTURE'] : null;

?>



<?php

    include "./database_connect.php";

    $status = "IN_CART";

    // 找出所有使用者IN_CART訂單
    $search_incart_order_sql = " SELECT * FROM orders WHERE BUYER_ID=? AND STATUS=? ";
    $search_incart_order_stmt = $conn->prepare($search_incart_order_sql);
    $search_incart_order_stmt->bind_param("ss", $user_id, $status);
    $search_incart_order_stmt->execute();
    $search_incart_order_result = $search_incart_order_stmt->get_result();

    $in_cart_orders = [];
    if($search_incart_order_result){
        while($row = $search_incart_order_result->fetch_assoc()){
            $in_cart_orders[] = $row;
        }
    }

?>

<?php
    include "./database_connect.php";

    function get_seller_name( $seller_id ){
        global $conn;

        $get_seller_name_sql = "SELECT USER_NAME FROM users WHERE user_id=?";
        $get_seller_name_stmt = $conn->prepare($get_seller_name_sql);
        $get_seller_name_stmt->bind_param("s", $seller_id);
        $get_seller_name_stmt->execute();
        $get_seller_name_result = $get_seller_name_stmt -> get_result();
        $row = $get_seller_name_result->fetch_assoc();
        return $row['USER_NAME'] ?? 'Unknown';
    }



    function get_product_name( $product_id ){
        global $conn;

        $get_product_name_sql = "SELECT PRODUCT_NAME FROM products WHERE PRODUCT_ID=?";
        $get_product_name_stmt = $conn->prepare($get_product_name_sql);
        $get_product_name_stmt->bind_param("s", $product_id);
        $get_product_name_stmt->execute();
        $get_product_name_result = $get_product_name_stmt -> get_result();
        $row = $get_product_name_result->fetch_assoc();
        return $row['PRODUCT_NAME'];
    }
?>


<!DOCTYPE html>

<html>

    <head>
        <link rel="stylesheet" href="./main_css/shopping_cart.css" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <title>shopping_cart</title>
    </head>

    <body>

        <!-- main banner -->
        <div class="main_banner">
            <div class="main_banner_title" onclick="window.location.href='../main.php'">E-shop system</div>

            <div class="inputbar_button_wrapper">
                <input class="eshop_search_bar"/>
                <button class="eshop_search_button" >Search</button>
            </div>


            <div class="login_register_wrapper">
                <!-- 如果用戶已登入，顯示使用者名稱和頭像 -->
                <?php if ($user_name && $user_picture): ?>
                    <div class="user_info">
                        <a href="../shopping_cart.php" class="cart_button">
                            <i class="fa fa-shopping-cart"></i>
                        </a>
                        <a href="http://shop_system.com/user/user_info.php#my_account" class="user_name"><?php echo $user_name; ?></a>
                        <img src="<?php echo  "../customer/customer_img/" . $user_picture; ?>" alt="User picture" class="user_picture">
                    </div>

                <?php else: ?>
                    <a class="login_word" href="../customer/login.html">Login</a>
                    <div class="login_register_divider">/</div>
                    <a class="register_word" href="../customer/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>


        <div class="shopping_cart_container">
            <?php foreach ($in_cart_orders as $orders): ?>
                <div class="order_card">
            
                    <p>Seller : <?php echo get_seller_name($orders["SELLER_ID"]) ?></p>
                    
                    <!-- 生成商品 -->
                    <?php foreach (json_decode($orders["ORDER_PRODUCTS"], true) as $product): ?>
                        <p>Product Name: <?php echo get_product_name($product["productId"]); ?></p>
                        <p>quantity: <?php echo $product["quantity"]?></p>
                    <?php endforeach; ?>
            
                </div>
            <?php endforeach; ?>
        </div>





    </body>

</html>