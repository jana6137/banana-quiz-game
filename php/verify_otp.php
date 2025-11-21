<?php
// Start session to access stored OTP data -- <!--Virtual Identity- PHP Sessions-->
session_start();

// ✅ Enable error reporting for debugging (optional during development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file
include 'db.php';

// ✅ Retrieve OTP and username from form submission
$otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';

// ✅ Basic validation: check if OTP field is empty
if (empty($otp)) {
    die("⚠️ Please enter the OTP.");
}

// ✅ Check if OTP session data exists and is valid
if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry']) || 
    !isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_username'])) {
    die("⚠️ OTP session expired or invalid. Please login again.");
}

// ✅ Check if OTP has expired (10-minute limit)
if (time() > $_SESSION['otp_expiry']) {
    // ✅ Clear all OTP-related session data
    unset($_SESSION['otp']);
    unset($_SESSION['otp_expiry']);
    unset($_SESSION['temp_user_id']);
    unset($_SESSION['temp_username']);
    unset($_SESSION['temp_email']);
    
    die("⚠️ OTP has expired. Please login again.");
}

// ✅ Verify OTP matches and username is correct
if ($_SESSION['otp'] == $otp && $_SESSION['temp_username'] == $username) {
    // ✅ OTP is correct - set permanent user session
    $_SESSION['user_id'] = $_SESSION['temp_user_id'];
    $_SESSION['username'] = $_SESSION['temp_username'];
    
    // ✅ Clear temporary OTP session data
    unset($_SESSION['otp']);
    unset($_SESSION['otp_expiry']);
    unset($_SESSION['temp_user_id']);
    unset($_SESSION['temp_username']);
    unset($_SESSION['temp_email']);
    
    // ✅ Return success message to JavaScript
    echo "success";
} else {
    // ✅ OTP is incorrect
    echo "❌ Invalid OTP. Please try again.";
}

// ✅ Close database connection
$conn->close();
?>
