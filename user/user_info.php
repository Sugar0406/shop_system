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
            <li><a href="./logout.php" >Log Out</a></li>
        </ul>
    </div>

    <!-- 主要內容區域 -->
    <div class="main-content">

        <!-- My Account -->
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




        <!-- Change Password -->
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



        <!-- My Purchase -->
        <div id="my_purchase_wrapper" class="content-section">
            <h1 class="my_purchase_title">My Purchase</h1>

        </div>



        <!-- My Product -->
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
                    <!-- 當 category-display="true" 的 product_card 才能被頁面按鈕顯示 -->
                    <!-- category-display 由 search_product_category_select 腳本調控 -->
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
                            <a href="product_details.php?product_id=<?php echo $product['PRODUCT_ID']; ?>" class="product_name"><?php echo htmlspecialchars($product['PRODUCT_NAME']); ?></a>
                            <p class="product_category">Product category: <?php echo htmlspecialchars($product['CATEGORY']); ?></p>
                            <p class="product_price">Price: $<?php echo number_format($product['PRICE']); ?></p>
                            <p class="product_in_stock"><?php echo $product['IN_STOCK']; ?> in stock</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- 上一頁 / 下一頁按鈕 -->
            <div class="pagination">
                <button onclick="prevPage()">&#10094; Prev</button>
                <button onclick="nextPage()">Next &#10095;</button>
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

    </div>


    <!-- 單頁跳轉實現 -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 取得所有選單按鈕
            const menuItems = document.querySelectorAll(".sidebar ul li a");
            // 取得所有內容區塊
            const sections = document.querySelectorAll(".content-section");

            menuItems.forEach(item => {
                item.addEventListener("click", function(event) {
            
                    const sectionId = this.getAttribute("data-section");

                    // 如果是 Log Out，允許直接跳轉，不攔截點擊事件
                    if (!sectionId) {
                        alert("Logout Successfully");
                        return; 
                    }

                    event.preventDefault(); // 防止頁面重新載入

                    // 隱藏所有內容區塊
                    sections.forEach(section => {
                        section.classList.remove("active");
                    });

                    // 顯示被選中的內容
                    document.getElementById(sectionId).classList.add("active");
                });
            });
        });
    </script>


</body>
</html>