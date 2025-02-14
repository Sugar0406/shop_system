<?php
    session_start();
    // 假設登入後，你將使用者的名稱和頭像儲存在 Session 中
    // 如果未登入，則這些 session 變數不會設置
    $user_id = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : null;
    $user_name = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : null;
    $user_picture = isset($_SESSION['USER_PICTURE']) ? $_SESSION['USER_PICTURE'] : null;
?>

<?php

    include "./show_product.php";

?>



<!DOCTYPE html>
<html>
    <head>
        <title>E-SHOP SYSTEM</title>
        <link rel="stylesheet" type="text/css" href="./main_css/main.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

        <!-- 寬度為裝置寬度 縮放為1倍 -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    
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
                    var newUrl = new URL(currentUrl); 
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


        <!-- 商品展示 -->
        <div class = "product_section">

            <!-- 分類選取 -->
            <div class="search_product_by_category_section">
                <button class="category-btn" onclick="redirectToCategory('All category')"><i class="fa-solid fa-bars"></i> All category</button>
                <button class="category-btn" onclick="redirectToCategory('Electronics & Accessories')"><i class="fa-solid fa-tv"></i> Electronics & Accessories</button>
                <button class="category-btn" onclick="redirectToCategory('Home Appliances & Living Essentials')"><i class="fa-solid fa-couch"></i> Home Appliances & Living Essentials</button>
                <button class="category-btn" onclick="redirectToCategory('Clothing & Accessories')"><i class="fa-solid fa-shirt"></i> Clothing & Accessories</button>
                <button class="category-btn" onclick="redirectToCategory('Beauty & Personal Care')"><i class="fa-solid fa-pump-soap"></i> Beauty & Personal Care</button>
                <button class="category-btn" onclick="redirectToCategory('Food & Beverages')"><i class="fa-solid fa-utensils"></i> Food & Beverages</button>
                <button class="category-btn" onclick="redirectToCategory('Home & Furniture')"><i class="fa-solid fa-bed"></i> Home & Furniture</button>
                <button class="category-btn" onclick="redirectToCategory('Sports & Outdoor Equipment')"><i class="fa-solid fa-football"></i> Sports & Outdoor Equipment</button>
                <button class="category-btn" onclick="redirectToCategory('Automotive & Motorcycle Accessories')"><i class="fa-solid fa-car"></i> Automotive & Motorcycle Access </button>
                <button class="category-btn" onclick="redirectToCategory('Baby & Maternity Products')"><i class="fa-solid fa-baby"></i> Baby & Maternity Products  </button>
                <button class="category-btn" onclick="redirectToCategory('Books & Office Supplies')"><i class="fa-solid fa-book"></i> Books & Office Supplies  </button>
                <button class="category-btn" onclick="redirectToCategory('Other')"><i class="fa-solid fa-box"></i> Other  </button>
            </div>
            <!-- 分類腳本 -->
            <script>
                function redirectToCategory( selectedCategory ){
                    var currentUrl = window.location.href; 
                    var newUrl = new URL(currentUrl); 

                    // 檢查是否已經有 selected_category 參數，若有則更新它，否則新增
                    if (newUrl.searchParams.has('selected_category')) {
                        newUrl.searchParams.set('selected_category', selectedCategory);
                    } 
                    else {
                        newUrl.searchParams.append('selected_category', selectedCategory);
                    }

                    // 檢查是否已經有 page 參數，設定為第一頁
                    if (newUrl.searchParams.has('page')) {
                        newUrl.searchParams.set('page', 1);
                    }
                    else{
                        newUrl.searchParams.append('page', 1);
                    }

                    window.location.href = newUrl.toString();
                }
            </script>




            <!-- 商品展示 -->
            <div class = "product_container">
                <!-- 生成product card -->
                <?php foreach ($products as $product): ?>
                    <div class="product_card"  onclick="window.location.href='./product/product_details.php?product_id=<?php echo $product['PRODUCT_ID']; ?>'" >
                        
                        <!-- product image -->
                        <div class="product_image_slider">
                            <?php 
                                // 解碼圖片陣列
                                $images = json_decode($product['PRODUCT_IMAGE']);
                                $totalImages = count($images);
                            ?>
                            <!-- 預設顯示第一張圖片 -->
                            <img src="./product_img/<?php echo $product['PRODUCT_ID']; ?>/<?php echo $images[0]; ?>" alt="Product Image" class="product_image" data-product-id="<?php echo $product['PRODUCT_ID']; ?>" id="product_image_<?php echo $product['PRODUCT_ID']; ?>_0">

                            <!-- 顯示其他圖片 -->
                            <?php for ($i = 1; $i < $totalImages; $i++): ?>
                                <img src="./product_img/<?php echo $product['PRODUCT_ID']; ?>/<?php echo $images[$i]; ?>" alt="Product Image" class="product_image" data-product-id="<?php echo $product['PRODUCT_ID']; ?>" id="product_image_<?php echo $product['PRODUCT_ID']; ?>_<?php echo $i; ?>" style="display:none;">
                            <?php endfor; ?>
                            
                            <!-- 左右切換按鈕 -->
                            <button class="prev" onclick="changeImage('<?php echo $product['PRODUCT_ID']; ?>', -1)">&#10094;</button>
                            <button class="next" onclick="changeImage('<?php echo $product['PRODUCT_ID']; ?>', 1)">&#10095;</button>
                        </div>

                        <!-- product info -->
                        <div class="product_info">
                            
                            <p class="product_name"><?php echo htmlspecialchars($product['PRODUCT_NAME']); ?></a>

                            <p class="product_category">Category: <?php echo htmlspecialchars($product['CATEGORY']); ?></p>
                            <p class="product_price">Price: $<?php echo number_format($product['PRICE']); ?></p>
                            <p class="product_in_stock"><?php echo $product['IN_STOCK']; ?> in stock</p>

                            <!-- 此處的USER_ID 為商品賣家的ID 而非當前使用者的ID -->
                            <p class="product_seller">Seller: <?php echo $product['USER_NAME']; ?></p>

                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- 上一頁 / 下一頁按鈕 -->
            <div id="pagination" class="pagination">
                <button id="prevBtn" onclick="prevPage()">&#10094; Prev</button>
                <button id="nextBtn" onclick="nextPage()">Next &#10095;</button>
            </div>


            <!-- 商品多圖片切換腳本 -->
            <script>
                function changeImage(productId, direction) {
                    const images = document.querySelectorAll(  " [data-product-id=\"" + productId + "\"]"  )

                    // const images = document.querySelectorAll("#product_image_" + productId);
                    let currentIndex = 0;

                    // 阻止事件冒泡，避免觸發product card 的 onclick
                    event.stopPropagation()
                    
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


            <!-- 多商品頁面切換腳本 -->
            <script>
                const urlParams = new URLSearchParams(window.location.search);
                const page =  parseInt(urlParams.get('page')) || 1;  // 默認為第 1 頁
                const max_pages = <?php echo (int)$max_pages;?>;  

                //初始為第一頁
                if (page === null){
                    page=1;
                }

                // 如果查無商品 $max_pages=0 隱藏所有pagenation button
                // 新增查無商品提示 (p tag)
                if(max_pages === 0){
                    document.getElementById('prevBtn').style.display = "none";
                    document.getElementById('nextBtn').style.display = "none";
                    
                    var noProductMsg = document.createElement("p");         // 建立 <p> 標籤
                    noProductMsg.innerText = "No products found.";          // 設定文字內容
                    noProductMsg.style.color = "red";                       // 設定文字顏色
                    noProductMsg.style.textAlign = "center";                // 讓文字置中
                    noProductMsg.style.fontFamily = "Verdana, sans-serif"   // 設定字形
                    noProductMsg.style.fontSize = "18px";                   // 調整字體大小

                    // 將 <p> 加入到特定區塊內（例如 <div id="productList">）
                    document.getElementById("pagination").appendChild(noProductMsg);

                }

                // 如果是第一頁 隱藏prev button
                if(page === 1){
                    document.getElementById('prevBtn').style.display = "none";
                }

                // 如果是最後一頁 隱藏next button
                if(page === max_pages){
                    document.getElementById('nextBtn').style.display = "none";
                }


                // 切換到上一頁
                function prevPage() {

                    // page不是第一頁才有效
                    if (page != 1){
                        urlParams.set('page', page-1);
                        window.location.href = '?' + urlParams.toString();
                    }

                }

                // 切換到下一頁
                function nextPage() {

                    const total_page = <?php echo (int)$max_pages; ?>;

                    // page不是最後一頁才有效
                    if ( page < total_page ){
                        urlParams.set('page', page+1);
                        window.location.href = '?' + urlParams.toString();
                    }

                }
            </script>


        </div>
    
    </body>

</html>