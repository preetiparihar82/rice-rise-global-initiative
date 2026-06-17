<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// =========================================================================
// ENVIRONMENT CONFIGURATION
// =========================================================================
// Choose your environment mode: 'godaddy_shared' or 'standard_smtp'
define('ENVIRONMENT', 'godaddy_shared'); 

$config = [
    'godaddy_shared' => [
        'host'        => 'localhost',
        'auth'        => false,
        'username'    => '',
        'password'    => '',
        'secure'      => '',
        'port'        => 25
    ],
    'standard_smtp' => [
        'host'        => 'mail.yourdomain.com', 
        'auth'        => true,
        'username'    => 'info@yourdomain.com',   
        'password'    => 'YourSecurePassword!',   
        'secure'      => PHPMailer::ENCRYPTION_SMTPS, 
        'port'        => 465
    ],
    'common' => [
        'from_email'  => 'noreply@yourdomain.com',
        'from_name'   => 'Website Contact Form',
        'recipient'   => 'rrgi5807.philippines@gmail.com'
    ]
];
// =========================================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
  // Securely capture and clean input data
    $name    = htmlspecialchars(strip_tags(trim($_POST["name"] ?? '')), ENT_QUOTES, 'UTF-8');
    $email   = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars(strip_tags(trim($_POST["address"] ?? '')), ENT_QUOTES, 'UTF-8');
    $phone   = htmlspecialchars(strip_tags(trim($_POST["phone"] ?? '')), ENT_QUOTES, 'UTF-8');
    $service = htmlspecialchars(strip_tags(trim($_POST["service"] ?? '')), ENT_QUOTES, 'UTF-8');
    // Validation
    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die("Please fill out your name and a valid email address.");
    }

    $mail = new PHPMailer(true);

    try {
        // Select active configuration environment
        $envSettings = $config[ENVIRONMENT];
        $common = $config['common'];

        // Server Settings
        $mail->isSMTP();
        $mail->Host       = $envSettings['host'];
        $mail->SMTPAuth   = $envSettings['auth'];
        $mail->Port       = $envSettings['port'];
        
        if ($envSettings['auth']) {
            $mail->Username   = $envSettings['username'];
            $mail->Password   = $envSettings['password'];
            $mail->SMTPSecure = $envSettings['secure'];
        }

        // Recipients
        $mail->setFrom($common['from_email'], $common['from_name']);
        $mail->addAddress($common['recipient']);
        $mail->addReplyTo($email, $name); 

        // Content Setup
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission from: " . $name;
        
        // Clean and structured HTML output
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 5px;'>
                <h2 style='border-bottom: 2px solid #333; padding-bottom: 10px; margin-top: 0;'>New Contact Form Message</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Phone:</strong> {$phone}</p>
                <p><strong>Address:</strong> {$address}</p>
                <p style='background-color: #f9f9f9; padding: 10px; border-left: 4px solid #333;'>
                    <strong>Service Requested:</strong> {$service}
                </p>
            </div>
        ";

        // Plain text fallback
        $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nAddress: $address\nService: $service";

        $mail->send();
        echo 'Thank You! Your message has been sent successfully.';
        
    } catch (Exception $e) {
        http_response_code(500);
        echo "Oops! Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    http_response_code(405);
    echo "There was a problem with your submission, please try again.";
}
?>
