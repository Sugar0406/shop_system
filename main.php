<?php
session_start();

// 假設登入後，你將使用者的名稱和頭像儲存在 Session 中
// 如果未登入，則這些 session 變數不會設置
$user_name = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : null;
$user_picture = isset($_SESSION['USER_PICTURE']) ? $_SESSION['USER_PICTURE'] : null;
?>



<!DOCTYPE html>
<html>
    <head>
        <title>E-SHOP SYSTEM</title>
        <link rel="stylesheet" type="text/css" href="main_css/main_style.css">

        <!-- 寬度為裝置寬度 縮放為1倍 -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    
    </head>

    <body>

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
                    <a href="http://shop_system.com/user/user_info.php" class="user_name"><?php echo $user_name; ?></a>
                    <img src="<?php echo  "./customer/customer_img/" . $user_picture; ?>" alt="User picture" class="user_picture">
                    
                </div>

                <?php else: ?>
                    <a class="login_word" href="./customer/login.html">Login</a>
                    <div class="login_register_divider">/</div>
                    <a class="register_word" href="./customer/register.php">Register</a>
                <?php endif; ?>
            </div>
            
        </div>
    
    
    
    
    
    </body>

</html>