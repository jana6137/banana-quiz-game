<?php
// Start the session to store user data -- <!--Virtual Identity- PHP Sessions-->
session_start(); 

// Include database connection file
include 'db.php';

// Retrieve form data safely
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Basic validation: check if fields are empty <!--Version Control-->
if (empty($username) || empty($password)) {
    die("⚠️ Please enter both username and password.");
}

// Prepare SQL query using prepared statements <!-- Interesting Features - Secure Coding (Protected Queries)-->
// (Prevents SQL injection attacks)
$stmt = $conn->prepare("SELECT id, username, password, email FROM users WHERE username = ?");
$stmt->bind_param("s", $username); // "s" means string type
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Verify password using password_verify()
    if (password_verify($password, $row['password'])) {
        // ✅ OTP AUTHENTICATION ADDITION: Generate 6-digit OTP
        $otp = rand(100000, 999999);
        
        // ✅ OTP AUTHENTICATION ADDITION: Store OTP in session with expiration time (10 minutes)
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 600; // 10 minutes from current time
        $_SESSION['temp_user_id'] = $row['id'];
        $_SESSION['temp_username'] = $row['username'];
        $_SESSION['temp_email'] = $row['email'];
        
        // ✅ OTP AUTHENTICATION ADDITION: For testing, display OTP on screen
        // In a real application, you would send the OTP via email
        // For this example, we'll just display it (in production, remove this line)
        echo "OTP sent to your email. For testing, your OTP is: " . $otp;
        
        // ✅ OTP AUTHENTICATION ADDITION: In production, uncomment the line below to actually send email
        // sendOtpEmail($row['email'], $otp);
        
    } else {
        echo "❌ Invalid password. Please try again.";
    }
} else {
    echo "❌ User not found. Please register first.";
}

// Close statement and connection
$stmt->close();
$conn->close();

// ✅ OTP AUTHENTICATION ADDITION: Function to send OTP email (placeholder for production)
function sendOtpEmail($email, $otp) {
    $subject = "Banana Quiz Game - OTP Verification";
    $message = "Your OTP for Banana Quiz Game login is: " . $otp . "\n\nThis code will expire in 10 minutes.";
    $headers = "From: no-reply@bananaquiz.com";
    
    // Uncomment the line below in production to actually send email
    // mail($email, $subject, $message, $headers);
}
?>
