<?php
session_start();
$user_name = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : null;
$email = isset($_SESSION['EMAIL']) ? $_SESSION['EMAIL'] : null;
$user_picture = isset($_SESSION['USER_PICTURE']) ? $_SESSION['USER_PICTURE'] : null;
$fullname = isset($_SESSION['FULL_NAME']) ? $_SESSION['FULL_NAME'] : null;
$address = isset($_SESSION['ADDRESS']) ? $_SESSION['ADDRESS'] : null;
$phone = isset($_SESSION['PHONE']) ? $_SESSION['PHONE'] : null;
?>

<!-- 查詢我的商品 -->
<?php
        include "../database_connect.php";

        // 查詢商品資料
        $serach_my_product_sql = "SELECT * FROM products WHERE SELLER_ID = ? ORDER BY CREATE_AT DESC"; 
        $serach_my_product_stmt = $conn->prepare($serach_my_product_sql);
        $serach_my_product_stmt->bind_param("s", $_SESSION['USER_ID']);  
        $serach_my_product_stmt->execute();
        $result = $serach_my_product_stmt->get_result();

        // 取得查詢結果
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $conn->close();
?>



<!-- 查詢我的訂單 -->
<?php

        include "../database_connect.php";

        // 作為賣家或買家的訂單都要列出
        $completed_status = "COMPLETED";
        $incart_status = "IN_CART";

        // 查詢自己作為賣家的訂單 (SELLER_ID為自己的ID 且狀態不為COMPLETED 和 IN_CART)
        $sell_order_sql = "SELECT * FROM orders WHERE SELLER_ID=? AND STATUS!=? AND STATUS!=?";
        $sell_order_stmt = $conn->prepare($sell_order_sql);
        $sell_order_stmt->bind_param("sss", $_SESSION['USER_ID'], $incart_status, $completed_status);
        $sell_order_stmt->execute();
        $sell_order_result = $sell_order_stmt->get_result();
        $sell_order_stmt->close();

        // 查詢自己作為買家的訂單
        $buy_order_sql = "SELECT * FROM orders WHERE BUYER_ID=? AND STATUS!=? AND STATUS!=?";
        $buy_order_stmt = $conn->prepare($buy_order_sql);
        $buy_order_stmt->bind_param("sss", $_SESSION['USER_ID'], $incart_status, $completed_status);
        $buy_order_stmt->execute();
        $buy_order_result = $buy_order_stmt->get_result();
        $buy_order_stmt->close();

        // 查詢歷史訂單
        $history_order_sql = "SELECT * FROM orders WHERE (SELLER_ID=? OR BUYER_ID=?) AND STATUS =?";
        $history_order_stmt = $conn->prepare($history_order_sql);
        $history_order_stmt->bind_param("sss", $_SESSION['USER_ID'], $_SESSION['USER_ID'], $completed_status);
        $history_order_stmt->execute();
        $history_order_result = $history_order_stmt->get_result();
        $history_order_stmt->close();

?>






<?php
    include "../database_connect.php";

    function get_user_name( $user_id ){
        global $conn;

        $get_user_name_sql = "SELECT USER_NAME FROM users WHERE user_id=?";
        $get_user_name_stmt = $conn->prepare($get_user_name_sql);
        $get_user_name_stmt->bind_param("s", $user_id);
        $get_user_name_stmt->execute();
        $get_user_name_result = $get_user_name_stmt -> get_result();
        $row = $get_user_name_result->fetch_assoc();
        return $row['USER_NAME'] ?? 'User Account Delete';
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


    function change_pay_method_name( $pay_method ){

        $new_pay_method_name = "";

        if( $pay_method == "CREDIT_CARD"){
            $new_pay_method_name = "credit card";
        }
        elseif( $pay_method == "CASH_ON_DELIVERY"){
            $new_pay_method_name = "cash on delivery";
        }
        elseif( $pay_method == "E_WALLET"){
            $new_pay_method_name = "e wallet";
        }
        elseif($pay_method == "BANK_TRANSFER"){
            $new_pay_method_name = "bank transfer";
        }

        return $new_pay_method_name;
    }


    function getFirstImagePath($product_id) {
        // 設定商品資料夾的路徑
        $directory = '../product_img/' . $product_id . "/";
    
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
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USER INFO</title>
    <link rel="stylesheet" href="user_css/info.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>
<body>

    <!-- 左側菜單欄 -->
    <div class="sidebar">
        <div class="back_to_home_page_wrapper">
            <button onclick="redirectToMainPage()" class="back_to_main_button">&#8636; Back</button>
        </div>
        <script>
            function redirectToMainPage() {
                window.location.href = "http://shop_system.com";
            }
        </script>

        <!-- 左側菜單欄 使用者展示 -->
        <div class="user_info">
            <img src="<?php echo  "../customer/customer_img/" . $user_picture; ?>" alt="User picture" class="user_picture">
            <span class="user_name"><?php echo $user_name; ?></span>
        </div>
        <ul>
            <li><a href="#my_account" data-section="my_account_wrapper">My Account</a></li>
            <li><a href="#change_password" data-section="change_password_wrapper">Change Password</a></li>
            <li><a href="#my_purchase" data-section="my_purchase_wrapper">My Purchase</a></li>
            <li><a href="#my_product" data-section="my_product_wrapper">My Product</a></li>

            <!-- logout 直接跳轉 不需要section -->
            <li><a onclick="redirectToLogout()" >Log Out</a></li>
            <!-- 刪除帳號 -->
            <li><a href="#delete_account" data-section="delete_account_wrapper">Delete Account</a></li>
        </ul>
    </div>


    <!-- 單頁跳轉實現 -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 取得所有選單按鈕
            const menuItems = document.querySelectorAll(".sidebar ul li a");
            // 取得所有內容區塊
            const sections = document.querySelectorAll(".content-section");

            function hideAllSections() {
                sections.forEach(section => {
                    section.classList.remove("active");
                });
            }

            // 檢查 URL hash，並顯示對應區塊
            function checkHash() {
                const hash = window.location.hash.substring(1); // 去掉 #
                if (hash) {
                    const targetSection = document.getElementById(hash + "_wrapper"); // 修正選擇器
                    if (targetSection) {
                        hideAllSections();
                        targetSection.classList.add("active");
                    }
                }
            }

            // 監聽選單按鈕點擊事件
            menuItems.forEach(item => {
                item.addEventListener("click", function(event) {
                    const sectionId = this.getAttribute("data-section");

                    // 如果是 Log Out，允許直接跳轉，不攔截點擊事件
                    if (!sectionId) {
                        // alert("Logout Successfully");
                        return; 
                    }

                    event.preventDefault(); // 防止預設行為影響 hash 更新

                    // 更新網址 hash
                    window.location.hash = sectionId.replace("_wrapper", ""); 

                    // 立即執行 checkHash，確保 active 類別即時更新
                    checkHash();
                });
            });

            // 在頁面載入時檢查 hash
            checkHash();

            // 監聽 hash 變化（如果用戶手動修改 hash）
            window.addEventListener("hashchange", checkHash);
        });

    </script>









    <!-- 主要內容區域 -->
    <div class="main-content">





        <!-- My Account My Account My Account My Account My Account My Account My Account My Account My Account My Account My Account My Account  -->
        <div id="my_account_wrapper" class="content-section active">
            <h1 class="my_account_title">Basic Information</h1>
            
            <!-- user name -->
            <div class="user_name_wrapper">
                <label class="user_name_field_word">User Name :</label>
                <span class="user_name" id="user_name"> <?php echo "$user_name" ?> </span>
            </div>
            
            <!-- email -->
            <div class="email_wrapper">
                <label class="email_field_word">Email :</label>
                <span class="email" id="email"> <?php echo "$email" ?> </span>
            </div>

            <!-- full name -->
            <div class="fullname_wrapper">
                <label class="fullname_field_word">Full Name:</label>
                <span class="fullname" id="fullname"> <?php echo "$fullname" ?> </span>
            
            </div>
            
            <!-- address -->
            <div class="address_wrapper">
                <label class="address_field_word">Address:</label>
                <span class="address" id="address"> <?php echo "$address" ?> </span>
            
            </div>

            <!-- user phone number -->
            <div class="phone_wrapper">
                <label class="phone_field_word">Phone Number:</label>
                <span class="phone" id="phone"> <?php echo "$phone" ?> </span>
            </div>


            <button onclick="redirectToEditInfoPage()" class="edit_basic_info_button">Edit Basic Information</button>
            <script>
                function redirectToEditInfoPage() {
                    location.href = "./edit_user_info.php";
                }
            </script>

        </div>





        <!-- Change Password Change Password Change Password Change Password Change Password Change Password Change Password Change Password Change Password -->
        <div id="change_password_wrapper" class="content-section">
        <form id="change_password" action="./change_password.php" method="POST">
            <h1 class="change_password_title">Change Password</h1>

            <!-- origin password -->
            <div class="origin_password_wrapper">
                <div class="origin_password_title">Input your original password</div>
                <div class="origin_password_input_container">
                    <input type="password" id="origin_password_input" class="origin_password_input" name="origin_password" placeholder="OLD PASSWORD" required>
                    <button id="show_origin_password_button" class="show_origin_password_button" type="button">
                        <i id="show_origin_password_button_icon" class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>

                <!-- 暫時顯示密碼 (origin password 欄位)-->
                <script>
                    const toggleOriginPassword = document.getElementById("origin_password_input");
                    const showOriginPasswordButton = document.getElementById("show_origin_password_button");
                    const origin_password_btn_icon = document.getElementById("show_origin_password_button_icon");

                    // 指標按下時 顯示密碼(將type改為text)
                    showOriginPasswordButton.addEventListener('mousedown', function () {
                        event.preventDefault(); // 阻止按鈕默認行為
                        toggleOriginPassword.type = "text";
                        origin_password_btn_icon.classList.remove('fa-eye-slash'); // 移除隱藏圖標
                        origin_password_btn_icon.classList.add('fa-eye'); // 顯示密碼圖標
                    });

                    // 指標鬆開時 隱藏密碼(將type改回password)
                    showOriginPasswordButton.addEventListener('mouseup', function () {
                        event.preventDefault();
                        toggleOriginPassword.type = "password";
                        origin_password_btn_icon.classList.remove('fa-eye');
                        origin_password_btn_icon.classList.add('fa-eye-slash');
                    });

                    // 處理按鈕的 "mouseleave"
                    showOriginPasswordButton.addEventListener('mouseleave', function () {
                        toggleOriginPassword.type = "password";
                        origin_password_btn_icon.classList.remove('fa-eye');
                        origin_password_btn_icon.classList.add('fa-eye-slash');
                    });
                </script>
            </div>

            <!-- New password -->
            <div class="new_password_wrapper">
                <div class="new_password_title">Input your new password</div>
                <div class="new_password_input_container">
                    <input type="password" id="new_password_input" class="new_password_input" name="new_password" placeholder="NEW PASSWORD" required>
                    <button id="show_new_password_button" class="show_new_password_button" type="button">
                        <i id="show_new_password_button_icon" class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>

                <!-- 暫時顯示密碼 (new password 欄位)-->
                <script>
                    const toggleNewPassword = document.getElementById("new_password_input");
                    const showNewPasswordButton = document.getElementById("show_new_password_button");
                    const new_password_btn_icon = document.getElementById("show_new_password_button_icon");

                    // 指標按下時 顯示密碼
                    showNewPasswordButton.addEventListener('mousedown', function () {
                        event.preventDefault();
                        toggleNewPassword.type = "text";
                        new_password_btn_icon.classList.remove('fa-eye-slash');
                        new_password_btn_icon.classList.add('fa-eye');
                    });

                    // 指標鬆開時 隱藏密碼
                    showNewPasswordButton.addEventListener('mouseup', function () {
                        event.preventDefault();
                        toggleNewPassword.type = "password";
                        new_password_btn_icon.classList.remove('fa-eye');
                        new_password_btn_icon.classList.add('fa-eye-slash');
                    });

                    // 處理按鈕的 "mouseleave"
                    showNewPasswordButton.addEventListener('mouseleave', function () {
                        toggleNewPassword.type = "password";
                        new_password_btn_icon.classList.remove('fa-eye');
                        new_password_btn_icon.classList.add('fa-eye-slash');
                    });
                </script>
            </div>

            <!-- Confirm New password -->
            <div class="confirm_new_password_wrapper">
                <div class="confirm_new_password_title">Confirm your new password</div>
                <div class="confirm_new_password_input_container">
                    <input type="password" id="confirm_new_password_input" class="confirm_new_password_input" name="confirm_new_password" placeholder="CONFIRM NEW PASSWORD" required>
                    <button id="show_confirm_password_button" class="show_confirm_password_button" type="button">
                        <i id="show_confirm_password_button_icon" class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>

                <!-- 暫時顯示密碼 (confirm new password 欄位)-->
                <script>
                    const toggleConfirmNewPassword = document.getElementById("confirm_new_password_input");
                    const showConfirmPasswordButton = document.getElementById("show_confirm_password_button");
                    const confirm_new_password_btn_icon = document.getElementById("show_confirm_password_button_icon");

                    // 指標按下時 顯示密碼
                    showConfirmPasswordButton.addEventListener('mousedown', function () {
                        event.preventDefault();
                        toggleConfirmNewPassword.type = "text";
                        confirm_new_password_btn_icon.classList.remove('fa-eye-slash');
                        confirm_new_password_btn_icon.classList.add('fa-eye');
                    });

                    // 指標鬆開時 隱藏密碼
                    showConfirmPasswordButton.addEventListener('mouseup', function () {
                        event.preventDefault();
                        toggleConfirmNewPassword.type = "password";
                        confirm_new_password_btn_icon.classList.remove('fa-eye');
                        confirm_new_password_btn_icon.classList.add('fa-eye-slash');
                    });

                    // 處理按鈕的 "mouseleave"
                    showConfirmPasswordButton.addEventListener('mouseleave', function () {
                        toggleConfirmNewPassword.type = "password";
                        confirm_new_password_btn_icon.classList.remove('fa-eye');
                        confirm_new_password_btn_icon.classList.add('fa-eye-slash');
                    });
                </script>
            </div>

            <!-- change password button -->
            <button id="change_password_button" class="change_password_button" type="submit">CHANGE PASSWORD</button>

        </form>
        </div>





        <!-- My Purchase My Purchase My Purchase My Purchase My Purchase My Purchase My Purchase My Purchase My Purchase My Purchase My Purchase My Purchase  -->
        <div id="my_purchase_wrapper" class="content-section">
            <h1 class="my_purchase_title">My Purchase</h1>

            <!-- purchase_card 分為2種 身為買家或身為賣家 -->
            <!-- 且訂單分為 SUBMITTED (訂單送出)，SHIPPED(商品寄出) COMPLETED(訂單完成)-->
            <!-- 利用radio input 切換顯示的分類(order_card都有一個自定義屬性status 分別為buyer seller history) -->
            <div class="ratio_button_container">
                <label class="ratio_button">
                    <input type="radio" name="order_filter" value="buyer" checked> My Purchased Orders
                </label>
                <label class="ratio_button">
                    <input type="radio" name="order_filter" value="seller"> My Sold Items
                </label>
                <label class="ratio_button">
                    <input type="radio" name="order_filter" value="history"> History Order
                </label>
            </div>


            <script>
                // 當網頁載入時 讓使用者選擇 My Purchase Order 來隱藏其他的訂單(預設顯示自己購買的訂單)
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.order_card').forEach(card => {
                        if (card.getAttribute('status') === 'buyer') {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });

                // 當選擇不同的 radio 按鈕時，過濾顯示對應的訂單卡片
                document.querySelectorAll('input[name="order_filter"]').forEach(input => {
                    input.addEventListener('change', function() {
                        const selectedValue = this.value; // 取得選中的值

                        // 獲取所有的 order_card
                        document.querySelectorAll('.order_card').forEach(card => {
                            // 根據 status 屬性來決定是否顯示
                            if (card.getAttribute('status') === selectedValue) {
                                card.style.display = 'block'; // 顯示符合條件的卡片
                            } else {
                                card.style.display = 'none'; // 隱藏不符合條件的卡片
                            }
                        });
                    });
                });
            </script>


            <div class="order_card_container">
                
                <!-- 自己是買家 order_card->status為buyer -->
                <?php foreach($buy_order_result as $buy_order): ?>
                    <div class="order_card" status="buyer">

                        <p class="seller_name">Seller : <?php echo get_user_name($buy_order["SELLER_ID"]) ?></p>
                        <p class="pay_methode">Payment Method : <?php echo change_pay_method_name($buy_order["PAY_METHOD"]); ?></p>
                        <p class="checkout_time">Checke Out Time : <?php echo $buy_order["CREATED_AT"]; ?></p>

                        <!-- 生成商品 -->
                        <?php foreach (json_decode($buy_order["ORDER_PRODUCTS"], true) as $product): ?>
                            <div class="product_container">

                                <div class="order_product_image">
                                    <img src="<?php  echo getFirstImagePath($product["productId"]) ?>" alt="<?php echo $order_product['product_name'];?>">
                                </div>

                                <div class="order_product_info">
                                    <p class="order_roduct_name">Product Name: <?php echo get_product_name($product["productId"]); ?></p>
                                    <p class="order_product_quantity">quantity: <?php echo $product["quantity"]?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- 按下按鈕相當於使用者收到貨品 完成訂單 -->
                        <form action="./complete_order.php" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $buy_order["ORDER_ID"]?>">
                            <button type="submmit" id="order_button_<?php echo $buy_order["ORDER_ID"]; ?>" class="order_button">Complete Order</button>
                        </form>
                        <script>

                            // 如果order還沒寄出(status不是SHIPPED) 則修改按鈕樣式提醒使用者並且改為不可點玄
                            document.addEventListener("DOMContentLoaded", function() {
                                const order_btn = document.getElementById("order_button_<?php echo $buy_order["ORDER_ID"]; ?>");
                                const now_order_status = <?php echo json_encode($buy_order["STATUS"]); ?>;
                                
                                if(now_order_status !== "SHIPPED"){
                                    order_btn.style.backgroundColor = "#ccc"; 
                                    order_btn.style.cursor = "not-allowed"; 
                                    order_btn.disabled = true;
                                    order_btn.textContent = "Order Not Shipped"; // 顯示商品未寄出的訊息
                                }
                            });
                        </script>

                    </div>
                <?php endforeach; ?>


                <!-- 自己是賣家 order_card->status為seller -->
                <?php foreach($sell_order_result as $sell_order): ?>
                    <div class="order_card" status="seller">

                        <p class="buyer_name">buyer : <?php echo get_user_name($sell_order["BUYER_ID"]) ?></p>
                        <p class="pay_methode">Payment Method : <?php echo change_pay_method_name($sell_order["PAY_METHOD"]); ?></p>
                        <p class="checkout_time">Checke Out Time : <?php echo $sell_order["CREATED_AT"]; ?></p>

                        <!-- 生成商品 -->
                        <?php foreach (json_decode($sell_order["ORDER_PRODUCTS"], true) as $product): ?>
                            <div class="product_container">

                                <div class="order_product_image">
                                    <img src="<?php  echo getFirstImagePath($product["productId"]) ?>" alt="<?php echo $order_product['product_name'];?>">
                                </div>

                                <div class="order_product_info">
                                    <p class="order_product_name">Product Name: <?php echo get_product_name($product["productId"]); ?></p>
                                    <p class="order_product_quantity">quantity: <?php echo $product["quantity"]; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <form action="./shipped_order.php" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $sell_order["ORDER_ID"]; ?>">
                            <button id="order_button_<?php echo $sell_order["ORDER_ID"]; ?>" class="order_button">Shipped Order</button>
                        </form>
                        <script>

                            // 寄出商品後(status是SHIPPED) 則隱藏按鈕
                            document.addEventListener("DOMContentLoaded", function() {
                                const order_btn = document.getElementById("order_button_<?php echo $sell_order["ORDER_ID"]; ?>");
                                const now_order_status = <?php echo json_encode($sell_order["STATUS"]); ?>;
                                
                                if(now_order_status == "SHIPPED"){
                                    order_btn.style.backgroundColor = "#ccc";
                                    order_btn.style.cursor = "not-allowed";
                                    order_btn.disabled = true;
                                    order_btn.textContent = "Order Aready Shipped"; // 顯示商品已寄出的訊息
                                }
                            });
                        </script>

                    </div>
                <?php endforeach; ?>




                <!-- 歷史訂單 無論自己是買家或賣家 status=history -->
                <?php foreach($history_order_result as $history_order): ?>
                    <div class="order_card" status="history">
                    
                        <p class="seller_name">Seller : <?php echo get_user_name($history_order["SELLER_ID"]) ?></p>
                        <p class="buyer_name">buyer : <?php echo get_user_name($history_order["BUYER_ID"]) ?></p>
                        <p class="pay_methode">Payment Method : <?php echo change_pay_method_name($history_order["PAY_METHOD"]); ?></p>
                        <p class="checkout_time">Checke Out Time : <?php echo $history_order["CREATED_AT"]; ?></p>
                        <p class="complete_time">Order Complete at : <?php echo $history_order["COMPLETED_AT"] ?></p>
                        
                        <!-- 生成商品 -->
                        <?php foreach (json_decode($history_order["ORDER_PRODUCTS"], true) as $product): ?>
                            <div class="product_container">

                                <div class="order_product_image">
                                    <img src="<?php  echo getFirstImagePath($product["productId"]) ?>" alt="<?php echo $order_product['product_name'];?>">
                                </div>

                                <div class="order_product_info">
                                    <p class="order_product_name">Product Name: <?php echo get_product_name($product["productId"]); ?></p>
                                    <p class="order_product_quantity">quantity: <?php echo $product["quantity"]?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                <?php endforeach; ?>                    

            </div>

        </div>





        <!-- My Product My Product My Product My Product My Product My Product My Product My Product My Product My Product My Product My Product My Product -->
        <div id="my_product_wrapper" class="content-section">
            <h1 class="my_product_title">My Product</h1>

            <!-- 新增商品按鈕 -->
            <button onclick="redirectToAddProductPage()" class="add_product_button">ADD PRODUCT</button>
            <script>
                function redirectToAddProductPage() {
                    location.href = "./add_product.php";
                }
            </script>

            <div class="my_product_container">
                <?php foreach ($products as $product): ?>

                    <div class="product_card" >
                        <div class="product_image_slider">
                            <?php 
                                // 解碼圖片陣列
                                $images = json_decode($product['PRODUCT_IMAGE']);
                                $totalImages = count($images);
                            ?>
                            <!-- 預設顯示第一張圖片 -->
                            <img src="../product_img/<?php echo $product['PRODUCT_ID']; ?>/<?php echo $images[0]; ?>" alt="Product Image" class="product_image" data-product-id="<?php echo $product['PRODUCT_ID']; ?>" id="product_image_<?php echo $product['PRODUCT_ID']; ?>_0">

                            <!-- 顯示其他圖片 -->
                            <?php for ($i = 1; $i < $totalImages; $i++): ?>
                                <img src="../product_img/<?php echo $product['PRODUCT_ID']; ?>/<?php echo $images[$i]; ?>" alt="Product Image" class="product_image" data-product-id="<?php echo $product['PRODUCT_ID']; ?>" id="product_image_<?php echo $product['PRODUCT_ID']; ?>_<?php echo $i; ?>" style="display:none;">
                            <?php endfor; ?>
                            
                            <!-- 左右切換按鈕 -->
                            <button class="prev" onclick="changeImage('<?php echo $product['PRODUCT_ID']; ?>', -1)">&#10094;</button>
                            <button class="next" onclick="changeImage('<?php echo $product['PRODUCT_ID']; ?>', 1)">&#10095;</button>
                        </div>

                        <div class="product_info">
                            
                            <p class="product_name">Product Name: <?php echo htmlspecialchars($product['PRODUCT_NAME']); ?></p>

                            <p class="product_category">Product category: <?php echo htmlspecialchars($product['CATEGORY']); ?></p>
                            <p class="product_price">Price: $<?php echo number_format($product['PRICE']); ?></p>
                            <p class="product_in_stock"><?php echo $product['IN_STOCK']; ?> in stock</p>
                            <p class="product_sold"><?php echo $product['SOLD']; ?> sold</p>

                            <div class="product_button_container">
                                <button class="edit_product_button" onclick="redirectToEditProductInfo('<?php echo $product['PRODUCT_ID']; ?>')">Edit Product Info</button>
                                <button class="delete_product_button" onclick="redirectToDeleteProduct('<?php echo $product['PRODUCT_ID']; ?>')">Delete Product</button>
                                <script>
                                    function redirectToEditProductInfo(productId){
                                        window.location.href = "../product/edit_product_info.php?product_id=" + encodeURIComponent(productId);
                                    }
                                    function redirectToDeleteProduct(productId){
                                        if (confirm("Are you sure to delete this product?")) {
                                            window.location.href = "../product/delete_product.php?product_id=" + encodeURIComponent(productId);
                                        }
                                    }
                                </script>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <!-- 上一頁 / 下一頁按鈕 -->
            <div class="pagination">
                <button id="prevBtn" onclick="prevPage()">&#10094; Prev</button>
                <button id="nextBtn" onclick="nextPage()">Next &#10095;</button>
            </div>


            <!-- 商品多圖片切換腳本 -->
            <script>
                function changeImage(productId, direction) {
                    const images = document.querySelectorAll(  " [data-product-id=\"" + productId + "\"]"  )

                    // const images = document.querySelectorAll("#product_image_" + productId);
                    let currentIndex = 0;

                    
                    // 找出當前顯示的圖片索引
                    for (let i = 0; i < images.length; i++) {
                        if (images[i].style.display !== "none") {
                            currentIndex = i;
                            break;
                        }
                    }


                    // 計算新的顯示索引
                    let newIndex = currentIndex + direction;
                    
                    if (newIndex < 0) newIndex = images.length - 1;  // 回到最後一張
                    if (newIndex >= images.length) newIndex = 0;     // 回到第一張

                    // 隱藏當前顯示的圖片
                    images[currentIndex].style.display = "none";
                    // 顯示新的圖片
                    images[newIndex].style.display = "block";
                }
            </script>

            <!-- 多商品頁面切換腳本(每頁僅顯示2個商品) -->
            <script>
                let currentPage = 0;
                const itemsPerPage = 2;
                const products = document.querySelectorAll(".product_card");

                // 初始化，顯示第一頁的商品
                function showPage(page) {
                    // 確保頁數不會超出範圍
                    const totalPages = Math.ceil(products.length / itemsPerPage);
                    if (page < 0) page = totalPages - 1;
                    if (page >= totalPages) page = 0;

                    // 隱藏所有商品
                    products.forEach(product => product.style.display = "none");

                    // 計算當前頁面需要顯示的商品索引
                    const start = page * itemsPerPage;
                    const end = start + itemsPerPage;

                    for (let i = start; i < end && i < products.length; i++) {
                        products[i].style.display = "block";
                    }

                    // 更新當前頁
                    currentPage = page;
                }

                // 切換到上一頁
                function prevPage() {
                    showPage(currentPage - 1);
                }

                // 切換到下一頁
                function nextPage() {
                    showPage(currentPage + 1);
                }

                // 初始化顯示第一頁
                showPage(0);
            </script>
        </div>




        <!-- Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out Log_out -->
        <script>
            function redirectToLogout() {
                if (confirm("Are you sure to log out?")) {
                    alert("Logout Successfully");
                    location.href = "./logout.php";
                }
            }
        </script>



    <!-- Delete Account Delete Account Delete Account Delete Account Delete Account Delete Account Delete Account Delete Account Delete Account Delete Account -->
    <div id="delete_account_wrapper" class="content-section">
        <h1 class="delete_account_title">Delete Account</h1>

        <div class="check_delete_account_container">
            <p>Are you sure to delete your account? This action cannot be undone.</p>
            <p>If you want to delete your account, please enter the following format:</p>
            <p>UserName@delete</p>

            <form method="post" action="./delete_account.php" onsubmit="return confirmDeletion()">
            <input type="text" class="delete_accout_input" name="delete_account" placeholder="<?php echo $user_name ?>@delete" required>
            <button type="submit" name="delete_account_button" class="delete_account_button">DeleteAccount</button>
            </form>
        </div>

        <script>
            // 按鈕被點擊時 跟用戶再次確認
            function confirmDeletion() {
                const userInput = document.querySelector('input[name="delete_account"]').value;
                const correctInput = "<?php echo $user_name; ?>@delete"; // 用戶名+@delete的正確格式

                if(confirm("Are you sure you want to delete your account? This action cannot be undone.")){
                    if (userInput === correctInput){
                        return true;
                    }
                    else{
                        alert("The input does not match the required format.");
                        return false;
                    }
                }
                else{
                    return false;
                }
            }
        </script>

    </div>


</body>
</html>