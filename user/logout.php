<?php
session_start();
session_destroy(); // 清除所有 session
header("Location: http://shop_system.com"); // 登出後跳轉
exit();
?>
