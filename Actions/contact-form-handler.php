<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // (Optional) You can add validation and sanitization here 
    // if you still want to practice those aspects, even for 
    // the demonstration version.

    // Redirect back to the contact page with a success message (simulated)
    header("Location: ../views/user/contact.php?success=1"); 
    exit(); 

} else {
    // Redirect to contact page if accessed directly without form submission
    header("Location: ../views/user/contact.php");
    exit(); 
}
?>