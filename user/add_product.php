<?php
    session_start();
    $user_id = $_SESSION['USER_ID'];
?>

<!-- 使用者縮圖顯示 -->
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



<!-- 頁面html -->
<html>
    <head>
        <title>ADD PRODUCT</title>
        <link rel="stylesheet" type="text/css" href="./user_css/add_product.css">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <div class = "add_product_wrapper">
            <div class="add_product_word">ADD NEW PRODUCT</div>
            <form id="product_form" action="./upload_product_info.php" method="POST" enctype="multipart/form-data">
                
                <!-- 商品名稱 商品名稱 商品名稱 商品名稱 商品名稱 商品名稱 商品名稱 -->
                <div class="product_name_wrapper">
                    <div class="product_name_word">Input product name</div>
                    <div class="product_name_input_container">
                        <input type="text" id="product_name_input" class="product_name_input" name="product_name" placeholder="PRODUCT NAME" required>
                    </div>
                </div>

                <!-- 商品圖片 商品圖片 商品圖片 商品圖片 商品圖片 商品圖片 商品圖片 -->
                <div class="product_image_wrapper">
                    <div class="product_image_word">Upload product image</div>
                    
                    <!-- 顯示縮略圖 -->
                    <div id="preview_container" class="preview_container"></div>
                    
                    <div class="product_image_input_container">
                        <input type="file" id="product_image_input" class="product_image_input" name="product_image[]" accept="image/*" multiple required>
                    </div>
                </div>

                <!-- 價格 價格 價格 價格 價格 價格 價格 價格 價格 價格 價格 價格 價格 價格 價格 -->
                <div class="price_wrapper">
                    <div class="price_word">Setting price</div>    
                    <div class="price_input_container">  
                        <input type="number" id="price_input" class="price_input" name="price" placeholder="PRICE" required>
                    </div>      
                </div>

                <!-- 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存  -->
                <div class="in_stock_wrapper">
                    <div class="in_stock_word">Product inventory</div>    
                    <div class="in_stock_input_container">  
                        <input type="number" id="in_stock_input" class="in_stock_input" name="in_stock" placeholder="PRODUCT INVENTORY" required>
                    </div>      
                </div>

                <!-- 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 -->
                <div class="description_wrapper">
                    <div class="description_word">Setting product description</div>
                    <div class="description_input_container">
                        <textarea id="description_textarea" class="description_textarea" name="description" placeholder="PRODUCT DESCRIPTION" required></textarea>
                    </div>
                </div>

                <!-- 商品分類 商品分類 商品分類 商品分類 商品分類 商品分類 商品分類 商品分類 商品分類 商品分類 -->
                <div class="category_wrapper">
                    <div class="category_word">Setting product category</div>
                    <div class="category_select_container">
                        <select id="category_select" class="category_select" name="category" required>
                            <option value="" selected disabled>Select a category</option>
                            <option value="Electronics & Accessories">Electronics & Accessories</option>
                            <option value="Home Appliances & Living Essentials">Home Appliances & Living Essentials</option>
                            <option value="Clothing & Accessories">Clothing & Accessories</option>
                            <option value="Beauty & Personal Care">Beauty & Personal Care</option>
                            <option value="Food & Beverages">Food & Beverages</option>
                            <option value="Home & Furniture">Home & Furniture</option>
                            <option value="Sports & Outdoor Equipment">Sports & Outdoor Equipment</option>
                            <option value="Automotive & Motorcycle Accessories">Automotive & Motorcycle Accessories</option>
                            <option value="Baby & Maternity Products">Baby & Maternity Products</option>
                            <option value="Books & Office Supplies">Books & Office Supplies</option>
                            <option value="Other">Other</option>
                        </select>
                </div>


            <!-- 提交按鈕 提交按鈕 提交按鈕 提交按鈕 提交按鈕 提交按鈕 提交按鈕 提交按鈕 提交按鈕 -->
            <div class="add_product_button_container">
                <button type="submit" class="add_product_button" >ADD PRODUCT</button>
            </div>
            </form>


            <!-- 顯示縮略圖腳本 -->
            <script>
                document.getElementById("product_image_input").addEventListener("change", function(event) {
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
                });
            </script>
        </div>    
    </body>
</html>