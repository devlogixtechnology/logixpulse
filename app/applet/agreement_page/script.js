document.addEventListener('DOMContentLoaded', () => {
    const signBtn = document.querySelector('.sign-trigger-btn');
    const submitBtn = document.getElementById('submit-signature-btn');
    const signatureArea = document.getElementById('client-signature-area');
    const signatureBox = document.querySelector('.client-box');

    if(signBtn) {
        signBtn.addEventListener('click', () => {
            // Simulate user signing the document
            
            // 1. Remove button and show signature font
            signatureArea.innerHTML = '<span class="signature-font">Acme Cloud Rep</span>';
            
            // 2. Change styling of the box
            signatureBox.classList.remove('action-required');
            signatureBox.style.border = '1px solid #E5E7EE';
            signatureBox.style.backgroundColor = '#FFFFFF';
            
            // 3. Update pending text to date
            const details = signatureBox.querySelector('.signature-details');
            details.innerHTML = '<span>Client Representative</span><span>Sep 16, 2026</span>';

            // 4. Enable Submit Button
            submitBtn.disabled = false;
        });
    }

    if(submitBtn) {
        submitBtn.addEventListener('click', () => {
            alert('Signature submitted successfully! (This is a static shell demo)');
        });
    }
});
