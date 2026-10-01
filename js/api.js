/**
 * @license
 * DevLogix Summer Internship Program 2026 - Sprint 2
 * Squad: PHP-FE-B (Client Communication Interface)
 * Task: FEB-01 (Sayeda Arooj) | Collaborative: FEB-02 (Kaneez Fatima)
 * 
 * File: js/api.js
 * Description: Dedicated API service module for client communication and notifications.
 * Connects with backend PHP endpoints (e.g. api/notifications_read.php) with an
 * automated mock data fallback when running in standalone frontend demonstration mode.
 */

// Centralized API Configuration
const API_CONFIG = {
  // Base path relative to portal root
  BASE_URL: './api',
  ENDPOINTS: {
    GET_NOTIFICATIONS: 'notifications.php',
    MARK_ALL_READ: 'notifications_read.php',
    MARK_SINGLE_READ: 'notifications_mark_single.php'
  },
  // Simulation flag: If true or if fetch fails with 404 (endpoint not created yet by backend squad),
  // use structured mock data conforming to Sprint 2 PostgreSQL schema expectations.
  USE_MOCK_FALLBACK: true,
  REQUEST_TIMEOUT: 6000
};

// Initial Mock Dataset for DevLogix CRM Pipeline & Client Communication
let mockNotificationsStore = [
  {
    id: 101,
    title: "Lead Stage Progression",
    message: "CRM Lead #DL-8429 (Acme Corp Cloud Expansion) advanced from 'Proposal Review' to 'Contract Negotiation'.",
    category: "milestone", // milestone | agreement | account | system | high-priority
    category_label: "CRM Milestone",
    timestamp: "10 mins ago",
    created_at: new Date(Date.now() - 10 * 60 * 1000).toISOString(),
    is_read: false,
    priority: "normal",
    action_url: "#lead-8429"
  },
  {
    id: 102,
    title: "Urgent: Opportunity Approval Required",
    message: "Senior Partner approved pricing terms for Sprint 2 Master Services Agreement. Awaiting client digital sign-off.",
    category: "high-priority",
    category_label: "Critical Action",
    timestamp: "25 mins ago",
    created_at: new Date(Date.now() - 25 * 60 * 1000).toISOString(),
    is_read: false,
    priority: "high",
    action_url: "#contract-preview"
  },
  {
    id: 103,
    title: "Contract Document Generated",
    message: "New statement of work PDF v2.4 ready for review in digital agreement vault.",
    category: "agreement",
    category_label: "Agreement",
    timestamp: "2 hours ago",
    created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(),
    is_read: false,
    priority: "normal",
    action_url: "#docs"
  },
  {
    id: 104,
    title: "Client Portal Security Check",
    message: "Two-factor authentication successfully verified from IP 192.168.1.42.",
    category: "account",
    category_label: "Account",
    timestamp: "Yesterday",
    created_at: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString(),
    is_read: true,
    priority: "low",
    action_url: "#security"
  },
  {
    id: 105,
    title: "System Maintenance Notice",
    message: "DevLogix platform scheduled optimization on Sunday 02:00 AM UTC.",
    category: "system",
    category_label: "System",
    timestamp: "2 days ago",
    created_at: new Date(Date.now() - 48 * 60 * 60 * 1000).toISOString(),
    is_read: true,
    priority: "low",
    action_url: "#system"
  }
];

/**
 * Fetch all notifications for the authenticated client user.
 * 
 * @returns {Promise<{success: boolean, data: Array, unread_count: number, source: string}>}
 */
async function getNotifications() {
  const targetUrl = `${API_CONFIG.BASE_URL}/${API_CONFIG.ENDPOINTS.GET_NOTIFICATIONS}`;

  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), API_CONFIG.REQUEST_TIMEOUT);

    const response = await fetch(targetUrl, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      signal: controller.signal
    });

    clearTimeout(timeoutId);

    if (response.ok) {
      const result = await response.json();
      return {
        success: true,
        data: result.notifications || result.data || [],
        unread_count: result.unread_count ?? (result.data ? result.data.filter(n => !n.is_read).length : 0),
        source: 'real-backend'
      };
    } else {
      throw new Error(`Server returned HTTP ${response.status}`);
    }
  } catch (error) {
    if (API_CONFIG.USE_MOCK_FALLBACK) {
      console.warn(`[PHP-FE-B] Real endpoint ${targetUrl} unavailable (${error.message}). Using mock store.`);
      const unreadCount = mockNotificationsStore.filter(n => !n.is_read).length;
      return {
        success: true,
        data: [...mockNotificationsStore],
        unread_count: unreadCount,
        source: 'mock-standalone'
      };
    }

    console.error('[PHP-FE-B] Failed to fetch notifications:', error);
    return {
      success: false,
      error: error.message,
      data: [],
      unread_count: 0
    };
  }
}

/**
 * FEB-01 Required Action:
 * Mark all notifications as read.
 * Directly triggers POST to api/notifications_read.php.
 * 
 * @returns {Promise<{success: boolean, message: string, updated_count: number, source: string}>}
 */
async function markAllNotificationsRead() {
  const targetUrl = `${API_CONFIG.BASE_URL}/${API_CONFIG.ENDPOINTS.MARK_ALL_READ}`;

  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), API_CONFIG.REQUEST_TIMEOUT);

    const response = await fetch(targetUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        action: 'mark_all_read',
        timestamp: new Date().toISOString()
      }),
      signal: controller.signal
    });

    clearTimeout(timeoutId);

    if (response.ok) {
      const result = await response.json();
      // Update local store as well
      mockNotificationsStore.forEach(n => n.is_read = true);
      return {
        success: true,
        message: result.message || 'All notifications marked as read',
        updated_count: result.updated_count || mockNotificationsStore.length,
        source: 'real-backend'
      };
    } else {
      throw new Error(`Server returned status ${response.status}`);
    }
  } catch (error) {
    if (API_CONFIG.USE_MOCK_FALLBACK) {
      console.info(`[PHP-FE-B] Mock fallback executing for ${targetUrl}: Setting all items to read.`);
      let updatedCount = 0;
      mockNotificationsStore = mockNotificationsStore.map(item => {
        if (!item.is_read) updatedCount++;
        return { ...item, is_read: true };
      });

      return {
        success: true,
        message: 'All notifications marked as read (standalone mock)',
        updated_count: updatedCount,
        source: 'mock-standalone'
      };
    }

    console.error('[PHP-FE-B] Error marking all notifications as read:', error);
    return {
      success: false,
      error: error.message,
      updated_count: 0
    };
  }
}

/**
 * Mark a single notification as read by ID.
 * 
 * @param {number|string} notificationId 
 * @returns {Promise<{success: boolean, id: number|string}>}
 */
async function markNotificationRead(notificationId) {
  const targetUrl = `${API_CONFIG.BASE_URL}/${API_CONFIG.ENDPOINTS.MARK_SINGLE_READ}`;

  try {
    const response = await fetch(targetUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ id: notificationId })
    });

    if (response.ok) {
      const idx = mockNotificationsStore.findIndex(n => n.id === notificationId);
      if (idx !== -1) mockNotificationsStore[idx].is_read = true;
      return { success: true, id: notificationId, source: 'real-backend' };
    } else {
      throw new Error(`HTTP ${response.status}`);
    }
  } catch (err) {
    if (API_CONFIG.USE_MOCK_FALLBACK) {
      const item = mockNotificationsStore.find(n => n.id === Number(notificationId));
      if (item) item.is_read = true;
      return { success: true, id: notificationId, source: 'mock-standalone' };
    }
    return { success: false, error: err.message };
  }
}

/**
 * Helper to push a new incoming notification (for demonstration & testing of FEB-01 live badge count increment)
 * 
 * @param {Object} notificationData
 */
function pushMockNotification(notificationData) {
  const newNotification = {
    id: Date.now(),
    title: notificationData.title || "New System Event",
    message: notificationData.message || "Notification event received from CRM pipeline.",
    category: notificationData.category || "system",
    category_label: notificationData.category_label || "Update",
    timestamp: "Just now",
    created_at: new Date().toISOString(),
    is_read: false,
    priority: notificationData.priority || "normal",
    action_url: notificationData.action_url || "#"
  };

  mockNotificationsStore.unshift(newNotification);
  return newNotification;
}

/**
 * Helper to clear all notifications (to test empty state UI)
 */
function clearMockNotifications() {
  mockNotificationsStore = [];
}

/**
 * Helper to reset dataset to initial state
 */
function resetMockNotifications() {
  mockNotificationsStore = [
    {
      id: 101,
      title: "Lead Stage Progression",
      message: "CRM Lead #DL-8429 (Acme Corp Cloud Expansion) advanced from 'Proposal Review' to 'Contract Negotiation'.",
      category: "milestone",
      category_label: "CRM Milestone",
      timestamp: "10 mins ago",
      created_at: new Date(Date.now() - 10 * 60 * 1000).toISOString(),
      is_read: false,
      priority: "normal",
      action_url: "#lead-8429"
    },
    {
      id: 102,
      title: "Urgent: Opportunity Approval Required",
      message: "Senior Partner approved pricing terms for Sprint 2 Master Services Agreement. Awaiting client digital sign-off.",
      category: "high-priority",
      category_label: "Critical Action",
      timestamp: "25 mins ago",
      created_at: new Date(Date.now() - 25 * 60 * 1000).toISOString(),
      is_read: false,
      priority: "high",
      action_url: "#contract-preview"
    },
    {
      id: 103,
      title: "Contract Document Generated",
      message: "New statement of work PDF v2.4 ready for review in digital agreement vault.",
      category: "agreement",
      category_label: "Agreement",
      timestamp: "2 hours ago",
      created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(),
      is_read: false,
      priority: "normal",
      action_url: "#docs"
    },
    {
      id: 104,
      title: "Client Portal Security Check",
      message: "Two-factor authentication successfully verified from IP 192.168.1.42.",
      category: "account",
      category_label: "Account",
      timestamp: "Yesterday",
      created_at: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString(),
      is_read: true,
      priority: "low",
      action_url: "#security"
    }
  ];
}

// Expose API to window for Vanilla JS scripts
window.DevLogixAPI = {
  CONFIG: API_CONFIG,
  getNotifications,
  markAllNotificationsRead,
  markNotificationRead,
  pushMockNotification,
  clearMockNotifications,
  resetMockNotifications
};
