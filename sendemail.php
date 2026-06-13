<?php
// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Securely grab the data from the HTML form
    $name    = htmlspecialchars(strip_tags(trim($_POST["name"])));
    $email   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars(strip_tags(trim($_POST["address"])));
    $phone   = htmlspecialchars(strip_tags(trim($_POST["phone"])));
    
    // Grab the service dropdown selection
    $service = htmlspecialchars(strip_tags(trim($_POST["service"])));

    // Basic validation
    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Please fill out your name and a valid email address.");
    }

    // ---------------------------------------------------------
    // UPDATED: Now sending to the requested Hotmail address
    $recipient = "mitrasaibal@hotmail.com"; 
    // ---------------------------------------------------------

    $subject = "New Contact Form Submission from: $name";

    // Build the body of the email
    $email_content = "You have a new message from your website contact form.\n\n";
    $email_content .= "Name: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Phone: $phone\n";
    $email_content .= "Address: $address\n";
    $email_content .= "Service Requested: $service\n";

    // Email headers
    $email_headers = "From: $name <$email>\r\n";
    $email_headers .= "Reply-To: $email\r\n";

    // Send the email
    if (mail($recipient, $subject, $email_content, $email_headers)) {
        echo "Thank You! Your message has been sent successfully.";
    } else {
        echo "Oops! Something went wrong and we couldn't send your message.";
    }

} else {
    echo "There was a problem with your submission, please try again.";
}
?>