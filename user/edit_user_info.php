<?php
    session_start();
    $user_name = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : null;
    $email = isset($_SESSION['EMAIL']) ? $_SESSION['EMAIL'] : null;
    $user_picture = isset($_SESSION['USER_PICTURE']) ? $_SESSION['USER_PICTURE'] : null;
    $fullname = isset($_SESSION['FULL_NAME']) ? $_SESSION['FULL_NAME'] : null;
    $address = isset($_SESSION['ADDRESS']) ? $_SESSION['ADDRESS'] : null;
    $phone = isset($_SESSION['PHONE']) ? $_SESSION['PHONE'] : null;
?>


<!-- 商品縮圖顯示 -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    previewImages();
}

function previewImages() {
    $allowedTypes = ["image/jpeg", "image/png", "image/gif"]; // 允許的圖片格式
    $base64Images = []; // 儲存 Base64 圖片

    if (!empty($_FILES["product_image"]["name"][0])) {
        foreach ($_FILES["product_image"]["tmp_name"] as $key => $tmp_name) {
            $fileType = $_FILES["product_image"]["type"][$key];

            // 檢查文件格式
            if (!in_array($fileType, $allowedTypes)) {
                echo "<script>alert('檔案格式不支援！請上傳 JPG, PNG, GIF 格式。');</script>";
                continue;
            }

            // 讀取圖片內容並轉為 Base64
            $imageData = file_get_contents($tmp_name);
            $base64Image = "data:$fileType;base64," . base64_encode($imageData);
            $base64Images[] = $base64Image;
        }
    }

    // 只顯示縮圖，不存入系統
    if (!empty($base64Images)) {
        echo '<div>';
        foreach ($base64Images as $image) {
            echo "<img src='$image' style='width: 100px; height: 100px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);'>";
        }
        echo "</div>";
    }
}
?>


<!DOCTYPE html>
<html>
    <head lang="en">
        <title>EDIT USER INFO</title>
        <link rel="stylesheet" type="text/css" href="./user_css/edit_user_info.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </head>

    <body>
        <div class="edit_user_info_wrapper">
            <div class="edit_user_info_word">EDIT</div>

            <!-- enctype 設定為 multipart/form-data 打開文件上傳功能 -->
            <!-- 尚未更新表單 -->
            <form id="register_form" action="./update_info.php" method="POST" enctype="multipart/form-data">

                <div class="username_word">Update user name</div>
                <div class="username_input_container">
                    <input class="register_user_name_input" placeholder="USER NAME" name="user_name" value="<?php echo trim($user_name) ?>" required/>
                </div>
                

                <div class="email_word">Update your email (account)</div>
                <div class="account_input_container">
                    <input class="register_user_account_input" placeholder="EMAIL (ACCOUNT)" name="user_account" value="<?php echo trim($email) ?>" required/>
                </div>
                


                <div class="photo_word">Update your user photo</div>
                <div id="hint_photo_word" class="hint_photo_word">HINT : If you don't want to update the photo. Don't select any image. </div>

                <!-- 顯示縮略圖 -->
                <div id="preview_container" class="preview_container"></div>

                <div class="photo_input_container">
                    <input id="register_user_photo_input" class="register_user_photo_input" placeholder="" name="user_photo" accept="image/*" type="file" />
                </div>
                
                <!-- 顯示縮略圖腳本 -->
                <script>
                    document.getElementById("register_user_photo_input").addEventListener("change", function(event) {
                        let previewContainer = document.getElementById("preview_container");
                        previewContainer.innerHTML = ""; // 清空之前的預覽
                        let files = event.target.files;

                        if (files.length > 0) {
                            for (let i = 0; i < files.length; i++) {
                                let file = files[i];

                                // 確保是圖片格式
                                if (file.type.startsWith("image/")) {
                                    let reader = new FileReader();
                                    reader.onload = function(e) {
                                        let img = document.createElement("img");
                                        img.src = e.target.result;
                                        img.style.width = "50px";
                                        img.style.height = "50px";
                                        img.style.objectFit = "cover";
                                        img.style.borderRadius = "10px";
                                        img.style.boxShadow = "0 2px 4px rgba(0,0,0,0.2)";
                                        previewContainer.appendChild(img);
                                    };
                                    reader.readAsDataURL(file);
                                }
                            }
                        }

                        // 有照片輸入 刪除hint
                        let hint = document.getElementById("hint_photo_word");
                        hint.remove();
                    });
                </script>



                <div class="fullname_word">Update your fullname</div>
                <div class="fullname_input_container">
                    <input class="register_user_fullname_input" placeholder="FULLNAME" name="user_fullname" value="<?php echo trim($fullname) ?>" required/>
                </div>


                <div class="address_word">Update your address</div>
                <div class="address_input_container">
                    <input class="register_user_address_input" placeholder="ADDRESS" name="user_address" value="<?php echo trim($address) ?>" required/>
                </div>

                <div class="phone_word">Update your phone number</div>
                <div class="phone_input_container">
                    <input class="register_user_phone_input" placeholder="PHONE NUMBER" name="user_phone" value="<?php echo trim($phone) ?>" required/>
                </div>


                <div class="edit_button_container">
                    <!-- submit:如果按鈕被點擊 表單執行相對應的操作(定義在action) -->                    
                    <button id="register_button" class="edit_button" type="submit">UPDATE INFORMATION</button>
                </div>

            </form>



            <!-- 暫時顯示密碼 (Input password 欄位)-->
            <script>
                const togglePassword = document.getElementById("register_user_password_input")
                const showPasswordButton = document.getElementById("show_passwords_button")
                const password_btn_icon = document.getElementById("password_button_icon")



                //指標按下時 顯示密碼(將type改為text)
                showPasswordButton.addEventListener('mousedown', function () {

                    event.preventDefault(); // 阻止按鈕默認行為
                    togglePassword.type = "text";

                    // 切換圖標
                    password_btn_icon.classList.remove('fa-eye-slash'); // 移除隱藏圖標
                    password_btn_icon.classList.add('fa-eye'); // 顯示密碼圖標
                    
                });

                //指標鬆開時 隱藏密碼(將type改回password)
                showPasswordButton.addEventListener('mouseup', function () {
                    event.preventDefault(); // 阻止按鈕默認行為
                    togglePassword.type = "password";

                    password_btn_icon.classList.remove('fa-eye'); // 移除顯示圖標
                    password_btn_icon.classList.add('fa-eye-slash'); // 隱藏密碼圖標
                })

                // 處理按鈕的 "mouseleave"（防止滑出按鈕時圖標狀態不正確）
                showPasswordButton.addEventListener('mouseleave', function () {
                    togglePassword.type = "password";
                    password_btn_icon.classList.remove('fa-eye');
                    password_btn_icon.classList.add('fa-eye-slash');
                });
            </script>


            <!-- 暫時顯示密碼 (confirm password 欄位) -->
            <script>
                const toggleConfirmPassword = document.getElementById("register_user_confirm_password_input")
                const showConfirmPasswordButton = document.getElementById("show_confirm_passwords_button")
                const confirm_password_btn_icon = document.getElementById("confirm_password_button_icon")



                //指標按下時 顯示密碼(將type改為text)
                showConfirmPasswordButton.addEventListener('mousedown', function () {

                    event.preventDefault(); // 阻止按鈕默認行為
                    toggleConfirmPassword.type = "text";

                    // 切換圖標
                    confirm_password_btn_icon.classList.remove('fa-eye-slash'); // 移除隱藏圖標
                    confirm_password_btn_icon.classList.add('fa-eye'); // 顯示密碼圖標
                    
                });

                //指標鬆開時 隱藏密碼(將type改回password)
                showConfirmPasswordButton.addEventListener('mouseup', function () {
                    event.preventDefault(); // 阻止按鈕默認行為
                    toggleConfirmPassword.type = "password";

                    confirm_password_btn_icon.classList.remove('fa-eye'); // 移除顯示圖標
                    confirm_password_btn_icon.classList.add('fa-eye-slash'); // 隱藏密碼圖標
                })

                // 處理按鈕的 "mouseleave"（防止滑出按鈕時圖標狀態不正確）
                showConfirmPasswordButton.addEventListener('mouseleave', function () {
                    toggleConfirmPassword.type = "password";
                    confirm_password_btn_icon.classList.remove('fa-eye');
                    confirm_password_btn_icon.classList.add('fa-eye-slash');
                });
            </script>


            <!-- 確保兩次輸入的密碼相同 -->
            <script>
                // 當 HTML 完全加載並解析後，執行 JavaScript 程式碼
                document.addEventListener("DOMContentLoaded", function () {
                    
                    const form = document.getElementById("register_form");
                    const register_btn = document.getElementById("register_button");
                    const password = document.getElementById("register_user_password_input");
                    const confirm_password = document.getElementById("register_user_confirm_password_input");
                    const required_fields = document.querySelectorAll("[required]");


                    form.addEventListener('submit', function (event) {
                        // 確保所有欄位都已填寫才驗證密碼
                        for (let field of required_fields) {
                            if (!field.value.trim()) {
                                alert("Please fill in all required fields.");
                                field.focus();  // 將焦點移到第一個未填寫的欄位
                                event.preventDefault(); // 阻止表單提交
                                return false;
                            }
                        }


                        // 驗證兩次密碼相同
                        if (password.value !== confirm_password.value) {
                            alert("The passwords you entered twice are different. Please confirm your password again.");
                            confirm_password.focus();  // 將焦點移到確認密碼輸入框
                            event.preventDefault(); // 阻止表單提交
                            return false;
                        }
                    });
                });
            </script>



        </div>

    </body>

</html>