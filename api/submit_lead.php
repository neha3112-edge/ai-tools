<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$country_code = trim($_POST['country_code'] ?? '+91');
$phone = trim($_POST['phone'] ?? '');
$course = trim($_POST['course'] ?? '');
$state = trim($_POST['state'] ?? '');
$page_url = trim($_POST['page_url'] ?? '');
$user_ip = $_SERVER['REMOTE_ADDR'] ?? '';

if (!$name || !$email || !$phone || !$course || !$state) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO brochure_leads (name, email, country_code, phone, course, state, page_url, user_ip, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $country_code, $phone, $course, $state, $page_url, $user_ip]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // If the table doesn't exist, provide a helpful error
    if ($e->getCode() == '42S02') {
        echo json_encode(['success' => false, 'error' => 'Database table brochure_leads is missing.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save system data. Please try again later.']);
    }
}
exit;
