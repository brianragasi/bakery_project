<?php
include "../classes/User.php"; 

$user = new User(); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $registrationResult = $user->register($name, $email, $password);

    // Redirect back to register.php with a message
    if (strpos($registrationResult, 'Error') === 0) {
        header("Location: ../views/register.php?error=" . urlencode($registrationResult)); 
    } else {
        header("Location: ../views/register.php?success=" . urlencode($registrationResult)); 
    }
    exit; 
} 
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $loginResult = $user->login($email, $password); 

    // Handle login result (redirect based on success or failure)
    if (is_string($loginResult)) { // Check if loginResult is an error message
        header("Location: ../views/login.php?error=" . urlencode($loginResult)); 
    } 
    // If login is successful, the user will be redirected within the login() method
    exit;
}
?>