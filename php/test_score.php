<?php
session_start();
include 'db.php';

// Manually set a user session for testing (use one of your user IDs from users table)
$_SESSION['user_id'] = 1; // Change this to an actual user ID from your users table

// Test data
$test_data = [
    'level' => 1,
    'score' => 50,
    'correct' => 5,
    'wrong' => 2,
    'time' => 120
];

$stmt = $conn->prepare("INSERT INTO scores (user_id, level, score, correct_answers, wrong_answers, time_taken) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiii", $_SESSION['user_id'], $test_data['level'], $test_data['score'], $test_data['correct'], $test_data['wrong'], $test_data['time']);

if ($stmt->execute()) {
    echo "✅ Test score saved successfully!";
} else {
    echo "❌ Test failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
