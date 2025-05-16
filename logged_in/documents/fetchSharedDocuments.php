<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../checkType.php';
header('Content-Type: application/json');

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: ../../login.php');
    exit();
}

require_once '../../config.php';

$conn = getDatabaseConnection();

if (!isset($_GET['key']) || empty($_GET['key'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing key parameter']);
    exit;
}

$key = $_GET['key'];

// Add filtering to exclude null or empty values
$filterConditions = "
    d.language IS NOT NULL AND TRIM(d.language) != '' AND
    d.historical_author IS NOT NULL AND TRIM(d.historical_author) != '' AND
    d.historical_date IS NOT NULL AND TRIM(d.historical_date) != '' AND
    d.country IS NOT NULL AND TRIM(d.country) != ''
";

$sql = "
    SELECT d.id, d.title, d.language, d.historical_author, d.historical_date, d.country
    FROM document_user_association dua
    JOIN documents d ON dua.document_id = d.id
    WHERE dua.user_id = ? AND d.doc_type = ? AND $filterConditions
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userData['id'], $key);
$stmt->execute();
$result = $stmt->get_result();

$sharedDocuments = [];
while ($row = $result->fetch_assoc()) {
    $sharedDocuments[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($sharedDocuments);
