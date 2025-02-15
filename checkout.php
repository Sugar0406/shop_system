<!-- 使用者基本資料 用來預填購買資訊的 但使用者依然可以自行輸入資訊 -->
<?php
    session_start();
    // 假設登入後，你將使用者的名稱和頭像儲存在 Session 中
    // 如果未登入，則這些 session 變數不會設置
    $user_id = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : null;
    $user_name = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : null;
    $user_picture = isset($_SESSION['USER_PICTURE']) ? $_SESSION['USER_PICTURE'] : null;
    $user_fullname = isset($_SESSION["FULL_NAME"]) ? $_SESSION["FULL_NAME"] : null;
    $user_address = isset($_SESSION["ADDRESS"]) ? $_SESSION["ADDRESS"] : null;
    $user_phone = isset($_SESSION["PHONE"]) ? $_SESSION["PHONE"] : null;
?>




<?php
    include "./database_connect.php";

    $order_id = $_POST["hidden_order_id"];
    // echo $order_id;

    //取得該筆訂單的所有資料
    $search_order_sql = "SELECT * FROM orders WHERE ORDER_ID=?";
    $search_order_stmt = $conn->prepare($search_order_sql);
    $search_order_stmt->bind_param("s", $order_id);
    $search_order_stmt->execute();
    $search_order_result = $search_order_stmt->get_result();
    $order_data = $search_order_result->fetch_assoc();


    // 取得當前所有的商品售價所有明細
    $order_product_list = json_decode($order_data["ORDER_PRODUCTS"], true);
    $total_price = 0;
    foreach ($order_product_list as &$order_product_data){

        // 取得每項商品的當前價格
        $search_product_sql = "SELECT PRODUCT_NAME, PRICE FROM products WHERE PRODUCT_ID=?";
        $search_product_stmt = $conn->prepare($search_product_sql);
        $search_product_stmt->bind_param("s", $order_product_data["productId"]);
        $search_product_stmt->execute();
        $search_product_result = $search_product_stmt->get_result();
        $search_product_row = $search_product_result->fetch_assoc();

        $order_product_data["price"] = $search_product_row["PRICE"];
        $order_product_data["product_name"] = $search_product_row["PRODUCT_NAME"];

        // echo $order_product_data["product_name"] . "</br>";
        // echo $order_product_data["price"] . "</br>";
        // echo $order_product_data["quantity"] . "</br>";

        // $將商品總價格加入total_price
        $total_price += $order_product_data["price"] * $order_product_data["quantity"];
    }
?>


<?php

function getFirstImagePath($product_id) {
    // 設定商品資料夾的路徑
    $directory = './product_img/' . $product_id . "/";

    // 讀取資料夾中的所有檔案
    $files = scandir($directory);

    // 移除 "." 和 ".."
    $files = array_diff($files, array('.', '..'));

    // 檢查是否有檔案
    if (empty($files)) {
        return null; // 沒有檔案的情況
    }

    // 取得第一個檔案的名稱
    $first_file = reset($files);

    // 回傳完整檔案路徑
    return $directory . $first_file;
}
?>











<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="./main_css/checkout.css" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <title>shopping_cart</title>
    </head>


    <body>
        <!-- main banner -->
        <div class="main_banner">
            <div class="main_banner_title" onclick="window.location.href='./main.php'">E-shop system</div>

            <div class="inputbar_button_wrapper">
                <input id="eshop_search_bar" class="eshop_search_bar"/>
                <button class="eshop_search_button" onclick="redirectToSearch()" >Search</button>
            </div>

            <script>
                
                //使用者按下enter後進行搜索
                document.getElementById("eshop_search_bar").addEventListener("keypress", function(event) {
                    if (event.key === "Enter") {
                        event.preventDefault(); // 防止預設的提交行為
                        redirectToSearch();
                    }
                });


                function redirectToSearch() {
                    var currentUrl = window.location.href; 
                    var newUrl = new URL("http://shop_system.com/main.php");
                    var keyword = document.getElementById("eshop_search_bar").value.trim();

                    if( keyword != "" ){

                        // 檢查是否已經有 keyword 參數
                        if (newUrl.searchParams.has('keyword')) {
                            newUrl.searchParams.set('keyword', keyword);
                        } 
                        else {
                            newUrl.searchParams.append('keyword', keyword);
                        }

                        // 檢查是否已經有 page 參數，設定為第一頁
                        if (newUrl.searchParams.has('page')) {
                            newUrl.searchParams.set('page', 1);
                        }
                        else{
                            newUrl.searchParams.append('page', 1);
                        }


                        // 檢查是否已經有 category 參數，搜尋結果優先顯示所有分類
                        if (newUrl.searchParams.has('selected_category')) {
                            newUrl.searchParams.set('selected_category', "All category");
                        }
                        else{
                            newUrl.searchParams.append('selected_category', "All category");
                        }



                        window.location.href = newUrl.toString();

                    }
                    // 沒有輸入時，跳出提醒
                    else{
                        alert("Please enter search keyword.");
                    }

                }
            </script>


            <div class="login_register_wrapper">
                <!-- 如果用戶已登入，顯示使用者名稱和頭像 -->
                <?php if ($user_name && $user_picture): ?>
                    <div class="user_info">
                        <a href="./shopping_cart.php" class="cart_button">
                            <i class="fa fa-shopping-cart"></i>
                        </a>
                        <a href="http://shop_system.com/user/user_info.php#my_account" class="user_name"><?php echo $user_name; ?></a>
                        <img src="<?php echo  "./customer/customer_img/" . $user_picture; ?>" alt="User picture" class="user_picture">
                    </div>

                <?php else: ?>
                    <a class="login_word" href="./customer/login.html">Login</a>
                    <div class="login_register_divider">/</div>
                    <a class="register_word" href="./customer/register.php">Register</a>
                <?php endif; ?>
            </div>
            
        </div>




        <!-- 結帳頁面內容主體 -->
        <div class="checkout_container">


            <!-- 先呈現所有商品 -->      
            <div class="checkout_product_container">
                <?php foreach ($order_product_list as $order_product): ?>
                    <div class="product_container">
                        <div class="product_image">
                            <img src="<?php  echo getFirstImagePath($order_product["productId"]) ?>" alt="<?php echo $order_product['product_name'];?>">
                        </div>

                        <div class="product_info">
                            <p class="product_name"><?php echo $order_product["product_name"];?></p>
                            <p class="price">Price: $<?php echo $order_product["price"];?></p>
                            <p class="quantity">Quantity: <?php echo $order_product["quantity"];?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- 呈現總價格 -->
            <div class="total_price">Total : <?php echo $total_price  ?></div>




            <!-- 結帳表單 -->
            <form id="cartForm" action="../upload_checkout.php" method="POST">

                <!-- 選擇付款方式 -->
                <div class="pay_method_container">
                    <p><strong>Please select a payment method:</strong></p>
                    <label>
                        <input type="radio" name="payment_method" value="CREDIT_CARD">
                        credit card
                    </label>
                    <label>
                        <input type="radio" name="payment_method" value="CASH_ON_DELIVERY">
                        cash on delivery
                    </label>
                    <label>
                        <input type="radio" name="payment_method" value="E_WALLET">
                        e-wallet
                    </label>
                    <label>
                        <input type="radio" name="payment_method" value="BANK_TRANSFER">
                        bank transfer
                    </label>


                    <p id="payment_method_error" style="color: red; display: none;">Please select a payment method.</p>
                </div>


                <!-- 購買人基本資料填寫 -->
                <div class="buyer_information">
                    <div class="fullname_input_container">
                        <span class="fuullname_hint">Input Buyer Full Name</span> 
                        <input type="text" id="full_name" name="fullname" class="full_name" value="<?php echo $user_fullname; ?>" placeholder="buyer fullname" required>
                    </div>

                    <div class="address_input_container">
                        <span class="address_hint">Input Delivery Address</span>
                        <input type="text" id="address" name="address" class="address" value="<?php echo $user_address; ?>" placeholder="delivery address" required>
                    </div>

                    <div class="phone_input_container">
                        <span class="phone_hint">Input Phone Number</span>
                        <input type="text" id="phone" name="phone" class="phone" value="<?php echo $user_phone; ?>"  placeholder="phone number" required>
                    </div>
                </div>
                
                <!-- hidden price -->
                <input type="hidden" name="total_price" value="<?php echo (int)$total_price; ?>" >
                <!-- hidden order_id -->
                <input type="hidden" name="order_id" value="<?php echo $order_id;?>" >
                <button class="place_order_button" id="place_order_button" type="submit">Place Order</button>
            </form>
            
            <script>
                const place_order_button = document.getElementById("place_order_button");
                const cartForm = document.getElementById("cartForm");
                const paymentMethodError = document.getElementById("payment_method_error");

                // 當按下按鈕時，檢查是否有選擇付款方式
                place_order_button.addEventListener("click", function (event) {
                    const paymentMethods = document.getElementsByName("payment_method");
                    let isPaymentSelected = false;

                    for (let i = 0; i < paymentMethods.length; i++) {
                        if (paymentMethods[i].checked) {
                            isPaymentSelected = true;
                            break;
                        }
                    }

                    if (!isPaymentSelected) {
                        paymentMethodError.style.display = "block";
                        event.preventDefault();
                    } else {
                        paymentMethodError.style.display = "none";
                    }
                });
            </script>

        </div>


    </body>

</html>