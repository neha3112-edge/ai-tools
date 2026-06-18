/**
 * lead_form.js
 * Generic Lead Capture Form Handler for SODE AI Tools.
 * Supports UTM extraction from both standard and hash-fragment URLs.
 *
 * URL patterns handled:
 *   ?utm_source=Google_Ads&utm_medium=...
 *   #anchor?utm_source=Google_Ads&utm_medium=...   (hash-based UTMs)
 *   Mixed: ?gad_source=1#section?utm_source=...
 */

// ─── UTM PARSER ────────────────────────────────────────────────────────────

/**
 * Extract UTM params from any URL (handles hash-fragment UTMs too).
 * Priority: real query string → hash-fragment query string.
 */
function extractUtmParams(urlStr) {
    const utm = {
        utm_source:   '',
        utm_medium:   '',
        utm_campaign: '',
        utm_term:     '',
        utm_content:  ''
    };

    try {
        const href = urlStr || window.location.href;

        // Helper: parse a query string and pull UTM values
        function parseQs(qs) {
            if (!qs) return;
            const params = new URLSearchParams(qs.startsWith('?') ? qs.slice(1) : qs);
            ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(k) {
                const v = params.get(k);
                if (v) utm[k] = v;
            });
        }

        // 1. Parse the real query string
        const qIdx = href.indexOf('?');
        const hIdx = href.indexOf('#');

        if (qIdx !== -1) {
            // Query string may end at '#' or end of string
            const qEnd = hIdx !== -1 && hIdx > qIdx ? hIdx : href.length;
            parseQs(href.slice(qIdx + 1, qEnd));
        }

        // 2. Parse query string embedded in the hash fragment: #something?utm_source=...
        if (hIdx !== -1) {
            const hashPart = href.slice(hIdx + 1); // after '#'
            const hashQIdx = hashPart.indexOf('?');
            if (hashQIdx !== -1) {
                parseQs(hashPart.slice(hashQIdx + 1));
            }
        }
    } catch (e) {
        console.warn('[LeadForm] UTM parse error:', e);
    }

    return utm;
}

/** Inject UTM values into every hidden .utm-field inside a form element */
function injectUtmsIntoForm(formElement) {
    const utms = extractUtmParams(window.location.href);
    ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(k) {
        const el = formElement.querySelector('input[name="' + k + '"]');
        if (el && !el.value) el.value = utms[k] || '';
    });
}

// ─── FORM SUBMIT HANDLER ────────────────────────────────────────────────────

function submitGenericLeadForm(e, formElement) {
    e.preventDefault();

    // Inject UTMs just before building FormData
    injectUtmsIntoForm(formElement);

    // Get the unique configured ID for this specific form instance
    const wrapperId = formElement.getAttribute('data-wrapper-id');
    const submitBtn = formElement.querySelector('.lead-submit-btn');
    const origBtnText = submitBtn ? submitBtn.innerText : '';

    if (submitBtn) {
        submitBtn.innerText = 'Submitting...';
        submitBtn.disabled = true;
    }

    // Use FormData to dynamically capture all fields natively
    const fd = new FormData(formElement);

    // Set full page URL (including hash+UTM fragment) as page_url
    fd.set('page_url', window.location.href);

    fetch(BASE_URL + '/api/submit_lead.php', { method: 'POST', body: fd })
    .then(function(res) { return res.json(); })
    .then(function(json) {
        if (json.success) {
            // Hide Form Area, Show Success Area
            const areaEl   = document.getElementById(wrapperId + '_Area');
            const successEl = document.getElementById(wrapperId + '_Success');
            if (areaEl)    areaEl.style.display   = 'none';
            if (successEl) successEl.style.display = 'block';

            // Brochure download integration
            if (typeof pendingBrochureUrl !== 'undefined' && pendingBrochureUrl) {
                setTimeout(function() { window.open(pendingBrochureUrl, '_blank'); }, 500);
            }

            // Fire unlock event for compare_unlock lead type
            const leadType = fd.get('lead_type');
            if (leadType === 'compare_unlock') {
                document.dispatchEvent(new CustomEvent('compareUnlockSuccess'));
            }
        } else {
            alert(json.error || 'Something went wrong while submitting.');
        }
        formElement.reset();
    })
    .catch(function(err) {
        console.error('Lead Form Submission Error:', err);
        alert('A network error occurred. Please try again.');
    })
    .finally(function() {
        if (submitBtn) {
            submitBtn.innerText = origBtnText;
            submitBtn.disabled  = false;
        }
    });
}

// ─── MODAL CLOSE HANDLER ────────────────────────────────────────────────────

document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('trigger-modal-close')) {
        const formId = e.target.getAttribute('data-form-id');

        if (typeof closeBrochureModal === 'function' &&
            document.getElementById('brochureModalBg') &&
            document.getElementById('brochureModalBg').classList.contains('active')) {
            closeBrochureModal();
        }
        if (typeof closeScholarshipModal === 'function' &&
            document.getElementById('scholarshipModalBg') &&
            document.getElementById('scholarshipModalBg').classList.contains('active')) {
            closeScholarshipModal();
        }
        if (typeof closecounselingModal === 'function' &&
            document.getElementById('counselingModalBg') &&
            document.getElementById('counselingModalBg').classList.contains('active')) {
            closecounselingModal();
        }
        if (typeof closeCompareUnlockModal === 'function' &&
            document.getElementById('compareUnlockModalBg') &&
            document.getElementById('compareUnlockModalBg').classList.contains('active')) {
            closeCompareUnlockModal();
        }

        // Reset local UI State for next time
        setTimeout(function() {
            const areaEl    = document.getElementById(formId + '_Area');
            const successEl = document.getElementById(formId + '_Success');
            if (areaEl)    areaEl.style.display    = 'block';
            if (successEl) successEl.style.display  = 'none';
        }, 300);
    }
});
