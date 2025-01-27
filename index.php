<?php

//將原網址重新導向到指定的php檔案 
header("Location: /main.html");





// $forbidden_files = ['secret.php', 'config.php']; // 禁止访问的文件名

// // 如果用户试图访问被禁止的文件
// if (in_array(basename($_SERVER['PHP_SELF']), $forbidden_files)) {
//     header('HTTP/1.0 403 Forbidden');
//     echo '403 Forbidden - You are not allowed to access this file.';
//     exit;
// }

?>