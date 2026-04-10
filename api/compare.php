<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// Fetch all logos for the Hero Slider
if ($action === 'get_marquee_logos') {
    $stmt = $pdo->query("SELECT name, image FROM universities WHERE is_active = 1 AND image IS NOT NULL AND image != ''");
    $logos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(function($l) {
        return ['name'=>$l['name'], 'image'=>e($l['image'])];
    }, $logos);
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

// Fetch all unique mapped courses
if ($action === 'get_courses') {
    $stmt = $pdo->query("
        SELECT DISTINCT c.id, c.name, c.display_name, c.course_level 
        FROM courses c 
        JOIN university_courses uc ON c.id = uc.course_id 
        WHERE c.is_active = 1 AND uc.is_active = 1 
        ORDER BY c.name ASC
    ");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $results = [];
    foreach ($courses as $c) {
        $name = get_display_name($c['name'], $c['display_name']);
        if ($c['course_level']) $name .= ' (' . $c['course_level'] . ')';
        $results[] = [
            'id' => $c['id'],
            'text' => $name
        ];
    }
    echo json_encode(['success' => true, 'data' => $results]);
    exit;
}

// Fetch available modes for a specific course
if ($action === 'get_modes') {
    $course_id = (int)($_GET['course_id'] ?? 0);
    $stmt = $pdo->prepare("
        SELECT DISTINCT m.id, m.mode_name 
        FROM education_modes m 
        JOIN university_courses uc ON m.id = uc.education_mode_id 
        WHERE uc.course_id = ? AND uc.is_active = 1
        ORDER BY m.mode_name ASC
    ");
    $stmt->execute([$course_id]);
    $modes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $modes]);
    exit;
}

// Fetch universities that offer the selected course + mode
if ($action === 'get_filtered_universities') {
    $course_id = (int)($_GET['course_id'] ?? 0);
    $mode_id = (int)($_GET['mode_id'] ?? 0);
    
    $stmt = $pdo->prepare("
        SELECT DISTINCT u.id, u.name, u.display_name, u.image 
        FROM universities u 
        JOIN university_courses uc ON u.id = uc.university_id 
        WHERE uc.course_id = ? AND uc.education_mode_id = ? AND u.is_active = 1 AND uc.is_active = 1
        ORDER BY u.name ASC
    ");
    $stmt->execute([$course_id, $mode_id]);
    $unis = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $results = [];
    foreach ($unis as $u) {
        $results[] = [
            'id' => $u['id'],
            'text' => get_display_name($u['name'], $u['display_name']),
            'image' => $u['image'] ? e($u['image']) : ''
        ];
    }
    echo json_encode(['success' => true, 'data' => $results]);
    exit;
}

// Fetch deep comparison data for Grid
if ($action === 'get_bulk_comparison') {
    $course_id = (int)($_GET['course_id'] ?? 0);
    $mode_id = (int)($_GET['mode_id'] ?? 0);
    $uni_ids_str = $_GET['uni_ids'] ?? '';
    
    // Parse valid subset of universities
    $uni_ids = array_filter(array_map('intval', explode(',', $uni_ids_str)));
    if (empty($uni_ids) || !$course_id || !$mode_id) {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
        exit;
    }
    
    // Fetch common course data
    $crs_stmt = $pdo->prepare("SELECT name, display_name, course_level, course_duration, program_eligibility FROM courses WHERE id=?");
    $crs_stmt->execute([$course_id]);
    $courseData = $crs_stmt->fetch(PDO::FETCH_ASSOC);
    if(!$courseData) {
         echo json_encode(['success' => false, 'error' => 'Course not found']); exit;
    }
    
    $courseName = get_display_name($courseData['name'], $courseData['display_name']);

    $results = [];
    
    // Fetch mapping and university specific data
    foreach ($uni_ids as $uid) {
        $stmt = $pdo->prepare("
            SELECT uc.id as mapping_id, uc.academic_fees, uc.fees_discount, uc.course_rating, uc.course_specializations, uc.brochure_file,
                   u.name, u.display_name, u.image, u.rating, u.nirf_ranking, u.year_of_establishment, u.university_type,
                   u.campus_location, u.avg_placement_package, u.placement_assistance, u.emi_facility, u.scholarship,
                   u.key_advantages, u.view_university_link, u.sample_certificate,
                   m.mode_name
            FROM university_courses uc
            JOIN universities u ON uc.university_id = u.id
            JOIN education_modes m ON uc.education_mode_id = m.id
            WHERE uc.course_id = ? AND uc.education_mode_id = ? AND u.id = ? AND uc.is_active = 1 AND u.is_active = 1
            LIMIT 1
        ");
        $stmt->execute([$course_id, $mode_id, $uid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$row) continue; // University doesn't map correctly anymore, skip

        // Exam Modes
        $em_stmt = $pdo->prepare("SELECT m.mode_name FROM exam_modes m JOIN university_exam_modes um ON m.id = um.exam_mode_id WHERE um.university_id = ?");
        $em_stmt->execute([$uid]);
        $exam_modes = $em_stmt->fetchAll(PDO::FETCH_COLUMN);

        // Accreditations
        $acc_stmt = $pdo->prepare("SELECT a.name, a.image FROM accreditations a JOIN university_accreditations ua ON a.id = ua.accreditation_id WHERE ua.university_id = ?");
        $acc_stmt->execute([$uid]);
        $accreditations = $acc_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $advantages = [];
        if (!empty($row['key_advantages'])) {
            $lines = explode("\n", $row['key_advantages']);
            foreach ($lines as $l) {
                $l = trim($l);
                if ($l !== '') $advantages[] = ltrim($l, '-• ');
            }
        }
        
        $specializations = [];
        if (!empty($row['course_specializations'])) {
             $lines = explode("\n", $row['course_specializations']);
             foreach ($lines as $l) {
                $l = trim($l);
                if ($l !== '') $specializations[] = ltrim($l, '-• ');
            }
        }

        $results[] = [
            'uni_id' => $uid,
            'mapping_id' => $row['mapping_id'],
            'uni_name' => get_display_name($row['name'], $row['display_name']),
            'uni_image' => $row['image'] ? e($row['image']) : '',
            'location' => $row['campus_location'] ?: '—',
            'established' => $row['year_of_establishment'] ?: '—',
            'uni_type' => $row['university_type'] ?: '—',
            'accreditations' => $accreditations,
            'eligibility' => $courseData['program_eligibility'] ?: '—',
            'fees' => $row['academic_fees'] ? '₹ ' . number_format($row['academic_fees']) : '—',
            'fees_period' => 'Total', // Fallback, no period in our DB schema inherently 
            'specializations' => $specializations,
            'education_mode' => $row['mode_name'],
            'exam_modes' => implode(', ', $exam_modes) ?: '—',
            'emi_facility' => $row['emi_facility'] ? 'Yes' : 'No',
            'advantages' => $advantages,
            'placement_assistance' => $row['placement_assistance'] ? 'Yes' : 'No',
            'rating' => $row['rating'] ? number_format($row['rating'], 1) : 'N/A',
            'fees_discount' => $row['fees_discount'] ? $row['fees_discount'] : 0,
            'scholarship' => $row['scholarship'] ? 'Available' : 'No',
            'view_link' => $row['view_university_link'] ?: '#',
            'sample_certificate' => $row['sample_certificate'] ?: null,
            'brochure_file' => $row['brochure_file'] ?: null
        ];
    }
    
    echo json_encode(['success' => true, 'course_name' => $courseName, 'data' => $results]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
exit;
