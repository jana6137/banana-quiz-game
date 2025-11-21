<?php
// Start session to access user_id
session_start();

header('Content-Type: application/json'); // ✅ Force JSON output

// ✅ Enable error reporting for debugging (optional during development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    // If no user is logged in, return an error in JSON format
    echo json_encode([
        "status" => "error",
        "message" => "⚠️ You must be logged in to view scores."
    ]);
    exit();
}

$user_id = $_SESSION['user_id']; //<!--Virtual Identity- Personal Score Retrieval-->

// Prepare SQL query using prepared statements
// ✅ Using prepared statements prevents SQL injection attacks
$stmt = $conn->prepare("SELECT level, score, correct_answers, wrong_answers, time_taken 
                        FROM scores 
                        WHERE user_id = ? 
                        ORDER BY score DESC"); // Order by highest score first

// ✅ Check if statement preparation failed
if (!$stmt) {
    echo json_encode([
        "status" => "error", 
        "message" => "❌ Database error: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("i", $user_id); // "i" = integer type

// ✅ Extra check: if query execution fails, return error JSON
if (!$stmt->execute()) {
    echo json_encode([
        "status" => "error",
        "message" => "❌ Database query failed: " . $stmt->error
    ]);
    exit();
}

// Get results from executed query
$result = $stmt->get_result();

// Fetch results into an array (row by row)
// ✅ Each row will contain: level, score, correct_answers, wrong_answers, time_taken
$scores = [];
while ($row = $result->fetch_assoc()) {
    $scores[] = $row;
}

// ✅ Calculate statistics from the retrieved scores
$highScore = 0;
$lowScore = PHP_INT_MAX;
$totalCorrect = 0;
$totalWrong = 0;

// ✅ Loop through each score to calculate statistics
foreach ($scores as $score) {
    $highScore = max($highScore, $score['score']);
    $lowScore = min($lowScore, $score['score']);
    $totalCorrect += $score['correct_answers'];
    $totalWrong += $score['wrong_answers'];
}

// ✅ Handle case where no scores exist (lowScore would still be PHP_INT_MAX)
if ($lowScore === PHP_INT_MAX) {
    $lowScore = 0;
}

// Return scores as JSON
if (!empty($scores)) {
    // ✅ Success case: scores found
    echo json_encode([
        "status" => "success",
        "user_id" => $user_id,
        "scores" => $scores,
        // ✅ Include calculated statistics in the response
        "statistics" => [
            "highScore" => $highScore,
            "lowScore" => $lowScore,
            "totalCorrect" => $totalCorrect,
            "totalWrong" => $totalWrong
        ]
    ]);
} else {
    // ✅ Success case but no scores yet
    echo json_encode([
        "status" => "success",
        "user_id" => $user_id,
        "scores" => [],
        // ✅ Include zero statistics when no scores exist
        "statistics" => [
            "highScore" => 0,
            "lowScore" => 0,
            "totalCorrect" => 0,
            "totalWrong" => 0
        ],
        "message" => "No scores found for this user."
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
