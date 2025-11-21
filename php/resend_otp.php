<?php
// Start session to access user data -- <!--Virtual Identity- PHP Sessions-->
session_start();

// ✅ Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file
include 'db.php';

// ✅ Retrieve username from form submission
$username = isset($_POST['username']) ? trim($_POST['username']) : '';

// ✅ Basic validation: check if username is provided
if (empty($username)) {
    die("⚠️ Username is required.");
}

// ✅ Prepare SQL query using prepared statements <!-- Interesting Features - Secure Coding (Protected Queries)-->
$stmt = $conn->prepare("SELECT id, username, email FROM users WHERE username = ?");
$stmt->bind_param("s", $username); // "s" means string type
$stmt->execute();
$result = $stmt->get_result();

// ✅ Check if user exists in database
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // ✅ Generate new 6-digit OTP
    $otp = rand(100000, 999999);
    
    // ✅ Store new OTP in session with fresh expiration time (10 minutes)
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 600; // 10 minutes from current time
    $_SESSION['temp_user_id'] = $row['id'];
    $_SESSION['temp_username'] = $row['username'];
    $_SESSION['temp_email'] = $row['email'];
    
    // ✅ For testing, display new OTP on screen
    // In production, you would send the OTP via email instead of displaying it
    echo "New OTP sent to your email. For testing, your new OTP is: " . $otp;
    
    // ✅ In production, uncomment the line below to actually send email
    // sendOtpEmail($row['email'], $otp);
    
} else {
    echo "❌ User not found. Please register first.";
}

// ✅ Close statement and database connection
$stmt->close();
$conn->close();

// ✅ Function to send OTP email (placeholder for production)
function sendOtpEmail($email, $otp) {
    $subject = "Banana Quiz Game - New OTP Verification";
    $message = "Your new OTP for Banana Quiz Game login is: " . $otp . "\n\nThis code will expire in 10 minutes.";
    $headers = "From: no-reply@bananaquiz.com";
    
    // Uncomment the line below in production to actually send email
    // mail($email, $subject, $message, $headers);
}
?>
