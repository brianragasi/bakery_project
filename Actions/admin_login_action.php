<?php
include "../classes/User.php";

session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User();
    $loginResult = $user->login($email, $password);

    if (is_string($loginResult)) { // Login failed (error message returned)
        header("Location: ../views/admin_login.php?error=" . urlencode($loginResult)); 
        exit;
    } else {
        if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
            // User is an admin - redirect to admin dashboard
            header("Location: ../views/admin/admin_dashboard.php");
            exit;
        } else {
            // User is not an admin - redirect to an error page or user login
            session_destroy(); // Destroy the session to prevent unauthorized access
            header("Location: ../views/admin_login.php?error=" . urlencode("You are not authorized to access the admin panel."));
            exit;
        }
    } 
} else {
    header("Location: ../views/admin_login.php");
    exit; 
}
?>