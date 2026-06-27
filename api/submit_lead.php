<?php
// ============================================================
// api/submit_lead.php
// Fires CRM + Brevo + Gallabox calls without saving to local DB.
// ============================================================
require_once dirname(__DIR__) . '/includes/config.php';

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// ── 1. COLLECT FORM DATA ──────────────────────────────────────────────────
$name         = trim($_POST['name']         ?? '');
$email        = trim($_POST['email']        ?? '');
$country_code = trim($_POST['country_code'] ?? '+91');
$phone        = trim($_POST['phone']        ?? '');
$course       = trim($_POST['course']       ?? '');
$state        = trim($_POST['state']        ?? '');
$page_url     = trim($_POST['page_url']     ?? '');
$lead_type    = trim($_POST['lead_type']    ?? 'brochure');
$form_name    = trim($_POST['form_name']    ?? $lead_type);
$uni_name     = trim($_POST['uni_name']     ?? '');

// UTM parameters (filled by JS from URL before submit)
$utm_source   = trim($_POST['utm_source']   ?? '');
$utm_medium   = trim($_POST['utm_medium']   ?? '');
$utm_campaign = trim($_POST['utm_campaign'] ?? '');
$utm_term     = trim($_POST['utm_term']     ?? '');
$utm_content  = trim($_POST['utm_content']  ?? '');

// Apply defaults when not provided
if (empty($utm_source))  $utm_source  = 'Organic';
if (empty($utm_medium))  $utm_medium  = 'DES_Compare_Organic';

// ── 2. GET USER IP ────────────────────────────────────────────────────────
function get_real_ip(): string {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    }
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return trim($_SERVER['HTTP_CLIENT_IP']);
    }
    return trim($_SERVER['REMOTE_ADDR'] ?? '');
}
$user_ip = get_real_ip();

// ── 3. BASIC VALIDATION ───────────────────────────────────────────────────
if (!$name || !$email || !$phone || !$course || !$state) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}

// ── 4. CSRF VALIDATION ────────────────────────────────────────────────────
$csrf_token = $_POST['csrf_token'] ?? '';
if (!empty($_SESSION['lead_csrf_token']) && !hash_equals($_SESSION['lead_csrf_token'], $csrf_token)) {
    echo json_encode(['success' => false, 'error' => 'Security token invalid. Please refresh the page and try again.']);
    exit;
}

// Respond to browser immediately
echo json_encode(['success' => true]);

// Flush output so user gets the response while we fire integrations
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    // Standard flush trick for Apache/mod_php
    if (ob_get_level()) {
        ob_end_flush();
    }
    flush();
}

// ── 6. SHARED HELPERS ─────────────────────────────────────────────────────

/**
 * Build a clean E.164 phone number.
 * country_code already contains '+' (e.g. '+91').
 * Strip everything except digits from the phone field, then prepend country_code.
 * e.g. country_code='+91', phone='9876543210'  →  '+919876543210'
 */
$phone_digits    = preg_replace('/[^0-9]/', '', $phone); // pure digits only
$phone_with_plus = $country_code . $phone_digits;        // +91 + 9876543210 = +919876543210

/** Fire a JSON POST request and return [bool $ok, string $responseBody] */
function fire_post(string $url, array $payload, array $extra_headers = []): array {
    $headers = array_merge(
        ['Content-Type: application/json'],
        $extra_headers
    );
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response = curl_exec($ch);
    $ok       = !curl_errno($ch);
    curl_close($ch);
    return [$ok, (string)$response];
}

// ── 7. CRM INTEGRATION ────────────────────────────────────────────────────
$crm_url     = 'https://api.crm.mysode.com/api/lead/apicreated';
$crm_api_key = 'a04b4291461f8b060559dfc965864c2c2590e6edd2f5aa7a49388484a1953f22';

$crm_payload = [
    'full_name'    => $name,
    'name'         => $name,
    'email'        => $email,
    'phone'        => $phone_with_plus,
    'course'       => $course,
    'state'        => $state,
    'source'       => 'DES',
    'sub_source'   => '',
    'utm_source'   => $utm_source,
    'utm_medium'   => $utm_medium,
    'utm_campaign' => $utm_campaign,
    'utm_term'     => $utm_term,
    'utm_content'  => $utm_content,
    'page_url'     => $page_url,
    'form_name'    => $form_name,
    'ip_address'   => $user_ip,
];

fire_post($crm_url, $crm_payload, ["x-api-key: {$crm_api_key}"]);

// ── 8. BREVO INTEGRATION ──────────────────────────────────────────────────
$brevo_api_key  = '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';

$brevo_payload = [
    'email'         => $email,
    'listIds'       => [267],
    'attributes'    => [
        'FULLNAME'     => $name,
        'FIRSTNAME'    => explode(' ', $name)[0],
        'SMS'          => $phone_with_plus,   // strict E.164 e.g. +919876543210
        'MOBILE'       => $phone_with_plus,
        'COURSES'      => $course,
        'STATES'       => $state,
        'UTM_SOURCE'   => $utm_source,
        'UTM_CAMPAIGN' => $utm_campaign,
        'UTM_MEDIUM'   => $utm_medium,
        'UTM_TERM'     => $utm_term,
        'SOURCE'       => 'DES Compare',
    ],
    'updateEnabled' => true,
];

fire_post(
    'https://api.brevo.com/v3/contacts',
    $brevo_payload,
    [
        "api-key: {$brevo_api_key}",
        'Accept: application/json',
    ]
);

// ── 9. GALLABOX INTEGRATION ───────────────────────────────────────────────
$gallabox_url = 'https://server.gallabox.com/accounts/61fce6fd9b042a00049ddbc1/integrations/genericWebhook/68494566ef0bd3067b0f3a8d/webhook';

$gallabox_payload = [
    'name'         => $name,
    'phone'        => $phone_with_plus,
    'email'        => $email,
    'course'       => $course,
    'state'        => $state,
    'source'       => 'DES Compare',
    'tags'         => ['Success'],
    'utm_source'   => $utm_source,
    'utm_medium'   => $utm_medium,
    'utm_campaign' => $utm_campaign,
    'utm_term'     => $utm_term,
    'utm_content'  => $utm_content,
    'form_name'    => $form_name,
    'page_url'     => $page_url,
];

fire_post($gallabox_url, $gallabox_payload);

exit;
