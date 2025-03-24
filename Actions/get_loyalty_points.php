<?php
include_once "../classes/Database.php";
include_once "../classes/User.php";

session_start();

if (isset($_SESSION['user_id'])) {
    $user = new User();
    $loyaltyPoints = $user->getLoyaltyPoints($_SESSION['user_id']);
    echo $loyaltyPoints; 
} else {
    echo "0"; // Or handle the case where the user is not logged in.
}
?>