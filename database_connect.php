<?php
$servername = "localhost";
$database_username = "root"; // 預設 root，無密碼
$database_password = "";
$database = "shop_system";

// 建立連線
$conn = new mysqli($servername, $database_username, $database_password, $database);

// 檢查連線
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}
?>
