<style>
/* ======================================================
   MASTER DATA — Shared UI Styles (roles, users, classes, subjects)
   ====================================================== */

/* ── PAGE HEADER ── */
.md-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.md-page-title {
    display: flex;
    align-items: center;
    gap: .75rem;
}
.md-page-icon {
    width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
}
.md-title {
    font-size: .95rem; font-weight: 700;
    color: var(--tblr-heading-color, #0f172a);
    margin: 0; line-height: 1.2;
}
.md-subtitle {
    font-size: .74rem; color: var(--tblr-text-muted, #64748b); margin-top: .1rem;
}

/* ── BUTTONS ── */
.md-btn-primary {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem 1rem; border-radius: 8px; font-size: .8rem; font-weight: 600;
    background: var(--tblr-primary, #206bc4); color: #fff; text-decoration: none;
    border: none; cursor: pointer; transition: all .18s ease; white-space: nowrap;
}
.md-btn-primary:hover { background: var(--tblr-primary-hover, #1a569d); color: #fff; transform: translateY(-1px); }
.md-btn-secondary {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .42rem .9rem; border-radius: 8px; font-size: .8rem; font-weight: 600;
    background: var(--tblr-card-bg, #fff); color: var(--tblr-text-muted, #64748b);
    text-decoration: none; border: 1px solid var(--tblr-border-color, #e2e8f0);
    cursor: pointer; transition: all .18s ease; white-space: nowrap;
}
.md-btn-secondary:hover { border-color: var(--tblr-primary, #206bc4); color: var(--tblr-primary, #206bc4); }
.md-btn-light {
    padding: .35rem .9rem; border-radius: 8px; font-size: .8rem; font-weight: 600;
    background: var(--tblr-body-bg, #f4f6fa); color: var(--tblr-text-muted, #64748b);
    border: 1px solid var(--tblr-border-color, #e2e8f0); cursor: pointer;
    transition: all .15s ease;
}
.md-btn-light:hover { border-color: var(--tblr-border-color-hover, #cbd5e1); }
.md-btn-danger {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .35rem .9rem; border-radius: 8px; font-size: .8rem; font-weight: 600;
    background: #ef4444; color: #fff; border: none; cursor: pointer; transition: all .15s ease;
}
.md-btn-danger:hover { background: #dc2626; }
.md-btn-submit {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem 1.1rem; border-radius: 8px; font-size: .8rem; font-weight: 600;
    background: var(--tblr-primary, #206bc4); color: #fff; border: none; cursor: pointer;
    transition: all .18s ease;
}
.md-btn-submit:hover { background: var(--tblr-primary-hover, #1a569d); }

/* ── FLASH ALERTS ── */
.md-alert {
    display: flex; align-items: center; gap: .6rem;
    padding: .7rem 1rem; border-radius: 10px; font-size: .82rem; font-weight: 500;
    position: relative;
}
.md-alert.success { background: rgba(12,166,120,.08); border: 1px solid rgba(12,166,120,.2); color: #0ca678; }
.md-alert.danger  { background: rgba(239,68,68,.08);  border: 1px solid rgba(239,68,68,.2);  color: #ef4444; }
.md-alert-close {
    margin-left: auto; background: none; border: none; cursor: pointer;
    color: inherit; opacity: .7; padding: 0; line-height: 1; font-size: .9rem;
}
.md-alert-close:hover { opacity: 1; }

/* ── CARD SHELL ── */
.md-card {
    background: var(--tblr-card-bg, #fff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 12px; overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.md-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.md-card-footer {
    padding: .65rem 1rem;
    border-top: 1px solid var(--tblr-border-color, #e2e8f0);
    display: flex; justify-content: flex-end;
}

/* ── TABLE ── */
.md-table { width: 100%; min-width: 480px; border-collapse: collapse; }
.md-table thead th {
    font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    color: var(--tblr-text-muted, #64748b);
    padding: .75rem 1rem;
    border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
    white-space: nowrap; background: var(--tblr-card-bg, #fff);
}
.md-table tbody td {
    padding: .7rem 1rem;
    border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
    font-size: .82rem; vertical-align: middle;
    color: var(--tblr-body-color, #1e293b);
}
.md-table tbody tr:last-child td { border-bottom: none; }
.md-table tbody tr { transition: background .13s ease; }
.md-table tbody tr:hover { background: rgba(32,107,196,.03); }
[data-theme="dark"] .md-table tbody tr:hover { background: rgba(59,130,246,.05); }

table.md-table th.md-th-no,
table.md-table thead th.md-th-no,
.md-th-no     { width: 56px !important; text-align: center !important; }
table.md-table td.md-td-no,
table.md-table tbody td.md-td-no,
.md-td-no     { width: 56px !important; text-align: center !important; font-weight: 700; color: var(--tblr-text-muted, #64748b); font-size: .78rem; }
table.md-table th.md-th-action,
table.md-table thead th.md-th-action { width: 110px !important; text-align: center !important; padding: .75rem .5rem !important; }
table.md-table td.md-td-action,
table.md-table tbody td.md-td-action { text-align: center !important; padding: .7rem .5rem !important; }
.md-action-group { display: flex !important; align-items: center !important; justify-content: center !important; gap: .35rem; margin: 0 auto; }

.md-row-name { display: flex; align-items: center; gap: .6rem; }
.md-row-icon {
    width: 28px; height: 28px; border-radius: 7px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .82rem;
}
.md-empty-row {
    text-align: center; padding: 2.5rem 1rem !important;
    color: var(--tblr-text-muted, #64748b); font-size: .82rem;
    display: flex; flex-direction: column; align-items: center; gap: .4rem;
}
.md-empty-row i { font-size: 1.5rem; opacity: .4; }

/* ── BADGES ── */
.md-badge {
    display: inline-flex; align-items: center; gap: .25rem;
    font-size: .66rem; font-weight: 700; padding: .22em .55em; border-radius: 50px;
    white-space: nowrap;
}
.md-badge.teal   { background: rgba(12,166,120,.1); border: 1px solid rgba(12,166,120,.2); color: #0ca678; }
.md-badge.blue   { background: rgba(32,107,196,.1); border: 1px solid rgba(32,107,196,.2); color: #206bc4; }
.md-badge.amber  { background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.2); color: #d97706; }
.md-badge.rose   { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.2);  color: #ef4444; }
.md-badge.purple { background: rgba(139,92,246,.1); border: 1px solid rgba(139,92,246,.2); color: #8b5cf6; }
.md-badge.muted  { background: rgba(100,116,139,.1);border: 1px solid rgba(100,116,139,.2);color: #64748b; }

/* ── ACTION BUTTONS ── */
.md-action-group { display: flex; align-items: center; justify-content: flex-end; gap: .35rem; }
.md-icon-btn {
    width: 30px; height: 30px; border-radius: 7px; border: 1px solid transparent;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .9rem; cursor: pointer; background: none; transition: all .15s ease;
    text-decoration: none;
}
.md-icon-btn.blue  { color: #206bc4; border-color: rgba(32,107,196,.2);  background: rgba(32,107,196,.06); }
.md-icon-btn.blue:hover  { background: #206bc4; color: #fff; border-color: #206bc4; }
.md-icon-btn.teal  { color: #0891b2; border-color: rgba(8,145,178,.2);   background: rgba(8,145,178,.06); }
.md-icon-btn.teal:hover  { background: #0891b2; color: #fff; border-color: #0891b2; }
.md-icon-btn.red   { color: #ef4444; border-color: rgba(239,68,68,.2);   background: rgba(239,68,68,.06); }
.md-icon-btn.red:hover   { background: #ef4444; color: #fff; border-color: #ef4444; }
.md-icon-btn.green { color: #0ca678; border-color: rgba(12,166,120,.2);  background: rgba(12,166,120,.06); }
.md-icon-btn.green:hover { background: #0ca678; color: #fff; border-color: #0ca678; }


/* ── MODAL ── */
.md-modal-content {
    background: var(--tblr-card-bg, #fff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 14px; padding: 1.75rem 1.5rem; text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,.12);
}
.md-modal-icon {
    width: 52px; height: 52px; border-radius: 50%; margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center; font-size: 1.4rem;
}
.md-modal-icon.danger { background: rgba(239,68,68,.1); color: #ef4444; }
.md-modal-icon.warning { background: rgba(245,158,11,.1); color: #f59e0b; }
.md-modal-title { font-size: .92rem; font-weight: 700; color: var(--tblr-heading-color, #0f172a); margin-bottom: .4rem; }
.md-modal-text  { font-size: .78rem; color: var(--tblr-text-muted, #64748b); margin-bottom: 1.25rem; }
.md-modal-actions { display: flex; align-items: center; justify-content: center; gap: .5rem; flex-wrap: wrap; }

/* ── FORM PAGE ── */
.md-form-card {
    background: var(--tblr-card-bg, #fff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 12px; overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    width: 100%;
    max-width: 100%;
}
.md-form-head {
    padding: .9rem 1.25rem;
    border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
    display: flex; align-items: center; gap: .6rem;
}
.md-form-head-icon {
    width: 28px; height: 28px; border-radius: 7px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .85rem;
}
.md-form-head-title { font-size: .88rem; font-weight: 700; color: var(--tblr-heading-color, #0f172a); margin: 0; }
.md-form-body { padding: 1.25rem; }

.md-form-label {
    font-size: .78rem; font-weight: 700; color: var(--tblr-heading-color, #0f172a);
    margin-bottom: .4rem; display: block;
}
.md-form-hint { font-size: .7rem; color: var(--tblr-text-muted, #64748b); margin-top: .3rem; }
.md-form-footer {
    padding: .85rem 1.25rem;
    border-top: 1px solid var(--tblr-border-color, #e2e8f0);
    display: flex; justify-content: flex-end; gap: .5rem; flex-wrap: wrap;
}

/* Checkbox list */
.md-check-list {
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 8px; overflow: hidden; max-height: 220px; overflow-y: auto;
}
.md-check-item {
    display: flex; align-items: center; gap: .65rem;
    padding: .55rem .85rem;
    border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
    transition: background .13s;
}
.md-check-item:last-child { border-bottom: none; }
.md-check-item:hover { background: rgba(32,107,196,.04); }
.md-check-item input[type="checkbox"] { width: 15px; height: 15px; cursor: pointer; accent-color: var(--tblr-primary, #206bc4); }
.md-check-label { font-size: .8rem; font-weight: 500; color: var(--tblr-body-color, #1e293b); cursor: pointer; flex: 1; }
.md-check-sub   { font-size: .7rem; color: var(--tblr-text-muted, #64748b); }

/* User avatar in table */
.md-user-avatar {
    width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; font-weight: 800; color: #fff;
}
.md-user-name  { font-size: .82rem; font-weight: 700; color: var(--tblr-heading-color, #0f172a); }
.md-user-email { font-size: .7rem; color: var(--tblr-text-muted, #64748b); }

/* Stat mini tags */
.md-stat-row { display: flex; flex-wrap: wrap; gap: .25rem; }
.md-stat { font-size: .62rem; font-weight: 600; padding: .15em .45em; border-radius: 50px; background: rgba(0,0,0,.05); color: var(--tblr-text-muted, #64748b); border: 1px solid rgba(0,0,0,.07); white-space: nowrap; }
[data-theme="dark"] .md-stat { background: rgba(255,255,255,.07); border-color: rgba(255,255,255,.1); color: #8a99ad; }

/* ── RESPONSIVE ── */
@media (max-width: 576px) {
    .md-page-header  { gap: .6rem; }
    .md-page-icon    { width: 34px; height: 34px; font-size: .95rem; }
    .md-title        { font-size: .88rem; }
    .md-subtitle     { font-size: .68rem; }
    .md-btn-primary span { display: none; }
    .md-btn-primary  { padding: .45rem .65rem; }

    .md-table thead th, .md-table tbody td { padding: .6rem .75rem; font-size: .78rem; }
    .md-th-no, .md-td-no { width: 36px; padding-left: .5rem !important; padding-right: .5rem !important; }
    .md-icon-btn     { width: 27px; height: 27px; font-size: .82rem; }

    .md-form-body    { padding: 1rem; }
    .md-form-footer  { padding: .75rem 1rem; }
}
</style>
