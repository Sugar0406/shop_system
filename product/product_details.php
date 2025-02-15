<?php
    session_start();
    // 假設登入後，你將使用者的名稱和頭像儲存在 Session 中
    // 如果未登入，則這些 session 變數不會設置
    $user_id = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : null;
    $user_name = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : null;
    $user_picture = isset($_SESSION['USER_PICTURE']) ? $_SESSION['USER_PICTURE'] : null;
?>


<!-- 取得商品詳請 -->
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
        $get_product_detail_sql = "SELECT p.*, u.USER_NAME AS SELLER_NAME FROM products p LEFT JOIN users u ON p.SELLER_ID = u.USER_ID WHERE p.PRODUCT_ID = ?";
        $get_product_detail_stmt = $conn->prepare($get_product_detail_sql);
        $get_product_detail_stmt->bind_param("s", $product_id);
        if($get_product_detail_stmt->execute()){
            $product_detail_result = $get_product_detail_stmt->get_result()->fetch_assoc();
            $conn->close();
            $get_product_detail_stmt->close();
        }
        else{
            $conn->close();
            $get_product_detail_stmt->close();
            echo "<script>
                alert('Not found the product! Please try again later.');
                window.location.href = 'http://shop_system.com';
            </script>";
        }
    }
?>




<html>
    <head>
        <link rel="stylesheet" href="./product_css/product_details.css" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <title><?php echo $product_detail_result["PRODUCT_NAME"] ?></title>
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
                        <a href="../shopping_cart.php" class="cart_button">
                            <i class="fa fa-shopping-cart"></i>
                        </a>
                        <a href="http://shop_system.com/user/user_info.php#my_account" class="user_name"><?php echo $user_name; ?></a>
                        <img src="<?php echo  "../customer/customer_img/" . $user_picture; ?>" alt="User picture" class="user_picture">
                    </div>

                <?php else: ?>
                    <a class="login_word" href="./customer/login.html">Login</a>
                    <div class="login_register_divider">/</div>
                    <a class="register_word" href="./customer/register.php">Register</a>
                <?php endif; ?>
            </div>
            
        </div>


        <!-- 返回上一页按钮 -->
        <div class="back_to_home_page_wrapper">
            <button onclick="redirectToMainPage()" class="back_button">&#8636; Back</button>
        </div>
        <script>
            function redirectToMainPage() {
                window.history.back();
            }
        </script>


        <div class="product_detail_container">

                <!-- product image -->
                <div class="product_image_slider">
                    <?php 
                        // 解碼圖片陣列
                        $images = json_decode($product_detail_result['PRODUCT_IMAGE']);
                        $totalImages = count($images);
                    ?>

                    <!-- 預設顯示第一張圖片 -->
                    <img src="../product_img/<?php echo $product_detail_result['PRODUCT_ID']; ?>/<?php echo $images[0]; ?>" alt="Product Image" class="product_image" data-product-id="<?php echo $product_detail_result['PRODUCT_ID']; ?>" id="product_image_<?php echo $product_detail_result['PRODUCT_ID']; ?>_0">

                    <!-- 顯示其他圖片 -->
                    <?php for ($i = 1; $i < $totalImages; $i++): ?>
                        <img src="../product_img/<?php echo $product_detail_result['PRODUCT_ID']; ?>/<?php echo $images[$i]; ?>" alt="Product Image" class="product_image" data-product-id="<?php echo $product_detail_result['PRODUCT_ID']; ?>" id="product_image_<?php echo $product_detail_result['PRODUCT_ID']; ?>_<?php echo $i; ?>" style="display:none;">
                    <?php endfor; ?>
                    
                    <!-- 左右切換按鈕 -->
                    <button class="prev" onclick="changeImage('<?php echo $product_detail_result['PRODUCT_ID']; ?>', -1)">&#10094;</button>
                    <button class="next" onclick="changeImage('<?php echo $product_detail_result['PRODUCT_ID']; ?>', 1)">&#10095;</button>

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
                
                </div>

                <!-- product info -->
                <div class="product_info_wrapper">
                    <div class="product_name"><?php echo $product_detail_result["PRODUCT_NAME"];?></div>
                    <div class="seller_name">Seller : <?php echo $product_detail_result["SELLER_NAME"]?></div>
                    <div class="price">$<?php echo $product_detail_result["PRICE"];?></div>


                    <div class="description_container">
                        <div class="description_label">Product Description</div>
                        <textarea class="description"><?php echo $product_detail_result["DESCRIPTION"]?></textarea>
                    </div>


                    <div class="input_quantity_container">
                        <button id="decrease_button" class="decrease_button" onclick="decrease()">-</button>

                        <!-- oninput="value=value.replace(/[^\d]/g,'')" 限制只能數字輸入 -->
                        <input id="number_input_field" class="number_input_field" type="text" value="1" oninput="value=value.replace(/[^\d]/g,'')">
                        
                        <button id="increase_button" class="increase_button" onclick="increase()">+</button>

                        <span class="in_stock"><?php echo $product_detail_result["IN_STOCK"] ?> in stock</span>



                        <script>

                            const input = document.getElementById("number_input_field");
                            const increaseBtn = document.getElementById("increase_button");
                            const decreaseBtn = document.getElementById("decrease_button");


                            document.addEventListener("DOMContentLoaded", function() {

                                // 載入時庫存量為1，顯示最大庫存提醒
                                if (<?php echo (int)$product_detail_result["IN_STOCK"]; ?> == 1 ){
                                    hint = document.getElementById("hint_max_input");
                                    hint.style.display = "block";
                                }

                                input.addEventListener("input", function(){

                                    let nowvalue = parseInt(input.value);
                                    let maxvalue = <?php echo (int)$product_detail_result["IN_STOCK"]; ?>

                                    // input非數字或小於1時 設定為1
                                    if (isNaN(nowvalue) || nowvalue < 1) {
                                        this.value = 1;
                                    } 
                                    // 數量超過庫存 設定為庫存
                                    else if (nowvalue > maxvalue) {
                                        this.value = maxvalue;
                                    }

                                    // 最大購買提醒控制
                                    if (nowvalue >= maxvalue){
                                        hint = document.getElementById("hint_max_input")
                                        hint.style.display = "block"
                                    }
                                    else{
                                        hint = document.getElementById("hint_max_input")
                                        hint.style.display = "none";
                                    }

                                });
                            });


                            function increase(){
                                let nowvalue = parseInt(input.value);

                                // 小於庫存才能++
                                if(nowvalue < <?php  echo (int)$product_detail_result["IN_STOCK"]; ?>){
                                    nowvalue++;
                                    input.value = nowvalue;
                                    
                                    // 若當前書入值等於庫存時，顯示提醒
                                    if(nowvalue >= <?php  echo (int)$product_detail_result["IN_STOCK"]; ?>){
                                        hint = document.getElementById("hint_max_input")
                                        hint.style.display = "block"
                                    }
                                }
                            }


                            function decrease(){
                                let nowvalue = parseInt(input.value);

                                // 不能小於 1
                                if(nowvalue > 1){
                                    nowvalue--;
                                    input.value = nowvalue;

                                    // 若當前書入值小於庫存時，隱藏提醒
                                    if(nowvalue < <?php  echo (int)$product_detail_result["IN_STOCK"]; ?>){
                                        hint = document.getElementById("hint_max_input");
                                        hint.style.display = "none";
                                    }
                                }

                            }

                        </script>
                    </div>
                    <!-- 提醒使用者當前輸入值為最大購買量 -->
                    <div id="hint_max_input" class="hint_max_input">Maximum purchase limit.</div>



                    <div class="add_to_cart_button_container">
                    <!-- form 搭配 hidden input 傳遞 JSON 字串 -->
                    <form id="cartForm" action="../add_to_shopping_cart.php" method="POST">
                        <input type="hidden" name="product" id="product">
                        <button class="add_to_cart_button" onclick="addToCart()">Add to Cart</button>
                        <script>
                            function addToCart() {

                                
                                let buyer_id = "<?php echo $user_id ;?>"; // 當 $user_id 為 null 時，等同於 buyer_id = "";
                                let productId = "<?php echo $product_detail_result['PRODUCT_ID'];?>";
                                let price = "<?php echo $product_detail_result['PRICE'];?>"; 
                                let sellerId = "<?php echo $product_detail_result['SELLER_ID'];?>";
                                let quantity = document.getElementById("number_input_field").value;

                                // 未登入時，引導到loginpage
                                if ( buyer_id === "") {
                                    event.preventDefault(); // 避免表單提交
                                    alert("Please login first.");
                                    window.location.href="../customer/login.html";
                                    return; 
                                }

                                // 登入時 加入購物車
                                else{
                                    // alert( 
                                    //     "buyerID : " + buyer_id + "\n"
                                    //     + "productId : " + productId + "\n"
                                    //     + "quantity : " + quantity + "\n"
                                    //     + "SellerId : " + sellerId + "\n"
                                    // )

                                    product = {};
                                    product.productId = productId;
                                    product.price = price;
                                    product.quantity = quantity;
                                    product.sellerId = sellerId;
                                    product.buyerId = buyer_id;

                                    document.getElementById('product').value = JSON.stringify(product);

                                    // 提交表單
                                    document.getElementById('cartForm').submit();


                                }
                            }
                        </script>
                    </form>
                    </div>
                </div>

        </div>


        
    </body>
</html>