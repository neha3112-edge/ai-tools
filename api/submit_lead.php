<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';

session_start();
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
$lead_type = trim($_POST['lead_type'] ?? 'brochure');
$uni_name = trim($_POST['uni_name'] ?? ''); // optional Context tracking for scholarship module
$user_ip = $_SERVER['REMOTE_ADDR'] ?? '';

if (!$name || !$email || !$phone || !$course || !$state) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}

// CSRF Validation Layer
$csrf_token = $_POST['csrf_token'] ?? '';
if (!empty($_SESSION['lead_csrf_token']) && !hash_equals($_SESSION['lead_csrf_token'], $csrf_token)) {
    echo json_encode(['success' => false, 'error' => 'Security token invalid. Please refresh the page and try again.']);
    exit;
}

try {
    if ($lead_type === 'scholarship') {
        $stmt = $pdo->prepare("INSERT INTO scholarship_leads (name, email, country_code, phone, course, state, uni_name, page_url, user_ip, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $country_code, $phone, $course, $state, $uni_name, $page_url, $user_ip]);
    } else if ($lead_type === 'counseling') {
        $stmt = $pdo->prepare("INSERT INTO counseling_leads (name, email, country_code, phone, course, state, uni_name, page_url, user_ip, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $country_code, $phone, $course, $state, $uni_name, $page_url, $user_ip]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO brochure_leads (name, email, country_code, phone, course, state, page_url, user_ip, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $country_code, $phone, $course, $state, $page_url, $user_ip]);
    }
    
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
