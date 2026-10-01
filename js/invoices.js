/**
 * LogixPulse CRM - Client Invoices Controller
 * Subsystem: FR-INV-01, FR-INV-02, FR-INV-03
 * Developer: Sayeda Arooj (Squad PHP-FE-B)
 */

(function () {
  'use strict';

  const InvoicesController = {
    currentClientId: 1,
    currentFilter: 'all',
    searchQuery: '',
    invoicesData: [],

    init() {
      this.bindEvents();
      this.loadInvoices(this.currentClientId);
    },

    bindEvents() {
      // Invoices Filter Tabs
      const filterBtns = document.querySelectorAll('.lp-invoice-filter-btn');
      filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
          filterBtns.forEach(b => b.classList.remove('active', 'btn-primary'));
          filterBtns.forEach(b => b.classList.add('btn-outline-secondary'));
          
          btn.classList.add('active', 'btn-primary');
          btn.classList.remove('btn-outline-secondary');

          this.currentFilter = btn.getAttribute('data-status') || 'all';
          this.filterAndRender();
        });
      });

      // Invoice Search Input
      const searchInput = document.getElementById('invoiceSearchInput');
      if (searchInput) {
        searchInput.addEventListener('input', (e) => {
          this.searchQuery = e.target.value.toLowerCase().trim();
          this.filterAndRender();
        });
      }
    },

    async loadInvoices(clientId) {
      this.currentClientId = clientId;
      const tableBody = document.getElementById('invoicesTableBody');
      if (!tableBody) return;

      tableBody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">
            <span class="spinner-border spinner-border-sm me-2 text-primary" role="status"></span>
            Loading client billing documents...
          </td>
        </tr>
      `;

      try {
        const response = await fetch(`/api/invoices.php?client_id=${clientId}`);
        const result = await response.json();

        if (result.status === 'success' && Array.isArray(result.data)) {
          this.invoicesData = result.data;
          this.updateMetrics(result.summary);
          this.filterAndRender();
        }
      } catch (err) {
        console.warn('Invoices fetch failed, using fallback:', err);
      }
    },

    updateMetrics(summary) {
      if (!summary) return;
      const billedEl = document.getElementById('metricTotalBilled');
      const paidEl = document.getElementById('metricTotalPaid');
      const outstandingEl = document.getElementById('metricTotalOutstanding');

      if (billedEl) billedEl.textContent = summary.total_billed;
      if (paidEl) paidEl.textContent = summary.total_paid;
      if (outstandingEl) outstandingEl.textContent = summary.total_outstanding;
    },

    filterAndRender() {
      const tableBody = document.getElementById('invoicesTableBody');
      const countEl = document.getElementById('invoicesCountBadge');
      if (!tableBody) return;

      let filtered = this.invoicesData;

      if (this.currentFilter !== 'all') {
        filtered = filtered.filter(inv => inv.status.toLowerCase() === this.currentFilter.toLowerCase());
      }

      if (this.searchQuery) {
        filtered = filtered.filter(inv => 
          inv.id.toLowerCase().includes(this.searchQuery) ||
          inv.title.toLowerCase().includes(this.searchQuery) ||
          inv.formatted_amount.toLowerCase().includes(this.searchQuery)
        );
      }

      if (countEl) countEl.textContent = `${filtered.length} Invoices`;

      if (filtered.length === 0) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center py-5">
              <div class="text-muted">
                <i class="bi bi-file-earmark-x fs-2 d-block mb-2 text-secondary opacity-50"></i>
                <div class="fw-semibold">No invoices found matching criteria.</div>
                <small class="text-muted">Adjust status filter or search parameters.</small>
              </div>
            </td>
          </tr>
        `;
        return;
      }

      tableBody.innerHTML = filtered.map(inv => `
        <tr class="align-middle">
          <td class="fw-bold text-dark font-monospace" style="font-size: 0.8125rem;">
            ${inv.id}
          </td>
          <td>
            <div class="fw-semibold text-dark text-truncate" style="max-width: 260px;" title="${inv.title}">
              ${inv.title}
            </div>
            <small class="text-muted" style="font-size: 0.7rem;">Method: ${inv.payment_method}</small>
          </td>
          <td class="text-muted small">
            ${inv.issue_date}
          </td>
          <td class="text-muted small">
            ${inv.due_date}
          </td>
          <td class="fw-bold text-dark font-monospace" style="font-size: 0.875rem;">
            ${inv.formatted_amount}
          </td>
          <td>
            <span class="badge ${inv.status_badge_class} px-2 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
              ${inv.status_label}
            </span>
          </td>
          <td class="text-end">
            <a 
              href="${inv.download_url}" 
              download="${inv.id}.html"
              class="btn btn-sm btn-outline-primary px-2 py-1 d-inline-flex align-items-center gap-1"
              title="Download formal invoice document"
              style="font-size: 0.75rem;"
            >
              <i class="bi bi-download"></i>
              <span>Download</span>
            </a>
          </td>
        </tr>
      `).join('');
    }
  };

  window.DevLogixInvoices = InvoicesController;
  document.addEventListener('DOMContentLoaded', () => {
    InvoicesController.init();
  });
})();
