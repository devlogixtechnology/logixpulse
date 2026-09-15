(() => {
  const form           = document.querySelector('#login-form');
  const passwordInput  = document.querySelector('#password');
  const passwordToggle = document.querySelector('.password-toggle');
  const message        = document.querySelector('#login-message');
  const submitButton   = document.querySelector('#login-submit');

  if (!form) return;

  // ── Password visibility toggle (unchanged) ────────────────────────────────
  passwordToggle?.addEventListener('click', () => {
    const isVisible = passwordInput.type === 'text';
    passwordInput.type = isVisible ? 'password' : 'text';
    passwordToggle.setAttribute('aria-pressed', String(!isVisible));
    passwordToggle.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
  });

  // ── Login form submit ─────────────────────────────────────────────────────
  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearMessage();

    const endpoint = form.dataset.loginEndpoint?.trim();
    if (!endpoint) {
      showError('Login endpoint is not configured.');
      return;
    }

    // Build a URL-encoded body so PHP can read it via $_POST['email'] / $_POST['password'].
    // The password is never logged or stored.
    const formData = new FormData(form);
    const body = new URLSearchParams();
    body.append('email',    String(formData.get('email')    || '').trim());
    body.append('password', String(formData.get('password') || ''));

    setLoading(true);

    try {
      const response = await fetch(endpoint, {
        method:      'POST',
        headers:     { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'include',   // send/receive the PHP session cookie
        body:        body.toString()
      });

      // login.php returns plain text: "login successful" or "login failed"
      const text = (await response.text()).trim();

      if (text === 'login successful') {
        // Redirect to the internal CRM destination defined on the form element.
        const redirectPath = form.dataset.redirectPath || '/crm/';
        window.location.assign(redirectPath);
      } else {
        // Covers "login failed" and any unexpected plain-text response.
        showError('The email or password you entered is incorrect. Please try again.');
      }
    } catch {
      // Network-level failure (no connection, CORS block, etc.)
      showError('Unable to reach the login service. Check your connection and try again.');
    } finally {
      setLoading(false);
    }
  });

  // ── Helpers ───────────────────────────────────────────────────────────────
  function setLoading(isLoading) {
    submitButton.disabled = isLoading;
    const arrow = submitButton.querySelector('span');
    if (arrow) arrow.textContent = isLoading ? '...' : '→';
  }

  function showError(text) {
    message.textContent = text;
    message.classList.add('login-message--error');
  }

  function clearMessage() {
    message.textContent = '';
    message.classList.remove('login-message--error');
  }
})();
