<?php

include "../database_connect.php";

session_start();
$user_id = $_SESSION["USER_ID"];


$origin_password = $_POST["origin_password"];
$new_password = $_POST["new_password"];
$confirm_new_password = $_POST["confirm_new_password"];

// verify form post content
// echo "origin_password: " . $origin_password . "\n";
// echo "new_password: " . $new_password . "\n";
// echo "confirm_new_password: " . $confirm_new_password . "\n";

$search_password_sql = "SELECT HASH_PASSWORD FROM users WHERE USER_ID = ? ";
$search_password_stml = $conn->prepare($search_password_sql);
$search_password_stml->bind_param("s", $user_id);
$search_password_stml->execute();

$search_password_result = $search_password_stml->get_result();
$search_password_result = $search_password_result->fetch_assoc();

$oldHashPassword_from_db = $search_password_result["HASH_PASSWORD"];


$search_password_stml->close();

if (password_verify($origin_password, $oldHashPassword_from_db)) {

    if ( $new_password != $confirm_new_password){

        $conn -> close();

        echo "<script>
            alert('New password and confirm password are different');
            window.location.href = 'http://shop_system.com/user/user_info.php';
        </script>";
    }
    else{
        $newHashPassword = password_hash($new_password, PASSWORD_BCRYPT);

        $update_password_sql = "UPDATE users SET HASH_PASSWORD = ? WHERE USER_ID = ?";
        $update_password_stml = $conn->prepare($update_password_sql);
        $update_password_stml->bind_param("ss", $newHashPassword, $user_id);
        
        if( $update_password_stml->execute() ){
            
            $update_password_stml->close();
            $conn ->close();

            echo "<script>
                alert('Password changed successfully');
                window.location.href = 'http://shop_system.com/user/user_info.php';
            </script>";
            
        }
        else{

            $update_password_stml->close();
            $conn ->close();

            echo "<script>
                alert('Update Password failed!\\nERROR: " . addslashes($update_password_stmt->error) . "');
                window.location.href = 'http://shop_system.com/customer/register.html';
            </script>";
        }



    }


} else {

    $conn->close();
    
    echo "<script>
            alert('Old Password error!');
            window.location.href = 'http://shop_system.com/user/user_info.php';
        </script>";
}



?>