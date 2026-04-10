/**
 * lead_form.js
 * Generic Lead Capture Form Handler for SODE AI Tools.
 * Attach this script to any page that includes `includes/lead_form.php`.
 */

function submitGenericLeadForm(e, formElement) {
    e.preventDefault();
    
    // Get the unique configured ID for this specific form instance
    const wrapperId = formElement.getAttribute('data-wrapper-id');
    const submitBtn = formElement.querySelector('.lead-submit-btn');
    const origBtnText = submitBtn.innerText;
    
    submitBtn.innerText = 'Submitting...';
    submitBtn.disabled = true;

    // Use FormData to dynamically capture all fields natively
    let fd = new FormData(formElement);
    
    // If hidden source url isn't set statically, ensure it dynamically captures current context
    if(!fd.get('source_url')) {
        fd.set('page_url', window.location.href);
    } else {
        fd.set('page_url', fd.get('source_url'));
    }

    fetch(BASE_URL + '/api/submit_lead.php', { method: 'POST', body: fd })
    .then(res => res.json())
    .then(json => {
        if(json.success) {
            // Hide Form Area, Show Success Area
            document.getElementById(wrapperId + '_Area').style.display = 'none';
            document.getElementById(wrapperId + '_Success').style.display = 'block';
            
            // Check if there's a global pending URL handler (optional integration for brochure downloads specifically)
            if (typeof pendingBrochureUrl !== 'undefined' && pendingBrochureUrl) {
                setTimeout(() => window.open(pendingBrochureUrl, '_blank'), 500);
            }
        } else {
            alert(json.error || 'Something went wrong while submitting.');
        }
        formElement.reset();
    })
    .catch(err => {
        console.error('Lead Form Submission Error:', err);
        alert('A network error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.innerText = origBtnText;
        submitBtn.disabled = false;
    });
}

// Global modal close triggers associated with dynamic forms
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('trigger-modal-close')) {
        const formId = e.target.getAttribute('data-form-id');
        
        // Context specific callback or global defaults
        if (typeof closeBrochureModal === 'function' && document.getElementById('brochureModalBg') && document.getElementById('brochureModalBg').classList.contains('active')) {
            closeBrochureModal();
        }
        if (typeof closeScholarshipModal === 'function' && document.getElementById('scholarshipModalBg') && document.getElementById('scholarshipModalBg').classList.contains('active')) {
            closeScholarshipModal();
        }
        if (typeof closecounselingModal === 'function' && document.getElementById('counselingModalBg') && document.getElementById('counselingModalBg').classList.contains('active')) {
            closecounselingModal();
        }
        
        // Reset local UI State for next time
        setTimeout(() => {
            document.getElementById(formId + '_Area').style.display = 'block';
            document.getElementById(formId + '_Success').style.display = 'none';
        }, 300);
    }
});
