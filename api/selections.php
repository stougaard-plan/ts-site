<?php
/**
 * Selections API — gem/hent ejendomsudvælgelser (shortlist) per måned.
 */

$BEARER_TOKEN = 'api-Yj3kR8mP5nQ2wL9x';
$DATA_DIR = __DIR__ . '/../data';

// CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Auth
$auth = '';
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $auth = $_SERVER['HTTP_AUTHORIZATION'];
} elseif (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $auth = $headers['Authorization'] ?? '';
}
if ($auth !== 'Bearer ' . $BEARER_TOKEN) {
    http_response_code(401);
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');
@mkdir($DATA_DIR, 0755, true);
$selectionsFile = $DATA_DIR . '/selections.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($selectionsFile)) {
        readfile($selectionsFile);
    } else {
        echo json_encode(['selections' => new \stdClass()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || (!isset($input['selections']) && !isset($input['ignored']))) {
        http_response_code(400);
        die(json_encode(['error' => 'Invalid JSON']));
    }

    file_put_contents($selectionsFile, json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
