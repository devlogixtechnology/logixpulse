/**
 * LogixPulse CRM - Client Timeline Controller
 * Squad: PHP-FE-B (Client Communication Interface)
 * Subtask: Client Onboarding Milestone Progression
 * Developer: Sayeda Arooj
 */

(function () {
  'use strict';

  const TimelineController = {
    currentClientId: 1,

    init() {
      this.bindEvents();
      this.loadTimeline(this.currentClientId);
    },

    bindEvents() {
      // Client switcher dropdown
      const clientSelect = document.getElementById('clientAccountSwitcher');
      if (clientSelect) {
        clientSelect.addEventListener('change', (e) => {
          this.currentClientId = parseInt(e.target.value, 10);
          this.loadTimeline(this.currentClientId);
          if (window.DevLogixInvoices) {
            window.DevLogixInvoices.loadInvoices(this.currentClientId);
          }
        });
      }
    },

    async loadTimeline(clientId) {
      const container = document.getElementById('timelineMilestonesContainer');
      const progressBar = document.getElementById('timelineProgressBar');
      const progressText = document.getElementById('timelineProgressText');
      const projectNameEl = document.getElementById('timelineProjectName');
      const dealValueEl = document.getElementById('timelineDealValue');

      if (!container) return;

      try {
        const response = await fetch(`/api/timeline.php?client_id=${clientId}`);
        const result = await response.json();

        if (result.status === 'success' && result.data) {
          const data = result.data;

          if (projectNameEl) projectNameEl.textContent = data.project_name;
          if (dealValueEl) dealValueEl.textContent = data.deal_value;
          if (progressBar) progressBar.style.width = `${data.completion_percentage}%`;
          if (progressText) progressText.textContent = `Stage ${data.current_step_id} of 5 (${data.completion_percentage}%)`;

          this.renderMilestones(data.steps, container);
        }
      } catch (err) {
        console.warn('API timeline fetch failed, using fallback data:', err);
      }
    },

    renderMilestones(steps, container) {
      if (!steps || !steps.length) return;

      container.innerHTML = steps.map((step, idx) => {
        let iconHtml = '';
        let cardStyle = 'border: 1px solid #E2E8F0; background: #FFFFFF;';
        let badgeClass = 'text-muted';

        if (step.status === 'completed') {
          iconHtml = '<i class="bi bi-check-circle-fill text-success fs-5"></i>';
          badgeClass = 'text-success fw-semibold';
        } else if (step.status === 'current') {
          iconHtml = '<i class="bi bi-arrow-repeat text-primary fs-5"></i>';
          cardStyle = 'border: 2px solid #3B82F6; background-color: #EEF2FF;';
          badgeClass = 'text-primary fw-bold';
        } else {
          iconHtml = '<i class="bi bi-lock-fill text-secondary fs-5 opacity-75"></i>';
          cardStyle = 'border: 1px solid #E2E8F0; background: #F8FAFC; opacity: 0.85;';
          badgeClass = 'text-muted';
        }

        return `
          <div class="col-12 col-sm-6 col-md-4 col-lg">
            <div class="p-3 rounded-2 text-center h-100 shadow-sm transition-all" style="${cardStyle}">
              ${iconHtml}
              <div class="fw-bold small mt-2 text-dark text-truncate" title="${step.title}">
                ${idx + 1}. ${step.phase_name}
              </div>
              <small class="${badgeClass} d-block" style="font-size: 0.72rem;">
                ${step.badge}
              </small>
              <div class="text-muted mt-1" style="font-size: 0.68rem; line-height: 1.2;">
                Target: ${step.target_date}
              </div>
            </div>
          </div>
        `;
      }).join('');
    }
  };

  window.DevLogixTimeline = TimelineController;
  document.addEventListener('DOMContentLoaded', () => {
    TimelineController.init();
  });
})();
