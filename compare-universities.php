<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compare Universities — SODE AI Tools</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/public.css">

    <script>
        const BASE_URL = '<?= rtrim(BASE_URL, '/') ?>';
        const UPLOAD_URL = '<?= rtrim(UPLOAD_URL, '/') ?>';
    </script>
</head>

<body>

    <nav class="public-navbar">
        <div class="brand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                <path d="M6 12v5c3 3 9 3 12 0v-5" />
            </svg>
            SODE AI
        </div>
        <div class="public-nav-links">
            <a href="<?= BASE_URL ?>/compare-universities.php" class="active">Compare Universities</a>
            <a href="<?= ADMIN_URL ?>/login.php" class="btn btn-primary" style="color:#fff; padding:0.4rem 1rem;">Admin
                Login</a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <div class="hero-section">
        <!-- Marquee Slider -->
        <div class="marquee-container" id="marqueeContainer">
            <div class="marquee-track" id="marqueeTrack1">
                <!-- Populated via JS -->
            </div>
            <div class="marquee-track" id="marqueeTrack2" aria-hidden="true">
                <!-- Duplicate for infinite loop -->
            </div>
        </div>
    </div>

    <!-- SELECTOR CARD -->
    <div class="center-card-wrapper">
        <div class="selector-card">
            <h1>Compare Universities <span>& Choose Best Fit For you</span></h1>

            <div class="course-controls">
                <select id="masterCourse" class="styled-select" onchange="onCourseSelect()">
                    <option value="">Select Course to Compare</option>
                </select>
                <select id="masterMode" class="styled-select" onchange="onModeSelect()" disabled>
                    <option value="">Select Mode</option>
                </select>
            </div>

            <div class="uni-boxes-grid">
                <div class="uni-box empty" id="uni-box-1" onclick="openUniModal(1)">
                    <div class="plus-icon">+</div>
                    <div>Select University</div>
                </div>
                <div class="uni-box empty" id="uni-box-2" onclick="openUniModal(2)">
                    <div class="plus-icon">+</div>
                    <div>Select University</div>
                </div>
                <div class="uni-box empty" id="uni-box-3" onclick="openUniModal(3)">
                    <div class="plus-icon">+</div>
                    <div>Select University</div>
                </div>
            </div>

            <button class="btn btn-success compare-action-btn" id="compareBtn" disabled onclick="executeCompare()">
                Compare Now
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px;">
                    <polyline points="5 12 19 12" />
                    <polyline points="12 5 19 12 12 19" />
                </svg>
            </button>
        </div>
    </div>

    <!-- COMPARISON GRID -->
    <div class="table-wrapper" id="tableWrapper">
        <!-- Rendered via JS -->
    </div>

    <!-- CTA SECTION -->
    <div class="cta-section">
        <h2><em>Still Have Confusion?</em></h2>
        <p><em>You Need A Career Expert Who Will Guide You<br>to choose Best fit for you</em></p>
        <button class="btn-gradient-cta" onclick="opencounselingModal('General Counseling', '')">
            <div class="icon-circle">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#0d6efd" stroke="none">
                    <path
                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                    </path>
                </svg>
            </div>
            Book 1:1 Free Counseling
        </button>
    </div>

    <!-- Modal for search -->
    <div class="uni-modal-bg" id="uniModalBg">
        <div class="uni-modal">
            <div class="modal-header">
                <h3>Select a University</h3>
                <button class="modal-close" onclick="closeUniModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <input type="text" class="search-input" id="uniSearch" placeholder="Search university by name..."
                oninput="filterUniversities()">
            <div class="search-results" id="uniList"></div>
        </div>
    </div>
    <!--  BROCHURE MODAL -->
    <div class="uni-modal-bg" id="brochureModalBg" style="z-index: 9999;">
        <div class="uni-modal" style="max-width: 450px; padding: 2rem; position:relative;">
            <button class="modal-close" style="position: absolute; right: 15px; top: 15px; z-index: 10;"
                onclick="closeBrochureModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <?php
            $lead_form_options = [
                'form_id' => 'brochureFormGlobal',
                'heading' => 'Download Brochure',
                'subheading' => 'Academic Experts will assist you!',
                'button_text' => 'Download Brochure',
                'success_heading' => 'Thank You!',
                'success_message' => 'Your request has been successfully submitted. Your brochure will open in a new tab momentarily. Our academic experts will contact you soon.'
            ];
            require 'includes/lead_form.php';
            ?>
        </div>
    </div>

    <!-- SCHOLARSHIP MODAL -->
    <div class="uni-modal-bg" id="scholarshipModalBg" style="z-index: 9999;">
        <div class="uni-modal" style="max-width: 450px; padding: 2rem; position:relative;">
            <button class="modal-close" style="position: absolute; right: 15px; top: 15px; z-index: 10;"
                onclick="closeScholarshipModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <div id="scholarshipUiTop">
                <!-- University Header Details will go here -->
                <div id="sch_uni_header"
                    style="display:flex; align-items:center; gap: 15px; margin-bottom: 20px; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <img id="sch_uni_img" src=""
                        style="width:50px; height:50px; object-fit:contain; border-radius:50%; background:#fff; border:1px solid #e2e8f0;">
                    <div>
                        <h4 id="sch_uni_name" style="margin:0; font-size:1.1rem; color:var(--accent-blue);">University
                            Name</h4>
                        <span style="font-size:0.8rem; color:var(--text-m);">Exclusive Admission Partner Offer</span>
                    </div>
                </div>

                <h2 id="sch_heading"
                    style="margin: 0; color: #0284c7; text-align: left; margin-bottom: 0.5rem;font-size:1.6rem; font-weight:800;">
                    Claim Discount</h2>
                <p style="text-align: left; color: var(--text-m); margin-bottom: 1.5rem; font-size: 0.95rem;">Join
                    15,000+ students who secured their future with us.</p>
            </div>

            <?php
            $lead_form_options = [
                'form_id' => 'scholarshipFormGlobal',
                'lead_type' => 'scholarship',
                'heading' => '', // Using custom JS heading
                'subheading' => '', // Using custom JS subheading
                'button_text' => 'Claim Discount & Reveal Code',
                'success_heading' => 'Submission Successful!',
                'success_message' => 'Your scholarship request has been received. Our academic counsellor will review your details and contact you shortly with the exact scholarship amount and the complete application process.'
            ];
            require 'includes/lead_form.php';
            ?>
        </div>
    </div>

    <!-- counseling MODAL -->
    <div class="uni-modal-bg" id="counselingModalBg" style="z-index: 9999;">
        <div class="uni-modal" style="max-width: 450px; padding: 2rem; position:relative;">
            <button class="modal-close" style="position: absolute; right: 15px; top: 15px; z-index: 10;"
                onclick="closecounselingModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <div id="counselingUiTop">
                <div id="mnt_uni_header"
                    style="display:flex; align-items:center; gap: 15px; margin-bottom: 20px; background: #fffbeb; padding: 15px; border-radius: 8px; border: 1px solid #fde68a;">
                    <img id="mnt_uni_img" src=""
                        style="width:50px; height:50px; object-fit:contain; border-radius:50%; background:#fff; border:1px solid #fde68a;">
                    <div>
                        <h4 id="mnt_uni_name" style="margin:0; font-size:1.1rem; color:#b45309;">University Name</h4>
                        <span style="font-size:0.8rem; color:#d97706;">Official counseling Partner</span>
                    </div>
                </div>
            </div>

            <?php
            $lead_form_options = [
                'form_id' => 'counselingFormGlobal',
                'lead_type' => 'counseling',
                'heading' => 'Book 1:1 Counselling with Our Experts',
                'subheading' => 'Get personalized guidance from industry experts to shape your academic journey.',
                'button_text' => 'Book Now',
                'success_heading' => 'Booking Successful!',
                'success_message' => 'Your counseling session request has been received. Our expert counsellor will reach out directly to coordinate the best time for your 1:1 session.'
            ];
            require 'includes/lead_form.php';
            ?>
        </div>
    </div>


    <script>
        // State variables
        let allCourses = [];
        let availableModes = [];
        let filteredUnis = []; // Universities valid for current Course+Mode
        let selectedCourseId = null;
        let selectedModeId = null;

        // Slots for selected Universities
        let selectedUniIds = { 1: null, 2: null, 3: null };
        let selectedUniData = { 1: null, 2: null, 3: null }; // Light data for rendering boxes
        let activeBoxIndex = null;

        // Step variables for Brochure Download
        let pendingBrochureUrl = null;

        document.addEventListener("DOMContentLoaded", () => {
            fetchMarquee();
            fetchCourses();
        });

        // ──────────────────────────────────────────────────────────
        // MARQUEE (HERO LOGOS)
        // ──────────────────────────────────────────────────────────
        async function fetchMarquee() {
            try {
                let res = await fetch(`${BASE_URL}/api/compare.php?action=get_marquee_logos`);
                let json = await res.json();
                if (json.success && json.data.length > 0) {
                    let html = json.data.map(l => `<img src="${l.image}" alt="${l.name}">`).join('');
                    // Clone track for seamless loop
                    document.getElementById('marqueeTrack1').innerHTML = html;
                    document.getElementById('marqueeTrack2').innerHTML = html;
                }
            } catch (e) { }
        }

        // ──────────────────────────────────────────────────────────
        // STEP 1: INITIALIZE COURSE DROPDOWN
        // ──────────────────────────────────────────────────────────
        async function fetchCourses() {
            try {
                let res = await fetch(`${BASE_URL}/api/compare.php?action=get_courses`);
                let json = await res.json();
                if (json.success) {
                    allCourses = json.data;
                    let sel = document.getElementById('masterCourse');
                    json.data.forEach(c => {
                        sel.add(new Option(c.text, c.id));
                    });
                }
            } catch (e) { }
        }

        async function onCourseSelect() {
            selectedCourseId = document.getElementById('masterCourse').value;
            resetSlots();
            hideComparisonTable();

            const modeSel = document.getElementById('masterMode');
            modeSel.innerHTML = '<option value="">Select Mode</option>';
            modeSel.disabled = true;

            if (!selectedCourseId) return;

            try {
                let res = await fetch(`${BASE_URL}/api/compare.php?action=get_modes&course_id=${selectedCourseId}`);
                let json = await res.json();
                if (json.success && json.data.length > 0) {
                    json.data.forEach(m => {
                        modeSel.add(new Option(m.mode_name, m.id));
                    });
                    modeSel.disabled = false;

                    // Auto-select mode if only 1 exists
                    if (json.data.length === 1) {
                        modeSel.value = json.data[0].id;
                        onModeSelect();
                    }
                }
            } catch (e) { }
        }

        async function onModeSelect() {
            selectedModeId = document.getElementById('masterMode').value;
            resetSlots();
            hideComparisonTable();

            if (!selectedModeId) return;

            try {
                let res = await fetch(`${BASE_URL}/api/compare.php?action=get_filtered_universities&course_id=${selectedCourseId}&mode_id=${selectedModeId}`);
                let json = await res.json();
                if (json.success) {
                    filteredUnis = json.data;
                    updateCompareBtnState();
                }
            } catch (e) { }
        }

        // ──────────────────────────────────────────────────────────
        // STEP 2: SELECTING UNIVERSITIES (MODAL & BOXES)
        // ──────────────────────────────────────────────────────────
        function openUniModal(index) {
            if (!selectedCourseId || !selectedModeId) {
                alert("Please select a Course and Mode first.");
                return;
            }
            activeBoxIndex = index;
            document.getElementById('uniModalBg').classList.add('active');
            document.getElementById('uniSearch').value = '';
            filterUniversities();
            setTimeout(() => document.getElementById('uniSearch').focus(), 100);
        }
        function closeUniModal() {
            document.getElementById('uniModalBg').classList.remove('active');
            activeBoxIndex = null;
        }

        function filterUniversities() {
            let q = document.getElementById('uniSearch').value.toLowerCase();
            let wrap = document.getElementById('uniList');

            // Find which IDs are already in other boxes to exclude them
            let usedIds = Object.values(selectedUniIds).filter(id => id !== null);

            let filtered = filteredUnis.filter(u => {
                if (usedIds.includes(u.id)) return false;
                return u.text.toLowerCase().includes(q);
            });

            if (filtered.length === 0) {
                wrap.innerHTML = '<div class="search-item-empty">No matching universities available.</div>';
                return;
            }

            let html = '';
            filtered.forEach(u => {
                html += `<div class="search-item" onclick="selectUniversity(${u.id}, '${u.text.replace(/'/g, "\\'")}', '${u.image}')">
                ${u.text}
            </div>`;
            });
            wrap.innerHTML = html;
        }

        function selectUniversity(id, text, image) {
            if (activeBoxIndex) {
                selectedUniIds[activeBoxIndex] = id;
                selectedUniData[activeBoxIndex] = { text, image };
                renderBoxes();
                updateCompareBtnState();
                if (document.getElementById('tableWrapper').classList.contains('active')) {
                    executeCompare();
                }
            }
            closeUniModal();
        }

        function removeUniFromBox(index, e) {
            e.stopPropagation();
            selectedUniIds[index] = null;
            selectedUniData[index] = null;
            renderBoxes();
            updateCompareBtnState();
            if (document.getElementById('tableWrapper').classList.contains('active')) {
                executeCompare();
            }
        }

        function renderBoxes() {
            [1, 2, 3].forEach(i => {
                let box = document.getElementById('uni-box-' + i);
                if (selectedUniIds[i]) {
                    let d = selectedUniData[i];
                    box.className = "uni-box";
                    box.onclick = null; // Removed so clicking the box directly doesn't reopen modal randomly unless they click a replace btn, but let's keep it simple: x to remove.

                    let imgHtml = d.image ? `<img src="${d.image}" class="inner-logo">` : `<strong style="text-align:center;">${d.text}</strong>`;
                    box.innerHTML = `
                    <button class="box-close" onclick="removeUniFromBox(${i}, event)" title="Remove">×</button>
                    ${imgHtml}
                `;
                } else {
                    box.className = "uni-box empty";
                    box.onclick = () => openUniModal(i);
                    box.innerHTML = `<div class="plus-icon">+</div><div>Select University</div>`;
                }
            });
        }

        function resetSlots() {
            selectedUniIds = { 1: null, 2: null, 3: null };
            selectedUniData = { 1: null, 2: null, 3: null };
            renderBoxes();
            updateCompareBtnState();
        }

        function updateCompareBtnState() {
            let count = Object.values(selectedUniIds).filter(id => id !== null).length;
            document.getElementById('compareBtn').disabled = (count === 0);
        }

        function hideComparisonTable() {
            document.getElementById('tableWrapper').classList.remove('active');
            document.getElementById('tableWrapper').innerHTML = '';
        }

        // ──────────────────────────────────────────────────────────
        // STEP 3: COMPARISON GRID RENDER
        // ──────────────────────────────────────────────────────────
        function executeCompare() {
            let activePairs = [];
            for (let i = 1; i <= 3; i++) {
                if (selectedUniIds[i]) {
                    activePairs.push({ index: i, id: selectedUniIds[i] });
                }
            }

            if (activePairs.length === 0) {
                hideComparisonTable();
                return;
            }

            let btn = document.getElementById('compareBtn');
            btn.innerHTML = 'Loading...';
            btn.disabled = true;

            let idsStr = activePairs.map(p => p.id).join(',');

            fetch(`${BASE_URL}/api/compare.php?action=get_bulk_comparison&course_id=${selectedCourseId}&mode_id=${selectedModeId}&uni_ids=${idsStr}`)
                .then(res => res.json())
                .then(json => {
                    if (json.success && json.data.length > 0) {
                        // To keep the order bound to our boxes exactly:
                        let orderedUnis = [];
                        activePairs.forEach(p => {
                            let matchingData = json.data.find(d => d.uni_id == p.id);
                            if (matchingData) {
                                matchingData._boxIndex = p.index;
                                orderedUnis.push(matchingData);
                            }
                        });
                        renderTable(json.course_name, orderedUnis);
                        setTimeout(() => {
                            document.getElementById('tableWrapper').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    }
                })
                .catch(e => console.error(e))
                .finally(() => {
                    btn.innerHTML = `Compare Now <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px;"><polyline points="5 12 19 12"/><polyline points="12 5 19 12 12 19"/></svg>`;
                    btn.disabled = false;
                });
        }

        const ICONS = {
            location: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>`,
            calendar: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>`,
            building: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path></svg>`,
            check_shield: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>`,
            user_check: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>`,
            rupee: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h12"></path><path d="M6 8h12"></path><path d="M6 13h8.5a4.5 4.5 0 1 0 0-9H6"></path><path d="M14.5 13L6 21"></path></svg>`,
            book: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>`,
            globe: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>`,
            edit: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>`,
            credit_card: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>`,
            star: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>`,
            award: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>`,
            target: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>`,
            gift: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>`,
            phone: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>`
        };

        // Helper for absolute urls
        function getAbsoluteUrl(path) {
            if (!path) return '';
            if (path.startsWith('http')) return path;
            if (path.startsWith('/')) return path;
            return UPLOAD_URL + '/' + path.replace(/^[\/\\]+/, '');
        }

        // Helper for rendering an entire logical row
        function buildRow(label, iconKey, valueExtractFn, unis) {
            let svg = ICONS[iconKey] || '';
            let labelCell = `<div class="v2-cell v2-label-cell" style="color:var(--accent-blue);"><span style="display:inline-flex; width:20px; align-items:center; justify-content:center;">${svg}</span> ${label}</div>`;

            let valCells = unis.map(u => `<div class="v2-cell v2-value-cell">${valueExtractFn(u)}</div>`).join('');

            return `<div class="v2-row">${labelCell}${valCells}</div>`;
        }

        function renderTable(courseName, unis) {
            let wrap = document.getElementById('tableWrapper');

            let headerRowTokens = [
                `<div class="v2-header-cell features-label" style="text-align:center;">Features</div>`
            ];

            let i = 0;
            unis.forEach(u => {
                let boxIndex = u._boxIndex;
                // The clickable header to change university:
                let content = u.uni_image ? `<img src="${u.uni_image}" class="v2-header-logo" style="margin:0;">` : `<h3 style="margin:0;font-size:0.9rem;color:var(--accent-blue);">${u.uni_name}</h3>`;

                headerRowTokens.push(`
                <div class="v2-header-cell" style="cursor:pointer; background:#fff; border-bottom:4px solid var(--accent-blue);" onclick="openUniModal(${boxIndex})">
                    <div style="display:flex; align-items:center; gap:8px;">
                        ${content}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-m)" stroke-width="2" style="margin-top:2px;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>
            `);
                i++;
            });

            // Add a "Select University" empty box if less than 3 selected
            for (let j = i + 1; j <= 3; j++) {
                // Find which box index is truly empty
                let emptyIdx = [1, 2, 3].find(idx => !selectedUniIds[idx]);
                // If we find one, we attach openUniModal, otherwise we just stop. 
                // Actually it's simpler: if activePairs had 2 items, the next column should just trigger modal for the next empty box.
                if (emptyIdx) {
                    headerRowTokens.push(`
                    <div class="v2-header-cell" style="cursor:pointer; background:#fff; border-bottom:4px solid var(--accent-blue); justify-content:center;" onclick="openUniModal(${emptyIdx})">
                        <div style="color:var(--text-m); font-weight:600;">+ Select University</div>
                    </div>
                `);
                }
            }

            // Ensure strictly 3 columns + 1 label column by padding `unis` array with nulls if < 3
            while (unis.length < 3) { unis.push(null); }

            let headerRowHTML = `<div class="v2-row v2-row-header">${headerRowTokens.join('')}</div>`;

            let rowsHtml = "";

            const cellFn = (u, fn) => u ? fn(u) : '—';

            let courseShort = courseName.split(' ')[0] || "Program";

            rowsHtml += buildRow("UNIVERSITY NAME", "award", u => cellFn(u, x => `<strong style="font-size:1.05rem;">${x.uni_name}</strong>`), unis);
            rowsHtml += buildRow("LOCATION", "location", u => cellFn(u, x => `<strong style="color:var(--text);">${x.location}</strong>`), unis);
            rowsHtml += buildRow("ESTABLISHED YEAR", "calendar", u => cellFn(u, x => `<strong>${x.established}</strong>`), unis);
            rowsHtml += buildRow("UNIVERSITY TYPE", "building", u => cellFn(u, x => `<strong>${x.uni_type}</strong>`), unis);

            rowsHtml += buildRow("ACCREDITATIONS & APPROVALS", "check_shield", u => cellFn(u, x => {
                if (!x.accreditations || x.accreditations.length === 0) return '—';
                let block = `<div style="display:flex; gap:6px; flex-wrap:wrap; justify-content:center;">`;
                x.accreditations.forEach(a => {
                    if (a.image) block += `<img src="${a.image}" title="${a.name}" style="width:34px;height:34px;border-radius:50%;object-fit:contain;border:1px solid var(--border);padding:2px;background:#fff; box-shadow:var(--shadow-sm);">`;
                    else block += `<span style="font-size:11px; font-weight:600; border:1px solid var(--border); padding:4px 6px; border-radius:4px;">${a.name.substring(0, 4)}</span>`;
                });
                return block + `</div>`;
            }), unis);

            rowsHtml += buildRow(`${courseShort.toUpperCase()} ELIGIBILITY`, "user_check", u => cellFn(u, x => `<strong style="font-size:0.9rem;">${x.eligibility}</strong>`), unis);

            rowsHtml += buildRow(`${courseShort.toUpperCase()} FEES STRUCTURE`, "rupee", u => cellFn(u, x => {
                return `<div class="desktop-fee">
                        <div style="color:var(--danger); font-weight:800; font-size:1.4rem; text-align:center; line-height:1;">
                            <span style="font-size:1rem; display:block;">₹</span>
                            ${x.fees.replace('₹ ', '')}
                        </div>
                        <div style="color:#4b5563; font-weight:600; font-size:0.85rem; text-align:left; line-height:1.2;">
                            Per<br>Semester
                        </div>
                    </div>
                    <div class="mobile-fee">
                        <div style="color:var(--danger); font-weight:800; font-size:1.3rem;">₹ ${x.fees.replace('₹ ', '')}</div>
                        <div style="color:#4b5563; font-weight:600; font-size:0.8rem;">Total Fees</div>
                    </div>`;
            }), unis);

            rowsHtml += buildRow(`${courseShort.toUpperCase()} SPECIALIZATION`, "book", u => cellFn(u, x => {
                if (!x.specializations || x.specializations.length === 0) return '—';
                return `<div style="max-height:160px; overflow-y:auto; padding-right:4px;">
                      <ul class="v2-list" style="font-weight:600; font-size:0.85rem; margin:0;">` + x.specializations.map(s => `<li style="margin-bottom:6px;">${s}</li>`).join('') + `</ul>
                    </div>`;
            }), unis);

            rowsHtml += buildRow("EDUCATION MODE", "globe", u => cellFn(u, x => `<strong style="color:var(--text);">${x.education_mode}</strong>`), unis);
            rowsHtml += buildRow("EXAM MODE", "edit", u => cellFn(u, x => `<strong>${x.exam_modes}</strong>`), unis);

            rowsHtml += buildRow("EMI FACILITY", "credit_card", u => cellFn(u, x => {
                return x.emi_facility === 'Yes' ? `<span class="v2-icon-success" style="font-size:1.8rem; font-weight:400;">✓</span>` : `<span class="v2-icon-danger" style="font-size:1.8rem; font-weight:400;">✕</span>`;
            }), unis);

            rowsHtml += buildRow("ADVANTAGES", "star", u => cellFn(u, x => {
                if (!x.advantages || x.advantages.length === 0) return '—';
                return `<ul class="v2-list" style="padding-left:1.5rem;">` + x.advantages.map(a => `<li style="font-weight:700;color:var(--text); font-size:0.8rem; margin-bottom:0.5rem; line-height:1.3;">${a}</li>`).join('') + `</ul>`;
            }), unis);

            rowsHtml += buildRow("STUDENT REVIEW", "star", u => cellFn(u, x => {
                if (x.rating === 'N/A') return '—';
                let starsHtml = '';
                for (let k = 1; k <= 5; k++) {
                    if (k <= Math.round(x.rating)) starsHtml += `<span style="color:#facc15; font-size:1.3rem;">★</span>`;
                    else starsHtml += `<span style="color:#cbd5e1; font-size:1.3rem;">★</span>`;
                }
                return `<div style="display:flex; flex-direction:column; align-items:center; gap:4px;">
                <strong style="font-size:1.1rem;">${x.rating}/5</strong>
                <div>${starsHtml}</div>
            </div>`;
            }), unis);

            rowsHtml += buildRow("DEGREE CERTIFICATE", "award", u => cellFn(u, x => {
                if (x.sample_certificate) {
                    let imgUrl = getAbsoluteUrl(x.sample_certificate);
                    return `<div style="display:flex; flex-direction:column; align-items:center; gap:0.5rem; border:1px solid var(--border); padding:0.5rem; border-radius:var(--radius-sm); width:120px; margin:0 auto; cursor:pointer;" onclick="openLightbox('${imgUrl}')">
                            <img src="${imgUrl}" style="width:100px; height:70px; object-fit:contain;">
                            <span style="font-size:0.75rem; color:var(--text-m); font-weight:600;">👁 View</span>
                        </div>`;
                }
                return '—';
            }), unis);

            rowsHtml += buildRow("PLACEMENT ASSISTANCE", "target", u => cellFn(u, x => {
                return x.placement_assistance === 'Yes' ? `<span style="border:2px solid var(--success); color:var(--success); font-weight:800; padding:4px 16px; border-radius:20px; font-size:0.85rem;">YES</span>` : `<span style="border:2px solid var(--danger); color:var(--danger); font-weight:800; padding:4px 16px; border-radius:20px; font-size:0.85rem;">NO</span>`;
            }), unis);

            rowsHtml += buildRow("SCHOLARSHIP", "gift", u => cellFn(u, x => {
                if (x.fees_discount > 0) {
                    return `<div class="desktop-scholarship">
                            <strong style="font-size:1rem;">Upto ₹${x.fees_discount}</strong>
                            <button class="btn btn-success" onclick="openScholarshipModal('${x.uni_name.replace(/'/g, "\\'")}', '${x.uni_image ? x.uni_image : ''}', '₹${x.fees_discount}')" style="padding:0.4rem 1rem; border-radius:30px; font-size:0.85rem; height:auto;">Claim Now</button>
                         </div>
                         <div class="mobile-scholarship">
                            <strong style="font-size:0.9rem;">Upto ₹${x.fees_discount}</strong>
                            <button class="btn btn-success" onclick="openScholarshipModal('${x.uni_name.replace(/'/g, "\\'")}', '${x.uni_image ? x.uni_image : ''}', '₹${x.fees_discount}')" style="padding:0.4rem 1rem; border-radius:30px; font-size:0.8rem; height:auto;">Claim Now</button>
                         </div>`;
                } else if (x.scholarship === 'Available') {
                    return `<div class="desktop-scholarship">
                            <strong style="font-size:1rem;">Available</strong>
                            <button class="btn btn-success" onclick="openScholarshipModal('${x.uni_name.replace(/'/g, "\\'")}', '${x.uni_image ? x.uni_image : ''}', 'Available Discount')" style="padding:0.4rem 1rem; border-radius:30px; font-size:0.85rem; height:auto;">Claim Now</button>
                         </div>
                         <div class="mobile-scholarship">
                            <strong style="font-size:0.9rem;">Available</strong>
                            <button class="btn btn-success" onclick="openScholarshipModal('${x.uni_name.replace(/'/g, "\\'")}', '${x.uni_image ? x.uni_image : ''}', 'Available Discount')" style="padding:0.4rem 1rem; border-radius:30px; font-size:0.8rem; height:auto;">Claim Now</button>
                         </div>`;
                }
                return '—';
            }), unis);

            rowsHtml += buildRow("COURSE BROCHURE", "book", u => cellFn(u, x => {
                if (x.brochure_file) {
                    let broUrl = getAbsoluteUrl(x.brochure_file);
                    return `<button onclick="openBrochureModal('${broUrl}')" style="background:#fff; border:2px solid #10b981; border-radius:8px; display:flex; flex-direction:column; align-items:center; width:100%; max-width:140px; margin:0 auto; padding:0.5rem; cursor:pointer; color:#10b981;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span style="font-size:0.75rem; font-weight:800; text-transform:uppercase; margin-top:4px;">Download<br>Brochure</span>
                         </button>`;
                }
                return '—';
            }), unis);

            rowsHtml += buildRow("GET FREE COUNSELING", "phone", u => cellFn(u, x => {
                return `<button class="btn btn-primary" onclick="opencounselingModal('${x.uni_name.replace(/'/g, "\\'")}', '${x.uni_image ? x.uni_image : ''}')" style="background:#1b84ff; border-color:#1b84ff; border-radius:8px; display:flex; flex-direction:column; align-items:center; width:100%; max-width:140px; margin:0 auto; padding:0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <span style="font-size:0.7rem; font-weight:700; color:#fff; text-transform:uppercase; margin-top:2px;">Book Free<br>counseling</span>
                     </button>`;
            }), unis);

            rowsHtml += buildRow("", "", u => cellFn(u, x => `<a href="${x.view_link}" class="v2-btn-know" style="background:#ffc107; color:#000; display:flex; justify-content:center; align-items:center;" target="_blank">Know More</a>`), unis);

            let finalHtml = `
            <div class="v2-grid" style="grid-template-columns: 260px repeat(3, 1fr); margin-bottom: 2rem;">
               ${headerRowHTML}
               ${rowsHtml}
            </div>
        `;
            wrap.innerHTML = finalHtml;
            wrap.classList.add('active');
        }

        // Lightbox minimal fallback within V2
        function openLightbox(src) {
            if (!document.getElementById('lightboxBg')) {
                document.body.insertAdjacentHTML('beforeend', `
            <div class="uni-modal-bg" id="lightboxBg" onclick="closeLightbox(event)" style="z-index:9999;">
                <div style="max-width:90%; max-height:90%; position:relative;">
                    <img id="lightboxImg" src="${src}" style="max-width:100%; max-height:90vh; border-radius:8px; display:block;">
                </div>
            </div>`);
            } else {
                document.getElementById('lightboxImg').src = src;
            }
            setTimeout(() => document.getElementById('lightboxBg').classList.add('active'), 50);
        }
        window.closeLightbox = function (e) {
            if (e.target.id === 'lightboxBg') {
                document.getElementById('lightboxBg').classList.remove('active');
            }
        }

        // Brochure Modal functions
        function openBrochureModal(url) {
            pendingBrochureUrl = url;
            let cSelects = document.querySelectorAll('.dynamic-course-select');
            cSelects.forEach(cSelect => {
                cSelect.innerHTML = '<option value="">Select Your Course</option>';
                allCourses.forEach(c => {
                    cSelect.add(new Option(c.text, c.text)); // passing actual name string for leads
                });
            });
            document.getElementById('brochureModalBg').classList.add('active');
        }



        // Form logic is handled by global assets/js/lead_form.js globally via submitGenericLeadForm

        function closeBrochureModal() {
            document.getElementById('brochureModalBg').classList.remove('active');
            // Let the global modal resetter handle state toggles
            pendingBrochureUrl = null;
        }

        // Scholarship Modal functions
        function openScholarshipModal(uniName, uniImage, discountStr) {
            // Populate Dynamic Header Details
            document.getElementById('sch_uni_name').innerText = uniName;

            // Set dynamic uniName in standard form structure
            let customFieldsSlot = document.querySelector('#scholarshipFormGlobal_Area form');
            if (!customFieldsSlot.querySelector('input[name="uni_name"]')) {
                customFieldsSlot.insertAdjacentHTML('afterbegin', `<input type="hidden" name="uni_name" value="">`);
            }
            customFieldsSlot.querySelector('input[name="uni_name"]').value = uniName;

            let imgEl = document.getElementById('sch_uni_img');
            if (uniImage) {
                imgEl.src = getAbsoluteUrl(uniImage);
                imgEl.style.display = 'block';
            } else {
                imgEl.style.display = 'none';
            }

            // Custom heading behavior
            document.getElementById('sch_heading').innerText = `Claim ${discountStr} Discount`;

            // Sync up generic states
            document.getElementById('scholarshipUiTop').style.display = 'block';

            let formWrapId = 'scholarshipFormGlobal';
            document.getElementById(formWrapId + '_Area').style.display = 'block';
            document.getElementById(formWrapId + '_Success').style.display = 'none';

            let cSelects = document.querySelectorAll('#scholarshipModalBg .dynamic-course-select');
            cSelects.forEach(cSelect => {
                cSelect.innerHTML = '<option value="">Select Your Course</option>';
                allCourses.forEach(c => {
                    cSelect.add(new Option(c.text, c.text));
                });
            });
            document.getElementById('scholarshipModalBg').classList.add('active');
        }

        function closeScholarshipModal() {
            document.getElementById('scholarshipModalBg').classList.remove('active');
        }

        // counseling Modal functions
        function opencounselingModal(uniName, uniImage) {
            document.getElementById('mnt_uni_name').innerText = uniName;

            let customFieldsSlot = document.querySelector('#counselingFormGlobal_Area form');
            if (!customFieldsSlot.querySelector('input[name="uni_name"]')) {
                customFieldsSlot.insertAdjacentHTML('afterbegin', `<input type="hidden" name="uni_name" value="">`);
            }
            customFieldsSlot.querySelector('input[name="uni_name"]').value = uniName;

            let imgEl = document.getElementById('mnt_uni_img');
            if (uniImage) {
                imgEl.src = getAbsoluteUrl(uniImage);
                imgEl.style.display = 'block';
            } else {
                imgEl.style.display = 'none';
            }

            if (uniName === 'General Counseling') {
                document.getElementById('counselingUiTop').style.display = 'none';
            } else {
                document.getElementById('counselingUiTop').style.display = 'block';
            }

            let formWrapId = 'counselingFormGlobal';
            document.getElementById(formWrapId + '_Area').style.display = 'block';
            document.getElementById(formWrapId + '_Success').style.display = 'none';

            let cSelects = document.querySelectorAll('#counselingModalBg .dynamic-course-select');
            cSelects.forEach(cSelect => {
                cSelect.innerHTML = '<option value="">Select Your Course</option>';
                allCourses.forEach(c => {
                    cSelect.add(new Option(c.text, c.text));
                });
            });
            document.getElementById('counselingModalBg').classList.add('active');
        }

        function closecounselingModal() {
            document.getElementById('counselingModalBg').classList.remove('active');
        }
    </script>
    <script src="<?= BASE_URL ?>/assets/js/lead_form.js"></script>
</body>

</html>