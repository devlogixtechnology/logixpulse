/* ============================================================
   LogixPulse CRM — Application Logic
   Covers: data layer, navigation, kanban, drag-and-drop,
           search/filter, lead detail panel, notes, toast.
   All data persisted in localStorage (demo-safe, no backend).
   ============================================================ */
(() => {
  'use strict';

  // ── Constants ────────────────────────────────────────────────────────────
  const LS_LEADS = 'lp_v1_leads';
  const LS_NOTES = 'lp_v1_notes';

  const STAGES = [
    { id: 'new-lead',       label: 'New Lead',       cls: 'badge--blue'    },
    { id: 'contacted',      label: 'Contacted',      cls: 'badge--indigo'  },
    { id: 'meeting-booked', label: 'Meeting Booked', cls: 'badge--amber'   },
    { id: 'closed-won',     label: 'Closed / Won',   cls: 'badge--emerald' },
  ];

  const SEED = [
    { id:'L001', company:'Acme Cloud Technologies',  name:'John Harrington', value:45000,  stage:'new-lead',       source:'Inbound Enterprise',   assignee:'SM', aName:'Sarah Miller',    ini:'JH', industry:'Technology',  created:'2026-09-01', email:'john.h@acmecloud.io',          phone:'+1 (555) 234-5678' },
    { id:'L002', company:'Vertex Fintech Corp',       name:'Sarah Chen',      value:112000, stage:'contacted',      source:'LinkedIn Campaign',    assignee:'MS', aName:'Marcus Sterling', ini:'SC', industry:'Finance',     created:'2026-09-05', email:'schen@vertexfintech.com',      phone:'+1 (555) 345-6789' },
    { id:'L003', company:'Helix BioTech',             name:'Marcus Williams', value:78500,  stage:'meeting-booked', source:'Partner Referral',     assignee:'ER', aName:'Elena Rostova',   ini:'MW', industry:'Healthcare',  created:'2026-09-08', email:'mwilliams@helixbio.com',       phone:'+1 (555) 456-7890' },
    { id:'L004', company:'Krypton Data Platform',     name:'Elena Popov',     value:234000, stage:'closed-won',     source:'Self-Service Demo',    assignee:'SM', aName:'Sarah Miller',    ini:'EP', industry:'Data & AI',   created:'2026-08-20', email:'epopov@kryptondata.io',        phone:'+1 (555) 567-8901' },
    { id:'L005', company:'Starlight Media Group',     name:'David Okonkwo',   value:56000,  stage:'new-lead',       source:'Cold Outreach',        assignee:'DC', aName:'David Chen',      ini:'DO', industry:'Media',       created:'2026-09-14', email:'d.okonkwo@starlightmedia.com', phone:'+1 (555) 678-9012' },
    { id:'L006', company:'NovaSphere Systems',        name:'Jennifer Walsh',  value:89000,  stage:'contacted',      source:'Web Inbound',          assignee:'MS', aName:'Marcus Sterling', ini:'JW', industry:'SaaS',        created:'2026-09-10', email:'jwalsh@novasphere.io',         phone:'+1 (555) 789-0123' },
    { id:'L007', company:'Quantum Dynamics',          name:'Robert Lee',      value:145000, stage:'meeting-booked', source:'Trade Show',           assignee:'ER', aName:'Elena Rostova',   ini:'RL', industry:'Engineering', created:'2026-09-03', email:'rlee@quantumdyn.com',          phone:'+1 (555) 890-1234' },
    { id:'L008', company:'Pinnacle Analytics',        name:'Amanda Foster',   value:67500,  stage:'new-lead',       source:'LinkedIn Campaign',    assignee:'DC', aName:'David Chen',      ini:'AF', industry:'Analytics',   created:'2026-09-18', email:'afoster@pinnacleanalytics.com',phone:'+1 (555) 901-2345' },
    { id:'L009', company:'ClearPath Solutions',       name:'Michael Brown',   value:93000,  stage:'closed-won',     source:'Partner Referral',     assignee:'SM', aName:'Sarah Miller',    ini:'MB', industry:'Consulting',  created:'2026-08-15', email:'mbrown@clearpath.io',          phone:'+1 (555) 012-3456' },
    { id:'L010', company:'TechVault Industries',      name:'Lisa Chang',      value:178000, stage:'contacted',      source:'Executive Intro',      assignee:'MS', aName:'Marcus Sterling', ini:'LC', industry:'Technology',  created:'2026-09-07', email:'lchang@techvault.com',         phone:'+1 (555) 123-4567' },
  ];

  // ── Data Layer ────────────────────────────────────────────────────────────
  function getLeads() {
    try {
      const raw = localStorage.getItem(LS_LEADS);
      if (raw) return JSON.parse(raw);
    } catch(e) { /* fall through */ }
    const seeds = JSON.parse(JSON.stringify(SEED));
    localStorage.setItem(LS_LEADS, JSON.stringify(seeds));
    return seeds;
  }
  function saveLeads(leads) {
    localStorage.setItem(LS_LEADS, JSON.stringify(leads));
  }
  function getNotesMap() {
    try { return JSON.parse(localStorage.getItem(LS_NOTES) || '{}'); } catch(e) { return {}; }
  }
  function saveNote(leadId, text) {
    const map = getNotesMap();
    if (!map[leadId]) map[leadId] = [];
    map[leadId].unshift({ id: Date.now(), text: text.trim(), by: 'Alex Lawson', ts: new Date().toISOString() });
    localStorage.setItem(LS_NOTES, JSON.stringify(map));
    return map[leadId];
  }
  function getLeadNotes(leadId) {
    return getNotesMap()[leadId] || [];
  }

  // ── State ─────────────────────────────────────────────────────────────────
  let currentView = 'dashboard';
  let searchQ     = '';
  let activeLead  = null;
  let draggedId   = null;

  // ── DOM refs ──────────────────────────────────────────────────────────────
  const $ = id => document.getElementById(id);

  // ── Init ──────────────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    initNav();
    initTopSearch();
    initKanbanSearch();
    initPanel();
    showView('dashboard');
  });

  // ── Navigation ────────────────────────────────────────────────────────────
  function initNav() {
    // Sidebar nav links
    document.querySelectorAll('[data-nav]').forEach(el => {
      el.addEventListener('click', e => {
        e.preventDefault();
        const view = el.dataset.nav;
        document.querySelectorAll('[data-nav]').forEach(x => x.classList.remove('active'));
        document.querySelectorAll(`[data-nav="${view}"]`).forEach(x => x.classList.add('active'));
        showView(view);
        const bc = $('topnav-breadcrumb');
        if (bc && el.querySelector('.nav-label')) bc.textContent = el.querySelector('.nav-label').textContent.trim();
      });
    });

    // Account dropdown toggle (sidebar)
    const avatarBtn  = $('sidebar-avatar-btn');
    const dropdown   = $('account-dropdown');
    if (avatarBtn && dropdown) {
      avatarBtn.addEventListener('click', e => {
        e.stopPropagation();
        const isOpen = dropdown.classList.toggle('open');
        avatarBtn.classList.toggle('open', isOpen);
      });
      document.addEventListener('click', () => {
        dropdown.classList.remove('open');
        avatarBtn && avatarBtn.classList.remove('open');
      });
    }

    // Sign out
    $('signout-btn')?.addEventListener('click', () => {
      window.location.assign('/client_login/');
    });

    // Leaderboard "View Pipeline" buttons (may be multiple)
    document.querySelectorAll('.view-pipeline-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('[data-nav="leads"]').forEach(el => el.click());
      });
    });
  }

  // ── View Router ───────────────────────────────────────────────────────────
  function showView(viewId) {
    currentView = viewId;
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    const target = $('view-' + viewId);
    if (target) target.classList.add('active');
    if (viewId === 'leads') renderKanban(searchQ);
  }

  // ── Top Nav Search ────────────────────────────────────────────────────────
  function initTopSearch() {
    const inp = $('lead-search');
    if (!inp) return;
    inp.addEventListener('input', e => {
      searchQ = e.target.value.toLowerCase().trim();
      // Mirror to kanban filter input
      const ki = $('kanban-search');
      if (ki && ki !== document.activeElement) ki.value = e.target.value;
      if (currentView === 'leads') renderKanban(searchQ);
      // If on dashboard and user types, auto-navigate to leads
      if (currentView !== 'leads' && searchQ.length > 0) {
        document.querySelectorAll('[data-nav="leads"]').forEach(el => el.click());
      }
    });
    document.addEventListener('keydown', e => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        inp.focus();
      }
    });
  }

  // ── Kanban Search ─────────────────────────────────────────────────────────
  function initKanbanSearch() {
    const inp = $('kanban-search');
    if (!inp) return;
    inp.addEventListener('input', e => {
      searchQ = e.target.value.toLowerCase().trim();
      const top = $('lead-search');
      if (top && top !== document.activeElement) top.value = e.target.value;
      renderKanban(searchQ);
    });
  }

  // ── Kanban Render ─────────────────────────────────────────────────────────
  function renderKanban(query) {
    const board = $('kanban-board');
    if (!board) return;
    const leads = getLeads();

    STAGES.forEach(stage => {
      const col   = board.querySelector(`[data-stage="${stage.id}"]`);
      if (!col) return;
      const list  = col.querySelector('.kanban-cards');
      const badge = col.querySelector('.col-count');

      const filtered = leads.filter(l => {
        if (l.stage !== stage.id) return false;
        if (!query)   return true;
        return [l.company, l.name, l.industry, l.aName, l.source]
          .some(f => f.toLowerCase().includes(query));
      });

      if (badge) badge.textContent = filtered.length;
      if (!list) return;
      list.innerHTML = '';

      filtered.forEach(lead => {
        list.appendChild(buildCard(lead, query));
      });
    });

    bindDragDrop();
  }

  function buildCard(lead, query) {
    const stage = STAGES.find(s => s.id === lead.stage);
    const el    = document.createElement('div');
    el.className     = 'kanban-card';
    el.draggable     = true;
    el.dataset.id    = lead.id;
    el.setAttribute('role', 'listitem');

    el.innerHTML = `
      <div class="card-top">
        <span class="badge ${stage ? stage.cls : ''}">${esc(stage ? stage.label : lead.stage)}</span>
        <span class="card-value">$${Number(lead.value).toLocaleString()}</span>
      </div>
      <div class="card-company">${hl(lead.company, query)}</div>
      <div class="card-contact">${hl(lead.name, query)}</div>
      <div class="card-footer">
        <span class="card-source">${esc(lead.source)}</span>
        <span class="assignee-chip">${esc(lead.assignee)}</span>
      </div>`;

    el.addEventListener('click', () => openPanel(lead.id));
    return el;
  }

  // ── Drag-and-Drop ─────────────────────────────────────────────────────────
  function bindDragDrop() {
    const board = $('kanban-board');
    if (!board) return;

    board.querySelectorAll('.kanban-card').forEach(card => {
      card.addEventListener('dragstart', onDragStart);
      card.addEventListener('dragend',   onDragEnd);
    });

    board.querySelectorAll('.kanban-column').forEach(col => {
      col.addEventListener('dragover',  onDragOver);
      col.addEventListener('dragleave', onDragLeave);
      col.addEventListener('drop',      onDrop);
    });
  }

  function onDragStart(e) {
    draggedId = e.currentTarget.dataset.id;
    e.currentTarget.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', draggedId);
  }

  function onDragEnd(e) {
    e.currentTarget.classList.remove('dragging');
    document.querySelectorAll('.kanban-column').forEach(c => c.classList.remove('drag-over'));
    draggedId = null;
  }

  function onDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    e.currentTarget.classList.add('drag-over');
  }

  function onDragLeave(e) {
    if (!e.currentTarget.contains(e.relatedTarget)) {
      e.currentTarget.classList.remove('drag-over');
    }
  }

  function onDrop(e) {
    e.preventDefault();
    const col      = e.currentTarget;
    const newStage = col.dataset.stage;
    col.classList.remove('drag-over');

    if (!draggedId || !newStage) return;
    const leads = getLeads();
    const lead  = leads.find(l => l.id === draggedId);
    if (!lead || lead.stage === newStage) return;

    lead.stage = newStage;
    saveLeads(leads);
    renderKanban(searchQ);

    const stageLabel = STAGES.find(s => s.id === newStage)?.label || newStage;
    toast(`Lead moved to "${stageLabel}"`);
  }

  // ── Lead Detail Panel ─────────────────────────────────────────────────────
  function initPanel() {
    $('panel-backdrop')?.addEventListener('click', closePanel);
    $('panel-close-btn')?.addEventListener('click', closePanel);
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && $('lead-panel')?.classList.contains('open')) closePanel();
    });

    $('note-form')?.addEventListener('submit', e => {
      e.preventDefault();
      const inp  = $('note-input');
      const text = inp?.value.trim();
      if (!text || !activeLead) return;
      saveNote(activeLead, text);
      inp.value = '';
      renderNotes(activeLead);
      toast('Note added');
    });
  }

  function openPanel(leadId) {
    activeLead = leadId;
    const leads = getLeads();
    const lead  = leads.find(l => l.id === leadId);
    if (!lead) return;

    const stage = STAGES.find(s => s.id === lead.stage);

    setText('panel-company',  lead.company);
    setText('panel-contact',  lead.name);
    setText('panel-industry', lead.industry);
    setText('panel-value',    '$' + Number(lead.value).toLocaleString());
    setText('panel-source',   lead.source);
    setText('panel-assignee', lead.aName);
    setText('panel-email',    lead.email);
    setText('panel-phone',    lead.phone);
    setText('panel-created',  fmtDate(lead.created));
    setText('panel-initials', lead.ini);

    const stageEl = $('panel-stage');
    if (stageEl) {
      stageEl.textContent = stage ? stage.label : lead.stage;
      stageEl.className   = 'badge ' + (stage ? stage.cls : '');
    }

    renderNotes(leadId);

    $('lead-panel')?.classList.add('open');
    $('panel-backdrop')?.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closePanel() {
    $('lead-panel')?.classList.remove('open');
    $('panel-backdrop')?.classList.remove('open');
    document.body.style.overflow = '';
    activeLead = null;
  }

  function renderNotes(leadId) {
    const list  = $('notes-list');
    if (!list) return;
    const notes = getLeadNotes(leadId);

    if (!notes.length) {
      list.innerHTML = '<p class="notes-empty">No notes yet. Add the first note above.</p>';
      return;
    }
    list.innerHTML = notes.map(n => `
      <div class="note-item">
        <div class="note-header">
          <span class="note-author">${esc(n.by)}</span>
          <span class="note-time">${fmtTs(n.ts)}</span>
        </div>
        <p class="note-text">${esc(n.text)}</p>
      </div>`).join('');
  }

  // ── Toast ─────────────────────────────────────────────────────────────────
  function toast(msg) {
    let el = $('app-toast');
    if (!el) return;
    el.textContent = msg;
    el.classList.add('show');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('show'), 2800);
  }

  // ── Helpers ───────────────────────────────────────────────────────────────
  function setText(id, val) {
    const el = $(id);
    if (el) el.textContent = val || '—';
  }
  function esc(str) {
    return String(str)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function hl(text, query) {
    if (!query) return esc(text);
    const re = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');
    return esc(text).replace(re, '<mark>$1</mark>');
  }
  function fmtDate(iso) {
    if (!iso) return '—';
    try { return new Date(iso + 'T12:00:00').toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}); }
    catch(e) { return iso; }
  }
  function fmtTs(iso) {
    if (!iso) return '';
    try {
      const d    = new Date(iso);
      const diff = Math.floor((Date.now() - d) / 1000);
      if (diff < 60)    return 'Just now';
      if (diff < 3600)  return Math.floor(diff/60) + 'm ago';
      if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
      return d.toLocaleDateString('en-US',{month:'short',day:'numeric'});
    } catch(e) { return ''; }
  }

})();
