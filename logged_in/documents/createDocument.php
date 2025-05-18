<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../checkType.php';
require_once '../../config.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'You have to log in first!'];
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = json_decode(file_get_contents('php://input'), true);

    if (empty($post['user_name']) || empty($post['id']) || empty($post['type']) || empty($post['doc_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($post['user_name'] !== $userData['username'] || $post['id'] !== $userData['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $post['doc_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid document name']);
        exit;
    }

    $doc_name = $post['doc_name'];
    $user_name = $post['user_name'];
    $id = $post['id'];
    $type = $post['type'];


    $conn = getDatabaseConnection();

    // Check if the document already exists
    $stmt = $conn->prepare('SELECT * FROM documents WHERE author_id = ? AND title = ? AND doc_type = ?');

    $stmt->bind_param('iss', $id, $doc_name, $type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Document already exists']);
        exit;
    }
    $stmt->close();

    // Create the directory for the document
    $dir = realpath(__DIR__ . '/../..') . '/DOCS/' . $user_name . '/' . $type . '/' . $doc_name;
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create directory']);
            exit;
        }
    }

    // Generate random origin country, historical date (1400-01-01 to 1700-12-31), original author, and language
    $countries = ['France', 'Italy', 'Spain', 'England', 'Portugal', 'Mongolia', 'China', 'Ottoman Empire', 'Russia', 'Japan'];
    $random_country = $countries[array_rand($countries)];

    // Generate historical date
    $start_year = 1400;
    $end_year = 1700;
    $random_year = rand($start_year, $end_year);
    $random_month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
    $random_day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

    $random_historical_date = "$random_year-$random_month-$random_day";

    // List of historical figures (poets, writers, generals, rulers)
    $authors = [
        'William Shakespeare',    // English playwright (1564–1616)
        'Miguel de Cervantes',    // Spanish writer (1547–1616)
        'Leonardo da Vinci',      // Italian polymath (1452–1519)
        'Genghis Khan',           // Mongol ruler (1162–1227)
        'Suleiman the Magnificent', // Ottoman sultan (1494–1566)
        'Tsar Ivan the Terrible', // Russian ruler (1530–1584)
        'Oda Nobunaga',           // Japanese daimyo (1534–1582)
        'Dante Alighieri',        // Italian poet (1265–1321, slightly earlier but influential)
        'Francis Drake',          // English sea captain (1540–1596)
        'Louis XIV',              // King of France (1638–1715)
        'Joan of Arc',            // French military leader (1412–1431)
        'Niccolò Machiavelli',    // Italian philosopher (1469–1527)
        'Francisco Pizarro',      // Spanish conquistador (1478–1541)
        'Hernán Cortés',          // Spanish conquistador (1485–1547)
        'Tokugawa Ieyasu'         // Japanese shogun (1543–1616)
    ];

    $random_author = $authors[array_rand($authors)];

    $languages = ['English', 'Spanish', 'Italian', 'French', 'Portuguese', 'Japanese', 'Chinese', 'Russian', 'Turkish'];
    $random_language = $languages[array_rand($languages)];

    // Create the document
    $stmt = $conn->prepare('INSERT INTO documents (author_id, title, doc_type, status, description, language, historical_author, historical_date, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $status = 'ACTIVE';
    $description = 'Please add a description';
    $stmt->bind_param('issssssss', $id, $doc_name, $type, $status, $description, $random_language, $random_author, $random_historical_date, $random_country);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create document']);
        exit;
    }
    $stmt->close();

    // Fetch the document ID
    $stmt = $conn->prepare('SELECT id FROM documents WHERE author_id = ? AND title = ? AND doc_type = ?');
    $stmt->bind_param('iss', $id, $doc_name, $type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch document ID']);
        exit;
    }
    $row = $result->fetch_assoc();
    $document_id = $row['id'];
    $stmt->close();
    echo json_encode(['success' => true ,'message' => 'Document created successfully', 'document_id' => $document_id]);
    $conn->close();
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>