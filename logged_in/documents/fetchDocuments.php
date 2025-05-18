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
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'You have to log in first!'];
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
$public = isset($_GET['public']) && $_GET['public'] === 'true';

// SQL with non-null and non-empty field filters
$filterConditions = "
    language IS NOT NULL AND TRIM(language) != '' AND
    historical_author IS NOT NULL AND TRIM(historical_author) != '' AND
    historical_date IS NOT NULL AND TRIM(historical_date) != '' AND
    country IS NOT NULL AND TRIM(country) != ''
";

if ($public) {
    $sql = "SELECT id, title, language, historical_author, historical_date, country
            FROM documents
            WHERE is_public = 1 AND doc_type = ? AND $filterConditions";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $key);
} else {
    $sql = "SELECT id, title, language, historical_author, historical_date, country
            FROM documents
            WHERE author_id = ? AND doc_type = ? AND $filterConditions";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userData['id'], $key);
}

$stmt->execute();
$result = $stmt->get_result();

$documents = [];
while ($row = $result->fetch_assoc()) {
    $documents[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($documents);
