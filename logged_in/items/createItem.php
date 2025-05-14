<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../checkType.php';
require_once '../../config.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = json_decode(file_get_contents('php://input'), true);

    if (empty($post['user_name']) || empty($post['id']) || empty($post['doc_id']) || empty($post['doc_name']) || empty($post['image_name']) || empty($post['type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($post['user_name'] !== $userData['username'] || $post['id'] !== $userData['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $post['image_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid document name']);
        exit;
    }

    $doc_id = $post['doc_id'];
    $type = $post['type'];
    $doc_name = $post['doc_name'];
    $user_name = $post['user_name'];
    $user_id = $post['id'];
    $image_name = $post['image_name'];

    $conn = getDatabaseConnection();

    // Check if image exists in the pictures table as temp
    $stmt = $conn->prepare('SELECT * FROM pictures WHERE creator = ? AND name = ? AND type = ?');
    $temp = 'temp';
    $stmt->bind_param('iss', $user_id, $image_name, $temp);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Image not found in database']);
        exit;
    }
    $row = $result->fetch_assoc();
    $temp_file_path = $row['path'];
    $extension = pathinfo($temp_file_path, PATHINFO_EXTENSION);
    $stmt->close();

    // Check if the document already exists
    $new_db_file_path = '/DOCS/' . $user_name . '/' . $type . '/' . $doc_name . '/' . $image_name . '.' . $extension;
    $stmt = $conn->prepare('SELECT * FROM items WHERE document_id = ? AND image_path = ?');
    $stmt->bind_param('is', $doc_id, $new_db_file_path);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'File already exists in items']);
        exit;
    }
    $stmt->close();

    $old_file_path = realpath(__DIR__ . '/../..') . $row['path'];

    if (!file_exists($old_file_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'Temp file not found']);
        exit;
    }
    // Define new directory and move file
    $new_directory = realpath(__DIR__ . '/../..') . '/DOCS/' . $user_name . '/' . $type . '/' . $doc_name;
    if (!is_dir($new_directory)) {
        mkdir($new_directory, 0777, true);
    }

    $new_file_path = $new_directory . '/' . basename($old_file_path);

    // Check if the new file path already exists
    if (file_exists($new_file_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'File already exists']);
        exit;
    }


    if (!rename($old_file_path, $new_file_path)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move file']);
        exit;
    }
    // remove the old picture from the database
    $stmt = $conn->prepare('DELETE FROM pictures WHERE creator = ? AND name = ? AND type = ?');
    $temp = 'temp';
    $stmt->bind_param('iss', $user_id, $image_name, $temp);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete old image from database']);
        exit;
    }
    $stmt->close();
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



    // Insert the new image into the items table
    $stmt = $conn->prepare('INSERT INTO items (document_id, status, title, description, image_path, publish_date, modified_date, language, historical_author, historical_date, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $status = 'UPLOADED';
    $description = 'Please add a description';
    $date = date('Y-m-d H:i:s');
    $stmt->bind_param('issssssssss', $doc_id, $status, $image_name, $description, $new_db_file_path, $date, $date, $random_language, $random_author, $random_historical_date, $random_country);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to insert image into items']);
        exit;
    }
    $stmt->close();

    //fetch the item id
    $stmt = $conn->prepare('SELECT id FROM items WHERE document_id = ? AND image_path = ?');
    $stmt->bind_param('is', $doc_id, $new_db_file_path);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch item ID']);
        exit;
    }
    $row = $result->fetch_assoc();
    $item_id = $row['id'];
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => true, 'message' => 'File moved and database updated', 'item_id' => $item_id, 'file_path' => $new_file_path]);


} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>