<?php
require_once '../config.php';

header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;

$conn = getDatabaseConnection();

$sql = "SELECT COUNT(*) as total FROM questions";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalRows = (int)$row['total'];
$totalPages = ceil($totalRows / $perPage);

$sql = "SELECT id, question, answer, created_at FROM questions ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $perPage);
$stmt->execute();
$result = $stmt->get_result();

$faqs = [];
while ($row = $result->fetch_assoc()) {
    $faqs[] = $row;
}

$conn->close();

echo json_encode([
    'success' => true,
    'page' => $page,
    'total_pages' => $totalPages,
    'faqs' => $faqs
]);
