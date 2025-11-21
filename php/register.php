<?php
// Include database connection file
include 'db.php';

// Retrieve form data safely
$username         = isset($_POST['username']) ? trim($_POST['username']) : '';
$email            = isset($_POST['email']) ? trim($_POST['email']) : '';
$password         = isset($_POST['password']) ? trim($_POST['password']) : '';
$confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

// Validate input fields
// ============================================================

// Check if any field is empty
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    die("⚠️ All fields are required. Please fill in the form completely.");
}

// Check if email is valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("⚠️ Invalid email format. Please enter a valid email address.");
}

// Check if passwords match
if ($password !== $confirm_password) {
    die("⚠️ Passwords do not match. Please try again.");
}

// Hash the password securely <!--Version Control--> 
// <!--Virtual Identity- Password Encryption-->
$hashedPassword = password_hash($password, PASSWORD_DEFAULT); 

//Check if username already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("⚠️ Username already taken. Please choose another one.");
}
$stmt->close();

//Insert new user into database using prepared statement <!--Version Control-->
$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashedPassword);

if ($stmt->execute()) {
    // Registration successful → redirect to login page
    header("Location: ../login.html");
    exit();
} else {
    // Registration failed → show error
    echo "❌ Registration failed: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
