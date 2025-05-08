<?php
require_once '../config.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['question']) || trim($input['question']) === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Question is required.']);
    exit();
}

$question = trim($input['question']);

$conn = getDatabaseConnection();

$sql = "INSERT INTO questions (question) VALUES (?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $question);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Question submitted successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}

$stmt->close();
$conn->close();
