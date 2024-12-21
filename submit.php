<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'survey_db');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Process survey submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect standard form fields
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $occupation = $_POST['occupation'];
    $salary_range = $_POST['salary_range'];

    // Insert into surveys table
    $stmt = $conn->prepare('INSERT INTO surveys (name, email, gender, age, occupation, salary_range) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssiss', $name, $email, $gender, $age, $occupation, $salary_range);

    if ($stmt->execute()) {
        $survey_id = $stmt->insert_id; // Get the inserted survey ID
        $stmt->close();

        // Handle dynamically added questions
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'question_') === 0) {
                $question_id = str_replace('question_', '', $key); // Extract the question ID
                $answer = is_array($value) ? json_encode($value) : $value;

                // Insert into survey_answers table
                $answer_stmt = $conn->prepare('INSERT INTO survey_answers (survey_id, question_id, answer) VALUES (?, ?, ?)');
                $answer_stmt->bind_param('iis', $survey_id, $question_id, $answer);
                $answer_stmt->execute();
                $answer_stmt->close();
            }
        }

        echo 'Survey submitted successfully!';
    } else {
        echo 'Error: ' . $conn->error;
    }
}

// Close database connection
$conn->close();
?>