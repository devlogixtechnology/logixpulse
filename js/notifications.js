/**
 * @license
 * DevLogix Summer Internship Program 2026 - Sprint 2
 * Squad: PHP-FE-B (Client Communication Interface)
 * Task: FEB-01 — Build Client Notification Center & System Event Alerts
 * Assigned to: Sayeda Arooj
 * 
 * File: js/notifications.js
 * Description: Interactive notification bell dropdown, unread badge counter,
 * mark-all-as-read integration with api/notifications_read.php, category icons,
 * empty state rendering, and high-priority toast alerts.
 */

// Category icons map (using SVG icons or Bootstrap Icons classes)
const CATEGORY_ICONS = {
  milestone: {
    iconClass: 'bi bi-flag-fill',
    badgeClass: 'dl-cat-milestone',
    label: 'Milestone'
  },
  agreement: {
    iconClass: 'bi bi-file-earmark-text-fill',
    badgeClass: 'dl-cat-agreement',
    label: 'Contract'
  },
  account: {
    iconClass: 'bi bi-shield-check',
    badgeClass: 'dl-cat-account',
    label: 'Account'
  },
  system: {
    iconClass: 'bi bi-gear-fill',
    badgeClass: 'dl-cat-system',
    label: 'System'
  },
  'high-priority': {
    iconClass: 'bi bi-exclamation-triangle-fill',
    badgeClass: 'dl-cat-high-priority',
    label: 'Urgent'
  }
};

class NotificationCenter {
  constructor() {
    // DOM Elements
    this.bellBtn = document.getElementById('notificationBellBtn');
    this.badgeEl = document.getElementById('notificationBadge');
    this.dropdownEl = document.getElementById('notificationDropdown');
    this.listEl = document.getElementById('notificationList');
    this.markAllBtn = document.getElementById('markAllReadBtn');
    this.countPill = document.getElementById('dropdownUnreadCount');
    this.toastContainer = document.getElementById('toastContainer');
    this.filterTabs = document.querySelectorAll('.lp-filter-tab, .dl-filter-btn');

    // State
    this.notifications = [];
    this.unreadCount = 0;
    this.currentFilter = 'all';
    this.isOpen = false;
    this.isLoading = false;

    this.init();
  }

  /**
   * Initialize notification center event listeners and load data
   */
  init() {
    if (!this.bellBtn || !this.dropdownEl) {
      console.warn('[FEB-01] Notification DOM elements missing.');
      return;
    }

    // Toggle dropdown on bell click
    this.bellBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      this.toggleDropdown();
    });

    // Close dropdown on click outside
    document.addEventListener('click', (e) => {
      if (this.isOpen && !this.dropdownEl.contains(e.target) && !this.bellBtn.contains(e.target)) {
        this.closeDropdown();
      }
    });

    // Close on Escape key for accessibility
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen) {
        this.closeDropdown();
        this.bellBtn.focus();
      }
    });

    // "Mark all as read" click handler (Triggers api/notifications_read.php)
    if (this.markAllBtn) {
      this.markAllBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        this.handleMarkAllAsRead();
      });
    }

    // Category filter tabs
    if (this.filterTabs) {
      this.filterTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
          e.stopPropagation();
          const filter = tab.getAttribute('data-filter') || 'all';
          this.setFilter(filter);
        });
      });
    }

    // Initial fetch
    this.loadNotifications();
  }

  /**
   * Toggle dropdown open/closed
   */
  toggleDropdown() {
    if (this.isOpen) {
      this.closeDropdown();
    } else {
      this.openDropdown();
    }
  }

  /**
   * Open dropdown with accessibility updates
   */
  openDropdown() {
    this.isOpen = true;
    this.dropdownEl.classList.add('show');
    this.bellBtn.setAttribute('aria-expanded', 'true');
    this.renderNotifications();
  }

  /**
   * Close dropdown
   */
  closeDropdown() {
    this.isOpen = false;
    this.dropdownEl.classList.remove('show');
    this.bellBtn.setAttribute('aria-expanded', 'false');
  }

  /**
   * Fetch notifications from API
   */
  async loadNotifications() {
    this.isLoading = true;
    this.renderLoading();

    try {
      const response = await window.DevLogixAPI.getNotifications();
      this.isLoading = false;

      if (response.success) {
        this.notifications = response.data;
        this.calculateUnreadCount();
        this.updateBadge();
        this.renderNotifications();
      } else {
        this.renderError('Unable to load notifications. Please try again.');
      }
    } catch (err) {
      this.isLoading = false;
      this.renderError(err.message || 'Network error.');
    }
  }

  /**
   * Calculate unread notifications count
   */
  calculateUnreadCount() {
    this.unreadCount = this.notifications.filter(item => !item.is_read).length;
    return this.unreadCount;
  }

  /**
   * Update the badge UI with smooth pop animation
   */
  updateBadge() {
    if (!this.badgeEl) return;

    if (this.unreadCount > 0) {
      this.badgeEl.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
      this.badgeEl.classList.remove('is-zero');
      
      // Accessibility: update aria-label
      this.bellBtn.setAttribute('aria-label', `Notifications, ${this.unreadCount} unread items`);
      
      // Trigger pop animation
      this.badgeEl.classList.remove('is-animating');
      void this.badgeEl.offsetWidth; // Force CSS reflow
      this.badgeEl.classList.add('is-animating');
    } else {
      this.badgeEl.textContent = '0';
      this.badgeEl.classList.add('is-zero');
      this.bellBtn.setAttribute('aria-label', 'Notifications, no unread items');
    }

    // Update count pill in dropdown header
    if (this.countPill) {
      this.countPill.textContent = `${this.unreadCount} unread`;
    }

    // Update "Mark all as read" button state
    if (this.markAllBtn) {
      this.markAllBtn.disabled = this.unreadCount === 0;
      this.markAllBtn.style.opacity = this.unreadCount === 0 ? '0.5' : '1';
    }
  }

  /**
   * Filter handling
   */
  setFilter(filter) {
    this.currentFilter = filter;
    this.filterTabs.forEach(tab => {
      tab.classList.toggle('active', tab.getAttribute('data-filter') === filter);
    });
    this.renderNotifications();
  }

  /**
   * Render notifications list based on current active filter
   */
  renderNotifications() {
    if (!this.listEl) return;

    if (this.notifications.length === 0) {
      this.renderEmptyState('No notifications yet', 'When CRM events or project milestones arrive, they will appear here.');
      return;
    }

    let filtered = [...this.notifications];
    if (this.currentFilter === 'unread') {
      filtered = filtered.filter(item => !item.is_read);
    } else if (this.currentFilter === 'milestone') {
      filtered = filtered.filter(item => item.category === 'milestone');
    } else if (this.currentFilter === 'system') {
      filtered = filtered.filter(item => item.category === 'system' || item.category === 'account');
    }

    if (filtered.length === 0) {
      this.renderEmptyState('No matches found', `No ${this.currentFilter} notifications to show.`);
      return;
    }

    this.listEl.innerHTML = '';

    filtered.forEach(item => {
      const itemEl = document.createElement('li');
      itemEl.className = `dl-notification-item ${!item.is_read ? 'is-unread' : ''}`;
      itemEl.setAttribute('role', 'button');
      itemEl.setAttribute('tabindex', '0');
      itemEl.setAttribute('aria-label', `${item.title}: ${item.message}`);

      const catInfo = CATEGORY_ICONS[item.category] || CATEGORY_ICONS.system;

      itemEl.innerHTML = `
        <div class="dl-cat-icon ${catInfo.badgeClass}" title="${catInfo.label}">
          <i class="${catInfo.iconClass}"></i>
        </div>
        <div class="dl-item-body">
          <div class="dl-item-header">
            <h4 class="dl-item-title">${this.escapeHtml(item.title)}</h4>
            <span class="dl-item-time">${this.escapeHtml(item.timestamp || 'Recent')}</span>
          </div>
          <p class="dl-item-desc">${this.escapeHtml(item.message)}</p>
          <div class="d-flex align-items-center justify-content-between mt-1">
            <span class="dl-item-badge">${this.escapeHtml(item.category_label || catInfo.label)}</span>
            ${!item.is_read ? '<span class="text-xs text-primary" style="font-size:0.6875rem; font-weight:600;">Mark as read</span>' : ''}
          </div>
        </div>
        <div class="dl-item-indicator">
          ${!item.is_read ? '<span class="dl-unread-dot" title="Unread"></span>' : ''}
        </div>
      `;

      // Click on item to mark as read
      itemEl.addEventListener('click', () => {
        this.handleItemClick(item);
      });

      // Keyboard accessibility (Enter / Space)
      itemEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.handleItemClick(item);
        }
      });

      this.listEl.appendChild(itemEl);
    });
  }

  /**
   * Handle single item click
   */
  async handleItemClick(item) {
    if (!item.is_read) {
      item.is_read = true;
      this.calculateUnreadCount();
      this.updateBadge();
      this.renderNotifications();

      // Trigger background update
      await window.DevLogixAPI.markNotificationRead(item.id);
    }

    if (item.action_url && item.action_url !== '#') {
      const target = document.querySelector(item.action_url);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
        target.classList.add('border-primary');
        setTimeout(() => target.classList.remove('border-primary'), 2000);
      }
    }
  }

  /**
   * FEB-01 Requirement: "Mark all as read"
   * Triggers api/notifications_read.php.
   * Only resets badge count to 0 upon successful API response!
   */
  async handleMarkAllAsRead() {
    if (this.unreadCount === 0) return;

    const originalBtnText = this.markAllBtn.innerHTML;
    this.markAllBtn.disabled = true;
    this.markAllBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 0.75rem; height: 0.75rem;"></span> Marking...';

    try {
      const result = await window.DevLogixAPI.markAllNotificationsRead();

      if (result.success) {
        // Operation succeeded! Update local state
        this.notifications.forEach(item => item.is_read = true);
        this.calculateUnreadCount(); // will be 0
        this.updateBadge(); // resets badge count to zero
        this.renderNotifications();

        this.showToast({
          title: "All Marked as Read",
          message: result.message || "All notifications marked as read successfully.",
          category: "system"
        });
      } else {
        // Do not reset count if API fails!
        alert(`Failed to mark all as read: ${result.error || 'Server error'}`);
      }
    } catch (err) {
      alert(`API Error: ${err.message}`);
    } finally {
      this.markAllBtn.innerHTML = originalBtnText;
      this.markAllBtn.disabled = this.unreadCount === 0;
    }
  }

  /**
   * Trigger incoming notification (tests FEB-01 count increment)
   */
  receiveIncomingNotification(notificationData) {
    const createdItem = window.DevLogixAPI.pushMockNotification(notificationData);
    this.notifications.unshift(createdItem);
    this.calculateUnreadCount();
    this.updateBadge(); // Badge count increments!

    if (this.isOpen) {
      this.renderNotifications();
    }

    // If high-priority, trigger popup toast
    if (notificationData.priority === 'high' || notificationData.category === 'high-priority') {
      this.showToast({
        title: notificationData.title || "Critical Event",
        message: notificationData.message,
        category: "high-priority"
      });
    }
  }

  /**
   * FEB-01 Requirement: High-priority toast notification popup
   */
  showToast({ title, message, category = 'high-priority' }) {
    if (!this.toastContainer) return;

    const toastId = `toast-${Date.now()}`;
    const toastEl = document.createElement('div');
    const isUrgent = category === 'high-priority';

    toastEl.className = `toast ${isUrgent ? 'dl-toast-high-priority' : ''} mb-2`;
    toastEl.id = toastId;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
      <div class="dl-toast-header">
        <h5 class="dl-toast-title">
          <i class="${isUrgent ? 'bi bi-exclamation-triangle-fill' : 'bi bi-bell-fill'}"></i>
          ${this.escapeHtml(title)}
        </h5>
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="dl-toast-body">
        ${this.escapeHtml(message)}
        <div class="mt-2 text-end">
          <small class="text-muted">Just now</small>
        </div>
      </div>
    `;

    this.toastContainer.appendChild(toastEl);

    // Initialize with Bootstrap 5 Toast API
    if (window.bootstrap && window.bootstrap.Toast) {
      const bsToast = new window.bootstrap.Toast(toastEl, {
        delay: isUrgent ? 8000 : 5000,
        autohide: true
      });
      bsToast.show();

      toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
      });
    } else {
      // Fallback if bootstrap JS is delayed
      toastEl.style.display = 'block';
      setTimeout(() => toastEl.remove(), 6000);
    }
  }

  /**
   * FEB-01 Requirement: Clean Empty State visual placeholder
   */
  renderEmptyState(title = "No new notifications", desc = "You're all caught up with your CRM updates.") {
    if (!this.listEl) return;
    this.listEl.innerHTML = `
      <div class="dl-empty-state">
        <div class="dl-empty-icon">
          <i class="bi bi-bell-slash"></i>
        </div>
        <h4 class="dl-empty-title">${this.escapeHtml(title)}</h4>
        <p class="dl-empty-desc">${this.escapeHtml(desc)}</p>
      </div>
    `;
  }

  /**
   * Loading state placeholder
   */
  renderLoading() {
    if (!this.listEl) return;
    this.listEl.innerHTML = `
      <div class="dl-loading-state">
        <div class="spinner-border text-primary" role="status" style="width: 1.75rem; height: 1.75rem;">
          <span class="visually-hidden">Loading notifications...</span>
        </div>
        <span class="text-xs text-muted" style="font-size: 0.75rem;">Syncing with DevLogix CRM...</span>
      </div>
    `;
  }

  /**
   * Error state placeholder
   */
  renderError(msg) {
    if (!this.listEl) return;
    this.listEl.innerHTML = `
      <div class="dl-error-state">
        <i class="bi bi-exclamation-octagon text-danger fs-4 mb-1 d-block"></i>
        <p><strong>Failed to load:</strong> ${this.escapeHtml(msg)}</p>
        <button class="btn btn-sm btn-outline-danger" onclick="window.DevLogixNotificationCenter.loadNotifications()">
          <i class="bi bi-arrow-clockwise me-1"></i>Retry
        </button>
      </div>
    `;
  }

  /**
   * Clear notifications (for testing empty state)
   */
  clearAll() {
    window.DevLogixAPI.clearMockNotifications();
    this.notifications = [];
    this.calculateUnreadCount();
    this.updateBadge();
    this.renderNotifications();
  }

  /**
   * Reset to default dataset
   */
  resetAll() {
    window.DevLogixAPI.resetMockNotifications();
    this.loadNotifications();
  }

  escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
}

// Instantiate and expose globally when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  window.DevLogixNotificationCenter = new NotificationCenter();
});
