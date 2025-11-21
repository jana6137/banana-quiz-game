<?php
// Start session to access user data
session_start();

// ✅ Enable error reporting for debugging (optional during development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file
include 'db.php';

// Debug: Log that we reached this file
error_log("🔄 save_score.php accessed"); // ✅ Debug: confirm PHP file is being called

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("❌ User not logged in for score saving"); // ✅ Debug: log login issue
    die("⚠️ You must be logged in to save scores.");
}
$user_id = $_SESSION['user_id']; // ✅ Correct usage <!--Virtual Identity- Score Saving with User ID-->

// Debug: Log user ID for tracking
error_log("👤 User ID attempting to save score: " . $user_id); // ✅ Debug: log which user is saving

// Retrieve and validate POST data
$level   = isset($_POST['level'])   ? intval($_POST['level'])   : 0;
$score   = isset($_POST['score'])   ? intval($_POST['score'])   : 0;
$correct = isset($_POST['correct']) ? intval($_POST['correct']) : 0;
$wrong   = isset($_POST['wrong'])   ? intval($_POST['wrong'])   : 0;
$time    = isset($_POST['time'])    ? intval($_POST['time'])    : 0;

// Debug: Log all received POST data for verification
error_log("📥 Received POST data - Level: $level, Score: $score, Correct: $correct, Wrong: $wrong, Time: $time"); // ✅ Debug: log all data received

// ✅ Validate required fields
if ($level <= 0 || $score < 0 || $correct < 0 || $wrong < 0 || $time < 0) {
    error_log("❌ Invalid score data received - Validation failed"); // ✅ Debug: log validation failure
    die("⚠️ Invalid score data. Please try again.");
}

// Prepare SQL query 
$stmt = $conn->prepare("
    INSERT INTO scores (user_id, level, score, correct_answers, wrong_answers, time_taken)
    VALUES (?, ?, ?, ?, ?, ?)
");

// ✅ Check if the SQL statement was prepared successfully
if (!$stmt) {
    error_log("❌ Database preparation failed: " . $conn->error); // ✅ Debug: log SQL preparation error
    die("❌ Database preparation failed: " . $conn->error);
}

// ✅ Bind parameters to the prepared statement
// "iiiiii" means six integer parameters
$stmt->bind_param("iiiiii", $user_id, $level, $score, $correct, $wrong, $time);

// Execute query <!--Version Control-->
if ($stmt->execute()) {
    // ✅ Debug: log successful score save with details
    error_log("✅ Score saved successfully for user $user_id - Level: $level, Score: $score");
    
    // ✅ Get the last inserted ID for confirmation
    $last_id = $conn->insert_id;
    error_log("📝 New score record ID: " . $last_id); // ✅ Debug: log the new record ID
    
    echo "✅ Score saved successfully!";
} else {
    error_log("❌ Failed to save score for user $user_id: " . $stmt->error); // ✅ Debug: log SQL execution error
    echo "❌ Failed to save score: " . $stmt->error;
}

// ✅ Close the prepared statement
$stmt->close();

// ✅ Close the database connection
$conn->close();

// ✅ Debug: confirm script completed execution
error_log("🏁 save_score.php execution completed"); // ✅ Debug: log script completion
?>
