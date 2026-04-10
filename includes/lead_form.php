<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Ensure $pdo is available (requires db.php to be loaded by caller or explicitly here)
global $pdo;

// Fetch unique available courses for the dropdown purely server-side
$comp_courses = [];
if ($pdo) {
    $c_stmt = $pdo->query("SELECT DISTINCT c.id, c.name, c.display_name, c.course_level 
        FROM courses c 
        JOIN university_courses uc ON c.id = uc.course_id 
        WHERE c.is_active = 1 AND uc.is_active = 1 
        ORDER BY c.name ASC");
    $comp_courses = $c_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Default options structure
// $lead_form_options = [
//    'form_id' => 'leadForm',           // Unique ID for the form and its wrappers
//    'heading' => 'Download Brochure', 
//    'subheading' => 'Academic Experts will assist you!',
//    'button_text' => 'Download Brochure',
//    'success_heading' => 'Thank You!',
//    'success_message' => 'Your request has been successfully submitted. Our academic experts will contact you soon.'
// ];

$opt = $lead_form_options ?? [];

$form_id = $opt['form_id'] ?? 'leadForm_' . uniqid();
$heading = $opt['heading'] ?? 'Request Information';
$subheading = $opt['subheading'] ?? 'Our academic experts will reach out to you shortly.';
$button_text = $opt['button_text'] ?? 'Submit';
$success_heading = $opt['success_heading'] ?? 'Thank You!';
$success_message = $opt['success_message'] ?? 'Your request has been successfully submitted.';
$lead_type = $opt['lead_type'] ?? 'brochure';

// Generate CSRF token if session exists
if (empty($_SESSION['lead_csrf_token'])) {
    $_SESSION['lead_csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['lead_csrf_token'];

?>
<div id="<?= e($form_id) ?>_Area">
    <?php if ($heading): ?>
        <h2 style="margin: 0; color: var(--accent-blue); text-align: center; margin-bottom: 0.5rem;font-size:1.5rem;">
            <?= e($heading) ?></h2>
    <?php endif; ?>
    <?php if ($subheading): ?>
        <p style="text-align: center; color: var(--text-m); margin-bottom: 1.5rem; font-weight: 500; font-size: 0.95rem;">
            <?= e($subheading) ?></p>
    <?php endif; ?>

    <form class="dynamic-lead-form" data-wrapper-id="<?= e($form_id) ?>" onsubmit="submitGenericLeadForm(event, this)">
        <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
        <input type="hidden" name="form_id_context" value="<?= e($form_id) ?>">
        <input type="hidden" name="lead_type" value="<?= e($lead_type) ?>">
        <input type="hidden" class="dynamic-source-url" name="source_url" value="">

        <div class="brochure-form-group">
            <input type="text" name="name" placeholder="Enter Your Name" required>
        </div>
        <div class="brochure-form-group">
            <input type="email" name="email" placeholder="Enter Your Email" required>
        </div>
        <div class="brochure-form-group" style="display: flex;">
            <select name="country_code"
                style="width: 100px; margin-right: 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: #f8fafc; font-size:0.9rem;"
                required>
                <option value="+91">🇮🇳 +91</option>
                <option value="+1">🇺🇸 +1</option>
                <option value="+44">🇬🇧 +44</option>
                <option value="+61">🇦🇺 +61</option>
                <option value="+81">🇯🇵 +81</option>
                <option value="+65">🇸🇬 +65</option>
                <option value="+971">🇦🇪 +971</option>
                <option value="+86">🇨🇳 +86</option>
                <option value="+49">🇩🇪 +49</option>
                <option value="+33">🇫🇷 +33</option>
                <option value="+7">🇷🇺 +7</option>
            </select>
            <input type="tel" name="phone" placeholder="Enter your Number" required pattern="[0-9]{8,15}"
                title="8 to 15 digit mobile number" style="flex:1;">
        </div>
        <div class="brochure-form-group">
            <select name="course" class="dynamic-course-select" required>
                <option value="">Select Your Course</option>
                <?php foreach ($comp_courses as $c):
                    $cname = !empty($c['display_name']) ? $c['display_name'] : $c['name'];
                    if ($c['course_level'])
                        $cname .= ' (' . $c['course_level'] . ')';
                    ?>
                    <option value="<?= e($cname) ?>"><?= e($cname) ?></option>
                <?php endforeach; ?>
                <!-- JS can still inject overriding options via .dynamic-course-select if required -->
            </select>
        </div>
        <div class="brochure-form-group">
            <select name="state" required>
                <option value="">Select Your State</option>
                <option value="Andhra Pradesh">Andhra Pradesh</option>
                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                <option value="Assam">Assam</option>
                <option value="Bihar">Bihar</option>
                <option value="Chhattisgarh">Chhattisgarh</option>
                <option value="Goa">Goa</option>
                <option value="Gujarat">Gujarat</option>
                <option value="Haryana">Haryana</option>
                <option value="Himachal Pradesh">Himachal Pradesh</option>
                <option value="Jharkhand">Jharkhand</option>
                <option value="Karnataka">Karnataka</option>
                <option value="Kerala">Kerala</option>
                <option value="Madhya Pradesh">Madhya Pradesh</option>
                <option value="Maharashtra">Maharashtra</option>
                <option value="Manipur">Manipur</option>
                <option value="Meghalaya">Meghalaya</option>
                <option value="Mizoram">Mizoram</option>
                <option value="Nagaland">Nagaland</option>
                <option value="Odisha">Odisha</option>
                <option value="Punjab">Punjab</option>
                <option value="Rajasthan">Rajasthan</option>
                <option value="Sikkim">Sikkim</option>
                <option value="Tamil Nadu">Tamil Nadu</option>
                <option value="Telangana">Telangana</option>
                <option value="Tripura">Tripura</option>
                <option value="Uttar Pradesh">Uttar Pradesh</option>
                <option value="Uttarakhand">Uttarakhand</option>
                <option value="West Bengal">West Bengal</option>
                <option value="Delhi">Delhi</option>
            </select>
        </div>

        <div
            style="display: flex; align-items: flex-start; gap: 8px; margin-bottom: 1.5rem; font-size: 0.8rem; color: var(--text);">
            <input type="checkbox" name="consent" id="consent_<?= e($form_id) ?>" required style="margin-top: 3px;">
            <label for="consent_<?= e($form_id) ?>">I consent to receive university updates via email and mobile number.
                <a href="#" style="color:var(--accent-blue);">Disclaimer</a></label>
        </div>

        <button type="submit" class="btn btn-success lead-submit-btn"
            style="width: 100%; border-radius: var(--radius-sm); font-size: 1.1rem; font-weight: 600; padding: 0.8rem; background: #10b981;">
            <?= e($button_text) ?>
        </button>
    </form>
</div>

<!-- Success Message Element (Hidden by Default) -->
<div id="<?= e($form_id) ?>_Success" style="display:none; text-align:center; padding: 2rem 0;">
    <div
        style="width: 60px; height: 60px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>
    <h2 style="color: var(--accent-blue); margin-bottom: 0.5rem; font-size: 1.5rem;"><?= e($success_heading) ?></h2>
    <p style="color: var(--text-m); margin-bottom: 2rem; font-size: 0.95rem; line-height: 1.5;">
        <?= e($success_message) ?></p>
    <button type="button" class="btn btn-secondary trigger-modal-close" data-form-id="<?= e($form_id) ?>"
        style="width: 100%; border-radius: 30px; font-weight: 600; padding: 0.8rem; background: #e2e8f0; color: #1e293b; border: 1px solid #cbd5e1; cursor: pointer;">Close</button>
</div>