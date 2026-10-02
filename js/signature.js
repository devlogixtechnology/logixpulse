/**
 * LogixPulse CRM - Client Signing Portal & Confirmation Engine
 * Squad: PHP-FE-B (Client Signing Portal)
 * 
 * Subtask [FEB-03] (Halima): Interactive Contract Review Screen & Terms Acceptance
 * Subtask [FEB-04] (Sayyda Arooj): Signed Agreement Confirmation & Receipt Download
 */

(function () {
  'use strict';

  const SigningPortal = {
    canvas: null,
    ctx: null,
    isDrawing: false,
    hasDrawn: false,
    hasScrolledTerms: false,
    token: 'lp_token_demo',
    agreementId: 'MSA-2026-904',
    referenceCode: 'MSA-2026-904-EXEC',
    lastExecutedData: null,

    init() {
      // 1. Extract Token from URL parameters (FEB-03 requirement)
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('token')) {
        this.token = urlParams.get('token');
      }
      if (urlParams.get('agreement_id')) {
        this.agreementId = urlParams.get('agreement_id');
        this.referenceCode = this.agreementId + '-EXEC';
      }

      const tokenEl = document.getElementById('signingTokenBadge');
      if (tokenEl) tokenEl.textContent = `Token: ${this.token}`;

      this.canvas = document.getElementById('signatureCanvas');
      if (this.canvas) {
        this.ctx = this.canvas.getContext('2d');
        this.setupCanvas();
        window.addEventListener('resize', () => this.setupCanvas());
      }

      this.bindEvents();
      this.checkAgreementStatus();
    },

    setupCanvas() {
      if (!this.canvas) return;
      const rect = this.canvas.getBoundingClientRect();
      let tempImg = null;
      if (this.hasDrawn) {
        try {
          tempImg = this.canvas.toDataURL();
        } catch (e) {}
      }

      const displayWidth = rect.width || this.canvas.offsetWidth || 350;
      const displayHeight = 140;

      this.canvas.width = displayWidth;
      this.canvas.height = displayHeight;

      this.ctx.lineWidth = 3;
      this.ctx.lineCap = 'round';
      this.ctx.lineJoin = 'round';
      this.ctx.strokeStyle = '#0F172A';
      this.ctx.fillStyle = '#0F172A';

      if (tempImg) {
        const img = new Image();
        img.onload = () => this.ctx.drawImage(img, 0, 0);
        img.src = tempImg;
      }
    },

    getCoordinates(e) {
      const rect = this.canvas.getBoundingClientRect();
      const scaleX = this.canvas.width / rect.width;
      const scaleY = this.canvas.height / rect.height;

      let clientX = e.clientX;
      let clientY = e.clientY;

      if (e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
      } else if (e.changedTouches && e.changedTouches.length > 0) {
        clientX = e.changedTouches[0].clientX;
        clientY = e.changedTouches[0].clientY;
      }

      return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top) * scaleY
      };
    },

    bindEvents() {
      // =======================================================================
      // FEB-03 (HALIMA): Scroll Tracking on Contract Terms
      // =======================================================================
      const termsBox = document.getElementById('contractTermsScrollbox');
      const scrollIndicator = document.getElementById('termsScrollIndicator');

      if (termsBox) {
        termsBox.addEventListener('scroll', () => {
          const scrollPercentage = Math.min(
            100,
            Math.round(((termsBox.scrollTop + termsBox.clientHeight) / termsBox.scrollHeight) * 100)
          );

          if (scrollIndicator) {
            scrollIndicator.textContent = `Terms Read: ${scrollPercentage}%`;
            if (scrollPercentage >= 90) {
              scrollIndicator.className = 'badge bg-success-subtle text-success border border-success-subtle';
              scrollIndicator.innerHTML = '<i class="bi bi-check-circle me-1"></i>All Terms Reviewed';
              this.hasScrolledTerms = true;
            } else {
              scrollIndicator.className = 'badge bg-light text-secondary border';
            }
          }

          this.validateForm();
        });
      }

      // =======================================================================
      // FEB-03 (HALIMA): Legal Acknowledgment Checkboxes
      // =======================================================================
      const chkScope = document.getElementById('chkScope');
      const chkPayment = document.getElementById('chkPayment');
      const chkConfidentiality = document.getElementById('chkConfidentiality');

      [chkScope, chkPayment, chkConfidentiality].forEach(chk => {
        if (chk) {
          chk.addEventListener('change', () => this.validateForm());
        }
      });

      // =======================================================================
      // FEB-03 (HALIMA): Canvas Drawing using Pointer & Touch Events
      // =======================================================================
      if (this.canvas) {
        const handleStart = (e) => {
          e.preventDefault();
          this.isDrawing = true;
          this.ctx.lineWidth = 3;
          this.ctx.lineCap = 'round';
          this.ctx.lineJoin = 'round';
          this.ctx.strokeStyle = '#0F172A';
          this.ctx.fillStyle = '#0F172A';

          const pos = this.getCoordinates(e);
          // Draw a small dot immediately on click/touch
          this.ctx.beginPath();
          this.ctx.arc(pos.x, pos.y, 1.5, 0, Math.PI * 2);
          this.ctx.fill();

          this.ctx.beginPath();
          this.ctx.moveTo(pos.x, pos.y);

          if (!this.hasDrawn) {
            this.hasDrawn = true;
            const dateInput = document.getElementById('signatureDateDisplay');
            if (dateInput) {
              dateInput.textContent = new Date().toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
              });
            }
            this.validateForm();
          }
        };

        const handleMove = (e) => {
          if (!this.isDrawing) return;
          e.preventDefault();
          const pos = this.getCoordinates(e);
          this.ctx.lineTo(pos.x, pos.y);
          this.ctx.stroke();
          this.ctx.beginPath();
          this.ctx.moveTo(pos.x, pos.y);
        };

        const handleEnd = (e) => {
          if (this.isDrawing) {
            this.ctx.closePath();
            this.isDrawing = false;
            this.validateForm();
          }
        };

        // Pointer Events (supports mouse, touch, and stylus uniformly)
        if (window.PointerEvent) {
          this.canvas.addEventListener('pointerdown', (e) => {
            this.canvas.setPointerCapture?.(e.pointerId);
            handleStart(e);
          });
          this.canvas.addEventListener('pointermove', handleMove);
          this.canvas.addEventListener('pointerup', (e) => {
            this.canvas.releasePointerCapture?.(e.pointerId);
            handleEnd(e);
          });
          this.canvas.addEventListener('pointercancel', handleEnd);
        } else {
          // Fallback Mouse Events
          this.canvas.addEventListener('mousedown', handleStart);
          this.canvas.addEventListener('mousemove', handleMove);
          window.addEventListener('mouseup', handleEnd);

          // Fallback Touch Events
          this.canvas.addEventListener('touchstart', handleStart, { passive: false });
          this.canvas.addEventListener('touchmove', handleMove, { passive: false });
          this.canvas.addEventListener('touchend', handleEnd, { passive: false });
          this.canvas.addEventListener('touchcancel', handleEnd, { passive: false });
        }
      }

      // Quick Demo Signature (Helper for users on laptops/desktops)
      const autoSignBtn = document.getElementById('autoSignBtn');
      if (autoSignBtn) {
        autoSignBtn.addEventListener('click', () => this.drawPresetSignature());
      }

      // Clear Signature Pad
      const clearBtn = document.getElementById('clearSignatureBtn');
      if (clearBtn) {
        clearBtn.addEventListener('click', () => this.clearCanvas());
      }

      // Submit Signature Button
      const submitBtn = document.getElementById('submitSignatureBtn');
      if (submitBtn) {
        submitBtn.addEventListener('click', () => this.submitSignature());
      }

      // FEB-04 (Sayyda Arooj): Proceed to Client Dashboard Button
      const proceedBtn = document.getElementById('proceedToDashboardBtn');
      if (proceedBtn) {
        proceedBtn.addEventListener('click', () => {
          const timelineEl = document.getElementById('timeline');
          if (timelineEl) {
            timelineEl.scrollIntoView({ behavior: 'smooth' });
          }
        });
      }
    },

    drawPresetSignature() {
      if (!this.ctx || !this.canvas) return;
      this.setupCanvas();
      
      const signerName = document.getElementById('signerName')?.value || 'Sarah Jenkins';
      
      this.ctx.save();
      this.ctx.font = 'italic 34px "Brush Script MT", "Segoe Script", cursive, sans-serif';
      this.ctx.fillStyle = '#0F172A';
      this.ctx.fillText(signerName, 30, 80);

      // Underline flourish
      this.ctx.beginPath();
      this.ctx.moveTo(25, 95);
      this.ctx.bezierCurveTo(90, 85, 180, 110, 240, 95);
      this.ctx.lineWidth = 2.5;
      this.ctx.strokeStyle = '#0F172A';
      this.ctx.stroke();
      this.ctx.restore();

      this.hasDrawn = true;
      const dateInput = document.getElementById('signatureDateDisplay');
      if (dateInput) {
        dateInput.textContent = new Date().toLocaleDateString('en-US', {
          month: 'short',
          day: 'numeric',
          year: 'numeric'
        });
      }
      this.validateForm();
    },

    clearCanvas() {
      if (!this.ctx || !this.canvas) return;
      this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
      this.hasDrawn = false;
      this.validateForm();
    },

    validateForm() {
      const chkScope = document.getElementById('chkScope');
      const chkPayment = document.getElementById('chkPayment');
      const chkConfidentiality = document.getElementById('chkConfidentiality');
      const submitBtn = document.getElementById('submitSignatureBtn');
      const warningText = document.getElementById('signingRequirementNotice');

      const allChecked = chkScope?.checked && chkPayment?.checked && chkConfidentiality?.checked;
      const isReady = allChecked && this.hasDrawn;

      if (submitBtn) {
        submitBtn.disabled = !isReady;
      }

      if (warningText) {
        if (!this.hasDrawn) {
          warningText.textContent = 'Please draw your signature above (or click "Adopt Sample Signature").';
          warningText.className = 'text-warning-emphasis small mt-2';
        } else if (!allChecked) {
          warningText.textContent = 'Signature recorded! Now please check all 3 mandatory checkboxes.';
          warningText.className = 'text-primary small mt-2 fw-semibold';
        } else {
          warningText.textContent = 'Ready! Click Submit Legal Signature below.';
          warningText.className = 'text-success small mt-2 fw-bold';
        }
      }
    },

    // =========================================================================
    // FEB-04 (SAYYDA AROOJ): Prevent Re-signing & Check Status
    // =========================================================================
    async checkAgreementStatus() {
      try {
        const response = await fetch(`/api/agreement_status.php?token=${this.token}&agreement_id=${this.agreementId}`);
        const result = await response.json();

        if (result.status === 'success' && result.is_already_executed) {
          // Client accessed an already executed agreement URL -> Prevent re-signing!
          this.renderExecutedConfirmation(result.agreement, true);
        }
      } catch (err) {
        console.warn('Agreement status check fallback:', err);
      }
    },

    // =========================================================================
    // FEB-03 (HALIMA) -> FEB-04 (SAYYDA AROOJ): Submit & Transition Flow
    // =========================================================================
    async submitSignature() {
      const chkScope = document.getElementById('chkScope');
      const chkPayment = document.getElementById('chkPayment');
      const chkConfidentiality = document.getElementById('chkConfidentiality');

      if (!this.hasDrawn || !chkScope?.checked || !chkPayment?.checked || !chkConfidentiality?.checked) {
        return;
      }

      const signerName = document.getElementById('signerName')?.value || 'Sarah Jenkins';
      const signerEmail = document.getElementById('signerEmail')?.value || 's.jenkins@acmecloud.com';
      const signatureData = this.canvas.toDataURL('image/png');

      const submitBtn = document.getElementById('submitSignatureBtn');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
          <span class="spinner-border spinner-border-sm me-2" role="status"></span>
          Encrypting &amp; Executing Agreement...
        `;
      }

      try {
        const response = await fetch('/api/submit_signature.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            agreement_id: this.agreementId,
            token: this.token,
            signer_name: signerName,
            signer_email: signerEmail,
            signature_data: signatureData,
            scope_agreed: true,
            payment_agreed: true,
            confidentiality_agreed: true
          })
        });

        const result = await response.json();

        if (result.status === 'success' && result.agreement) {
          this.lastExecutedData = result.agreement;

          // Transition to FEB-04 Post-Signature Confirmation Screen
          this.renderExecutedConfirmation(result.agreement, false);

          // Real-time Event Toast from Notification Center
          if (window.DevLogixNotificationCenter) {
            window.DevLogixNotificationCenter.showToast(
              'Agreement Executed & Sealed',
              `Reference ${result.agreement.reference_code} recorded with SHA-256 seal.`,
              'success'
            );
          }
        }
      } catch (err) {
        console.error('Signature upload failed:', err);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="bi bi-pen-fill me-1"></i>Submit Legal Signature';
        }
      }
    },

    // =========================================================================
    // FEB-04 (SAYYDA AROOJ): Post-Signature Celebration & Receipt Download Screen
    // =========================================================================
    renderExecutedConfirmation(agreement, wasPreExecuted = false) {
      const reviewContainer = document.getElementById('agreementReviewSection');
      const confirmationContainer = document.getElementById('agreementConfirmationSection');

      if (reviewContainer) reviewContainer.classList.add('d-none');
      if (confirmationContainer) {
        confirmationContainer.classList.remove('d-none');

        // Populate fields
        const refCodeEl = document.getElementById('confReferenceCode');
        const timestampEl = document.getElementById('confTimestamp');
        const signerInfoEl = document.getElementById('confSignerInfo');
        const shaSealEl = document.getElementById('confShaSeal');
        const sigPreviewEl = document.getElementById('confSignaturePreview');
        const downloadReceiptBtn = document.getElementById('downloadExecutedReceiptBtn');
        const preventReSigningNotice = document.getElementById('preventReSigningNotice');

        if (refCodeEl) refCodeEl.textContent = agreement.reference_code || (this.agreementId + '-EXEC');
        if (timestampEl) timestampEl.textContent = agreement.execution_timestamp || new Date().toLocaleString();
        if (signerInfoEl) signerInfoEl.textContent = `${agreement.signer_name} (${agreement.signer_email})`;
        if (shaSealEl) shaSealEl.textContent = agreement.certificate_hash || 'SHA256: 7F8B2C4D9A0E1F3B...';

        if (sigPreviewEl && agreement.signature_image_data) {
          sigPreviewEl.innerHTML = `<img src="${agreement.signature_image_data}" alt="Verified Signature" style="max-height: 70px; max-width: 240px; display: inline-block;" />`;
        }

        if (downloadReceiptBtn) {
          downloadReceiptBtn.href = `/api/download_receipt.php?agreement_id=${encodeURIComponent(agreement.id || this.agreementId)}&ref=${encodeURIComponent(agreement.reference_code || this.referenceCode)}`;
        }

        if (preventReSigningNotice) {
          if (wasPreExecuted) {
            preventReSigningNotice.classList.remove('d-none');
          } else {
            preventReSigningNotice.classList.add('d-none');
          }
        }
      }
    }
  };

  window.DevLogixSigningPortal = SigningPortal;
  document.addEventListener('DOMContentLoaded', () => {
    SigningPortal.init();
  });
})();
