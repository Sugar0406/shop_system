<?php
    include "../database_connect.php";

    $search_product_by_id_sql = "SELECT * FROM products WHERE product_id = ?";
    $search_product_by_id_stmt = $conn->prepare($search_product_by_id_sql);
    $search_product_by_id_stmt->bind_param("s", $_GET["product_id"]);
    $search_product_by_id_stmt->execute();
    $search_product_by_id_result = $search_product_by_id_stmt->get_result();
    $search_product_by_id_row = $search_product_by_id_result->fetch_assoc();
    $search_product_by_id_stmt->close();
    $conn->close();

    $product_id = $_GET["product_id"];
    $product_name = $search_product_by_id_row["PRODUCT_NAME"];
    $product_category = $search_product_by_id_row["CATEGORY"];
    $product_description = $search_product_by_id_row["DESCRIPTION"];
    $product_in_stock = $search_product_by_id_row["IN_STOCK"];
    $product_price = $search_product_by_id_row["PRICE"];

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
        <link rel="stylesheet" type="text/css" href="./product_css/edit_product_info.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </head>

    <body>
        <div class="edit_product_info_wrapper">
            <div class="edit_product_info_word">EDIT</div>

            <!-- enctype 設定為 multipart/form-data 打開文件上傳功能 -->
            <form id="edit_product_form" action="./upload_new_product_info.php?product_id=<?php echo $product_id ?>" method="POST" enctype="multipart/form-data" >

                <!-- 商品名稱 商品名稱 商品名稱 商品名稱 商品名稱 商品名稱 商品名稱 -->
                <div class="edit_product_name_wrapper">
                    <div class="edit_product_name_word">Update product name</div>
                    <div class="edit_product_name_input_container">
                        <input class="edit_product_name_input" placeholder="FULLNAME" name="product_name" value="<?php echo trim($product_name) ?>" required/>
                    </div>
                </div>


                <!-- 商品圖片 商品圖片 商品圖片 商品圖片 商品圖片 商品圖片 商品圖片 -->
                <div class="edit_product_image_wrapper">
                    <div class="edit_product_image_word">Update product image</div>
                    <div id="hint_photo_word" class="hint_photo_word">HINT : If you don't want to update the photo. Don't select any image. </div>
                    
                    <!-- 顯示縮略圖 -->
                    <div id="preview_container" class="preview_container"></div>
                    
                    <div class="edit_product_image_input_container">
                        <input type="file" id="edit_product_image_input" class="edit_product_image_input" name="product_image[]" accept="image/*" multiple>
                    </div>
                </div>



                <!-- 商品價格 商品價格 商品價格 商品價格 商品價格 商品價格 商品價格 -->
                <div class="edit_product_price_wrapper">
                    <div class="edit_product_price_word">Update product price</div>
                    <div class="edit_product_price_input_container">
                        <input type="number" class="edit_product_price_input" placeholder="PRICE" name="price" value="<?php echo trim($product_price) ?>" required/>
                    </div>
                </div>



                <!-- 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存 庫存  -->
                <div class="edit_in_stock_wrapper">
                    <div class="edit_in_stock_word">Update product inventory</div>    
                    <div class="edit_in_stock_input_container">  
                        <input type="number" id="edit_in_stock_input" class="edit_in_stock_input" name="in_stock" placeholder="PRODUCT INVENTORY" value="<?php echo trim($product_in_stock) ?>" required>
                    </div>      
                </div>



                <!-- 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 商品描述 -->
                <div class="edit_description_wrapper">
                    <div class="edit_description_word">Update product description</div>
                    <div class="edit_description_input_container">
                        <textarea id="edit_description_textarea" class="edit_description_textarea" name="description" placeholder="PRODUCT DESCRIPTION" required><?php echo htmlspecialchars(trim($product_description)); ?></textarea>
                    </div>
                </div>



                <div class="edit_product_category_word">Change Product Category</div>
                <select class="edit_product_category" id="category_select" class="category_select" name="category" required>
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


                <div class="edit_button_container">
                    <!-- submit:如果按鈕被點擊 表單執行相對應的操作(定義在action) -->                    
                    <button id="edit_product_button" class="edit_button" type="submit">UPDATE INFORMATION</button>
                </div>

            </form>


            <!-- 顯示縮略圖腳本 -->
            <script>
            document.getElementById("edit_product_image_input").addEventListener("change", function(event) {
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