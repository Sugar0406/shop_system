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

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 8; // 每頁顯示8個商品 (4*2 grid)
    $offset = ($page - 1) * $limit; // 偏移量

    // 分類預設為所有分類
    $product_category = isset($_GET['selected_category']) ? $_GET['selected_category'] : "All category";
    
    // 計算沒種情況的商品數
    $product_count = 0;


    // 未登入時查詢所有商品
    if ($user_id == null){

        // 無指定分類
        if ($product_category == "All category") {

            // 計算商品數
            $product_count_sql = "
                SELECT COUNT(*) 
                FROM products p
                LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                WHERE IN_STOCK > 0;
            ";
            $product_count_stmt = $conn->prepare($product_count_sql);
            $product_count_stmt->execute();
            $product_count_result = $product_count_stmt->get_result();
            $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數


            // 查詢商品資訊
            $get_product_sql = "
                SELECT p.*, u.USER_NAME 
                FROM products p 
                LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                WHERE p.IN_STOCK > 0
                ORDER BY p.UPDATE_AT DESC
                LIMIT ? OFFSET ?
            ";
            $get_product_stmt = $conn->prepare($get_product_sql);
            $get_product_stmt->bind_param("ii", $limit, $offset); 
        } 
        // 有指定分類
        else {
            // 計算商品數
            $product_count_sql = "
                SELECT COUNT(*) 
                FROM products p
                LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                WHERE IN_STOCK > 0 AND p.CATEGORY = ?;
            ";
            $product_count_stmt = $conn->prepare($product_count_sql);
            $product_count_stmt->bind_param("s", $product_category); 
            $product_count_stmt->execute();
            $product_count_result = $product_count_stmt->get_result();
            $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數
            
            // 查詢商品
            $get_product_sql = "
                SELECT p.*, u.USER_NAME 
                FROM products p 
                LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                WHERE p.IN_STOCK > 0  AND p.CATEGORY = ?
                ORDER BY p.UPDATE_AT DESC
                LIMIT ? OFFSET ?
            ";
            $get_product_stmt = $conn->prepare($get_product_sql);
            $get_product_stmt->bind_param("sii", $product_category, $limit, $offset); 
        }

    }
    // 已登入時排除自己的商品
    else{

        // 無指定分類，並排除當前使用者的上架商品
        if ($product_category == "All category") {

            // 計算商品數
            $product_count_sql = "
                SELECT COUNT(*) 
                FROM products p
                LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                WHERE IN_STOCK > 0 AND p.SELLER_ID != ?;
            ";
            $product_count_stmt = $conn->prepare($product_count_sql);
            $product_count_stmt->bind_param("s", $user_id); 
            $product_count_stmt->execute();
            $product_count_result = $product_count_stmt->get_result();
            $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數


            // 查詢商品
            $get_product_sql = "
                SELECT p.*, u.USER_NAME 
                FROM products p 
                LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                WHERE p.IN_STOCK > 0 AND p.SELLER_ID != ?
                ORDER BY p.UPDATE_AT DESC
                LIMIT ? OFFSET ?
            ";
            $get_product_stmt = $conn->prepare($get_product_sql);
            $get_product_stmt->bind_param("sii", $user_id, $limit, $offset); 
        } 
        // 有指定分類，並排除當前使用者的上架商品
        else {

            // 計算商品數
            $product_count_sql = "
                SELECT COUNT(*) 
                FROM products p
                LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                WHERE IN_STOCK > 0 AND p.CATEGORY = ? AND p.SELLER_ID != ?;
            ";
            $product_count_stmt = $conn->prepare($product_count_sql);
            $product_count_stmt->bind_param("ss", $product_category, $user_id); 
            $product_count_stmt->execute();
            $product_count_result = $product_count_stmt->get_result();
            $product_count = $product_count_result->fetch_row()[0]; // 取得總商品數

            // 查詢商品
            $get_product_sql = "
                SELECT p.*, u.USER_NAME 
                FROM products p 
                LEFT JOIN users u ON p.SELLER_ID = u.USER_ID
                WHERE p.IN_STOCK > 0  AND p.CATEGORY = ? AND p.SELLER_ID != ?
                ORDER BY p.UPDATE_AT DESC
                LIMIT ? OFFSET ?
            ";
            $get_product_stmt = $conn->prepare($get_product_sql);
            $get_product_stmt->bind_param("ssii", $product_category, $user_id, $limit, $offset); 
        }
    }

    //計算最大頁數
    //例如: 17個商品，頁數設定為1,2,3
    $max_pages = ceil($product_count / $limit);

    $get_product_stmt->execute();
    $get_product_result = $get_product_stmt->get_result();
    $products = [];
    //回傳結果不為空
    if($get_product_result){
        while ($row = $get_product_result->fetch_assoc()) {
            $products[] = $row;
        }
    }



    $get_product_stmt->close();
    $conn->close();

?>



<!DOCTYPE html>
<html>
    <head>
        <title>E-SHOP SYSTEM</title>
        <link rel="stylesheet" type="text/css" href="main_css/main_style.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

        <!-- 寬度為裝置寬度 縮放為1倍 -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    
    </head>

    <body>

        <!-- main banner -->
        <div class="main_banner">
            <div class="main_banner_title">E-shop system</div>

            <div class="inputbar_button_wrapper">
                <input class="eshop_search_bar"/>
                <button class="eshop_search_button" >Search</button>
            </div>


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
                <div class="search_product_by_category_title">Search Products by Category</div>
                <select class="search_product_by_category_selection" id="search_product_by_category_selection">
                    <option value="All category" <?php if ($product_category == 'All category') echo'selected';?>>All category</option>
                    <option value="Electronics & Accessories" <?php if ($product_category == 'Electronics & Accessories') echo 'selected'; ?>>Electronics & Accessories</option>
                    <option value="Home Appliances & Living Essentials" <?php if ($product_category == 'Home Appliances & Living Essentials') echo 'selected'; ?>>Home Appliances & Living Essentials</option>
                    <option value="Clothing & Accessories" <?php if ($product_category == 'Clothing & Accessories') echo 'selected'; ?>>Clothing & Accessories</option>
                    <option value="Beauty & Personal Care" <?php if ($product_category == 'Beauty & Personal Care') echo 'selected'; ?>>Beauty & Personal Care</option>
                    <option value="Food & Beverages" <?php if ($product_category == 'Food & Beverages') echo 'selected'; ?>>Food & Beverages</option>
                    <option value="Home & Furniture" <?php if ($product_category == 'Home & Furniture') echo 'selected'; ?>>Home & Furniture</option>
                    <option value="Sports & Outdoor Equipment" <?php if ($product_category == 'Sports & Outdoor Equipment') echo 'selected'; ?>>Sports & Outdoor Equipment</option>
                    <option value="Automotive & Motorcycle Accessories" <?php if ($product_category == 'Automotive & Motorcycle Accessories') echo 'selected'; ?>>Automotive & Motorcycle Accessories</option>
                    <option value="Baby & Maternity Products" <?php if ($product_category == 'Baby & Maternity Products') echo 'selected'; ?>>Baby & Maternity Products</option>
                    <option value="Books & Office Supplies" <?php if ($product_category == 'Books & Office Supplies') echo 'selected'; ?>>Books & Office Supplies</option>
                    <option value="Other" <?php if ($product_category == 'Other') echo 'selected'; ?>>Other</option>
                </select>

                <!-- 分類腳本 -->
                <script>
                    document.getElementById('search_product_by_category_selection').addEventListener('change', function() {
                        var selectedCategory = this.value; 
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
                    });
                </script>
            </div>

            <!-- 商品展示 -->
            <div class = "product_container">
                <!-- 生成product card -->
                <?php foreach ($products as $product): ?>
                    <div class="product_card" >
                        
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
                            <button class="prev" onclick="chang
                            eImage('<?php echo $product['PRODUCT_ID']; ?>', -1)">&#10094;</button>
                            <button class="next" onclick="changeImage('<?php echo $product['PRODUCT_ID']; ?>', 1)">&#10095;</button>
                        </div>

                        <!-- product info -->
                        <div class="product_info">
                            
                            <a href="./product/product_details.php?product_id=<?php echo $product['PRODUCT_ID']; ?>" class="product_name"><?php echo htmlspecialchars($product['PRODUCT_NAME']); ?></a>

                            <!-- 主頁不展示商品分類 -->
                            <!-- <p class="product_category">Product category: <?php //echo htmlspecialchars($product['CATEGORY']); ?></p> -->
                            <p class="product_price">Price: $<?php echo number_format($product['PRICE']); ?></p>
                            <p class="product_in_stock"><?php echo $product['IN_STOCK']; ?> in stock</p>

                            <!-- 此處的USER_ID 為商品賣家的ID 而非當前使用者的ID -->
                            <p class="product_seller">Seller: <?php echo $product['USER_NAME']; ?></p>

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


            <!-- 多商品頁面切換腳本 -->
            <script>
                const urlParams = new URLSearchParams(window.location.search);
                const page =  parseInt(urlParams.get('page')) || 1;  // 默認為第 1 頁
                const originalUrl = "http://shop_system.com/main"

                //初始為第一頁
                if (page === null){
                    page=1;
                }

                // 如果是第一頁 隱藏prev button
                if(page === 1){
                    document.getElementById('prevBtn').style.display = "none";
                }

                // 如果是最後一頁 隱藏next button
                if(page === <?php echo (int)$max_pages; ?>){
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