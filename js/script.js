/**
 * script.js
 * Frontend visual logic and form handling for authentication UI.
 * NOTE: This is for visual/demo purposes only. No real backend is connected.
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ========================================================
       1. TRADITIONAL LOGIN FORM HANDLING
       ======================================================== */
    const loginForm = document.getElementById('traditional-login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            // Prevent actual form submission to avoid page crash/reload
            e.preventDefault();
            
            const email = document.getElementById('login-email').value;
            const btn = document.getElementById('login-submit');
            
            // Visual feedback
            const originalText = btn.innerText;
            btn.innerText = 'Signing in...';
            btn.style.opacity = '0.8';
            
            // Simulate network request delay (Frontend demo only)
            setTimeout(() => {
                btn.innerText = originalText;
                btn.style.opacity = '1';
                alert(`Frontend Demo: Login attempt captured for ${email}.\n(No backend is connected yet).`);
            }, 800);
        });
    }

    /* ========================================================
       2. MAGIC CODE REQUEST FORM HANDLING
       ======================================================== */
    const magicCodeForm = document.getElementById('magic-code-request-form');
    if (magicCodeForm) {
        magicCodeForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const email = document.getElementById('magic-code-email').value;
            const btn = document.getElementById('magic-code-submit');
            
            btn.innerText = 'Sending...';
            btn.style.opacity = '0.8';
            
            // Simulate sending code, then navigate to verify screen
            setTimeout(() => {
                // Navigate to the verification screen
                // In a real app, you might pass the email via URL parameters or state
                window.location.href = 'verify-code.html';
            }, 600);
        });
    }

    /* ========================================================
       3. VERIFY MAGIC CODE (OTP) INPUT LOGIC
       ======================================================== */
    const verifyForm = document.getElementById('verify-code-form');
    const otpInputs = document.querySelectorAll('.otp-input');
    
    if (otpInputs.length > 0) {
        // Auto-focus the first input on load
        otpInputs[0].focus();

        otpInputs.forEach((input, index) => {
            // Handle Typing (Move to next input)
            input.addEventListener('input', (e) => {
                // Ensure only numbers are entered
                input.value = input.value.replace(/[^0-9]/g, '');
                
                if (input.value !== '') {
                    // Move focus to the next input if it exists
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                }
            });

            // Handle Backspace (Move to previous input)
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    if (input.value === '' && index > 0) {
                        // If current box is empty, move to previous box
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = ''; // Clear it slightly for better UX
                    } else {
                        // Normal backspace clears current box
                        input.value = '';
                    }
                }
            });

            // Handle Paste (Fill multiple boxes)
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, otpInputs.length);
                
                if (pastedData) {
                    for (let i = 0; i < pastedData.length; i++) {
                        if (i + index < otpInputs.length) {
                            otpInputs[i + index].value = pastedData[i];
                        }
                    }
                    // Focus the last filled input or the very last one
                    const focusIndex = Math.min(index + pastedData.length, otpInputs.length - 1);
                    otpInputs[focusIndex].focus();
                }
            });
        });
    }

    if (verifyForm) {
        verifyForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Gather the code
            let code = '';
            otpInputs.forEach(input => code += input.value);
            
            if (code.length < 6) {
                alert('Please enter all 6 digits.');
                return;
            }

            const btn = document.getElementById('verify-submit');
            const originalText = btn.innerText;
            btn.innerText = 'Verifying...';
            btn.style.opacity = '0.8';

            setTimeout(() => {
                btn.innerText = originalText;
                btn.style.opacity = '1';
                alert(`Frontend Demo: Code ${code} verified successfully!\n(No backend is connected yet).`);
            }, 800);
        });
    }

    // Handle "Resend Code" link visually
    const resendLink = document.getElementById('resend-code-link');
    if (resendLink) {
        resendLink.addEventListener('click', (e) => {
            e.preventDefault();
            const originalText = resendLink.innerText;
            resendLink.innerText = 'Sending...';
            resendLink.style.pointerEvents = 'none';
            resendLink.style.color = 'var(--color-text-muted)';
            
            setTimeout(() => {
                resendLink.innerText = 'Sent!';
                setTimeout(() => {
                    resendLink.innerText = originalText;
                    resendLink.style.pointerEvents = 'auto';
                    resendLink.style.color = 'var(--color-primary)';
                }, 2000);
            }, 1000);
        });
    }

    /* ========================================================
       4. RESET PASSWORD REQUEST FORM HANDLING
       ======================================================== */
    const resetRequestForm = document.getElementById('reset-password-request-form');
    if (resetRequestForm) {
        resetRequestForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const email = document.getElementById('reset-email').value;
            const btn = document.getElementById('reset-request-submit');
            
            const originalText = btn.innerText;
            btn.innerText = 'Sending Link...';
            btn.style.opacity = '0.8';
            
            setTimeout(() => {
                btn.innerText = originalText;
                btn.style.opacity = '1';
                // For demo purposes, automatically navigate to set new password screen
                // In a real app, the user would click a link in their email
                alert(`Frontend Demo: Reset link sent to ${email}!\n\n(Redirecting to "Set New Password" screen for demo purposes).`);
                window.location.href = 'set-new-password.html';
            }, 800);
        });
    }

    /* ========================================================
       5. SET NEW PASSWORD FORM HANDLING
       ======================================================== */
    const setNewPasswordForm = document.getElementById('set-new-password-form');
    if (setNewPasswordForm) {
        setNewPasswordForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const btn = document.getElementById('set-password-submit');
            
            if (newPassword !== confirmPassword) {
                alert('Passwords do not match. Please try again.');
                return;
            }

            if (newPassword.length < 6) {
                alert('Password must be at least 6 characters long.');
                return;
            }
            
            const originalText = btn.innerText;
            btn.innerText = 'Updating...';
            btn.style.opacity = '0.8';
            
            setTimeout(() => {
                btn.innerText = originalText;
                btn.style.opacity = '1';
                alert('Frontend Demo: Password successfully reset!\n\n(Redirecting to Login).');
                window.location.href = 'index.html';
            }, 800);
        });
    }
});
