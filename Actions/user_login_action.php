<?php
include "../classes/User.php"; 

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_login'])) { 
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User(); 
    $loginResult = $user->login($email, $password); 

    if (is_string($loginResult)) { 
        header("Location: ../views/user_login.php?error=" . urlencode($loginResult)); 
        exit;
    } 
    // Successful login - redirect already handled in User->login()
} else {
    header("Location: ../views/user_login.php"); 
    exit;
}
?>