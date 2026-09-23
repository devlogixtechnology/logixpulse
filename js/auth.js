/**
 * LogixPulse Client Portal — Login Authentication Handler
 */

document.addEventListener('DOMContentLoaded', function () {
    var loginForm = document.getElementById('traditional-login-form');

    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            var submitBtn = document.getElementById('login-submit');
            var originalText = submitBtn.textContent;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Signing in...';

            var existingError = document.querySelector('.login-error');
            if (existingError) {
                existingError.remove();
            }

            try {
                var formData = new FormData(loginForm);

                var response = await fetch('api/login.php', {
                    method: 'POST',
                    body: formData
                });

                var data = await response.json();

                if (response.ok && data.success) {
                    window.location.href = data.redirect;
                } else {
                    var error = data.error || 'Login failed. Please try again.';
                    showLoginError(error);
                }

            } catch (error) {
                showLoginError('Unable to connect to server. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
});

function showLoginError(message) {
    var existingError = document.querySelector('.login-error');
    if (existingError) {
        existingError.remove();
    }

    var errorDiv = document.createElement('div');
    errorDiv.className = 'login-error';
    errorDiv.style.cssText = 'color: #991b1b; background: #fee2e2; padding: 0.75rem 1rem; border-radius: 6px; margin-top: 1rem; font-size: 0.875rem; border: 1px solid #fca5a5;';
    errorDiv.textContent = message;

    var authCard = document.querySelector('.auth-card');
    if (authCard) {
        authCard.parentNode.insertBefore(errorDiv, authCard.nextSibling);
    }

    setTimeout(function () {
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 6000);
}