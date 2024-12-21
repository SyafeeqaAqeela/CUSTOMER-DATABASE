<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'survey_db');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

$survey_id = 1; // Use the relevant survey_id or create a new one dynamically
$question_name = $data['question_name'];
$question_type = $data['question_type'];
$options = isset($data['options']) ? json_encode($data['options']) : null;

// Insert into survey_questions table
$stmt = $conn->prepare('INSERT INTO survey_questions (survey_id, question_name, question_type, options) VALUES (?, ?, ?, ?)');
$stmt->bind_param('isss', $survey_id, $question_name, $question_type, $options);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>