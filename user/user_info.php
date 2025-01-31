<?php
session_start();

$user_name = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : null;
$user_picture = isset($_SESSION['USER_PICTURE']) ? $_SESSION['USER_PICTURE'] : null;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USER INFO</title>
    <style>
        /* 主要內容區域 */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        /* 隱藏未選中的內容 */
        .content-section {
            display: none;
        }

        /* 顯示當前選中的區域 */
        .active {
            display: block;
        }
    </style>
    <link rel="stylesheet" href="user_css/info.css">

</head>
<body>

    <!-- 左側菜單欄 -->
    <div class="sidebar">
        <div class="user_info">
            <img src="<?php echo  "../customer/customer_img/" . $user_picture; ?>" alt="User picture" class="user_picture">
            <span class="user_name"><?php echo $user_name; ?></span>
        </div>
        <ul>
            <li><a href="#" data-section="my_account">My Account</a></li>
            <li><a href="#" data-section="change_password">Change Password</a></li>
            <li><a href="#" data-section="my_purchase">My Purchase</a></li>
            <li><a href="#" data-section="my_product">My Product</a></li>

            <!-- logout 直接跳轉 不需要section -->
            <li><a href="./logout.php" >Log Out</a></li>
        </ul>
    </div>

    <!-- 主要內容區域 -->
    <div class="main-content">

        <!-- My Account -->
        <div id="my_account" class="content-section active">
            <h1>My Account</h1>

        </div>


        <!-- Change Password -->
        <div id="change_password" class="content-section">
            <h1>Change Password</h1>

        </div>
       
        
        <!-- My Purchase -->
        <div id="my_purchase" class="content-section">
            <h1>My Purchase</h1>
        </div>


        <!-- My Product -->
        <div id="my_product" class="content-section">
            <h1>My Product</h1>
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