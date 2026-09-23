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
    gap: .85rem;
}
.md-page-icon {
    width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    background: #ffffff !important;
    color: #16465c !important;
    box-shadow: 0 2px 8px rgba(15, 45, 65, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.95);
}
.md-title {
    font-size: 1.15rem; font-weight: 800;
    color: #ffffff !important;
    text-shadow: 0 1px 3px rgba(15, 45, 65, 0.4);
    margin: 0; line-height: 1.2;
}
.md-title span {
    color: #ffffff !important;
    text-shadow: 0 1px 3px rgba(15, 45, 65, 0.4);
}
.md-subtitle {
    font-size: .78rem; color: rgba(255, 255, 255, 0.92) !important;
    text-shadow: 0 1px 2px rgba(15, 45, 65, 0.3);
    margin-top: .15rem;
}

[data-theme="dark"] .md-title {
    color: var(--tblr-heading-color, #f8fafc) !important;
    text-shadow: none;
}
[data-theme="dark"] .md-title span {
    color: #60a5fa !important;
    text-shadow: none;
}
[data-theme="dark"] .md-subtitle {
    color: var(--tblr-text-muted, #8a99ad) !important;
    text-shadow: none;
}
[data-theme="dark"] .md-page-icon {
    background: rgba(102, 163, 191, 0.15) !important;
    color: #66A3BF !important;
    border-color: rgba(102, 163, 191, 0.25);
}

/* ── BUTTONS ── */
.md-btn-primary {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .48rem 1.15rem; border-radius: 8px; font-size: .82rem; font-weight: 700;
    background: linear-gradient(135deg, #184e66 0%, #296d8c 100%);
    color: #ffffff !important; text-decoration: none;
    border: 1px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 4px 12px rgba(15, 45, 65, 0.22);
    cursor: pointer; transition: all .18s ease; white-space: nowrap;
}
.md-btn-primary:hover {
    background: linear-gradient(135deg, #10384a 0%, #1f566f 100%);
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(15, 45, 65, 0.3);
}
.md-btn-secondary {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem 1rem; border-radius: 8px; font-size: .82rem; font-weight: 700;
    background: #ffffff !important; color: #16465c !important;
    text-decoration: none; border: 1px solid rgba(15, 45, 65, 0.2) !important;
    box-shadow: 0 2px 6px rgba(15, 45, 65, 0.1);
    cursor: pointer; transition: all .18s ease; white-space: nowrap;
}
.md-btn-secondary:hover {
    border-color: #16465c !important; color: #0b2533 !important;
    background: #f0f8fb !important; transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(15, 45, 65, 0.16);
}
.md-btn-light {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem 1rem; border-radius: 8px; font-size: .82rem; font-weight: 700;
    background: #ffffff !important; color: #1e293b !important;
    border: 1px solid #cbd5e1 !important; cursor: pointer;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    transition: all .18s ease; text-decoration: none;
}
.md-btn-light:hover {
    background: #f1f5f9 !important; border-color: #94a3b8 !important;
    color: #0f172a !important; transform: translateY(-1px);
}
.md-btn-danger {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .45rem 1rem; border-radius: 8px; font-size: .82rem; font-weight: 700;
    background: #dc2626; color: #ffffff !important; border: none; cursor: pointer; transition: all .18s ease;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
}
.md-btn-danger:hover { background: #b91c1c; color: #ffffff !important; transform: translateY(-1px); }
.md-btn-submit {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .48rem 1.25rem; border-radius: 8px; font-size: .82rem; font-weight: 700;
    background: linear-gradient(135deg, #184e66 0%, #296d8c 100%);
    color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.2); cursor: pointer;
    box-shadow: 0 3px 10px rgba(15, 45, 65, 0.22);
    transition: all .18s ease;
}
.md-btn-submit:hover {
    background: linear-gradient(135deg, #10384a 0%, #1f566f 100%);
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 5px 14px rgba(15, 45, 65, 0.32);
}
.md-btn-warning {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem 1.1rem; border-radius: 8px; font-size: .82rem; font-weight: 700;
    background: #d97706; color: #ffffff !important; border: none; cursor: pointer;
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25);
    transition: all .18s ease; white-space: nowrap; text-decoration: none;
}
.md-btn-warning:hover { background: #b45309; color: #ffffff !important; transform: translateY(-1px); }

/* ── FLASH ALERTS ── */
.md-alert {
    display: flex; align-items: center; gap: .6rem;
    padding: .75rem 1.1rem; border-radius: 10px; font-size: .84rem; font-weight: 600;
    position: relative; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    line-height: 1.45;
}
.md-alert.success { background-color: #ecfdf5 !important; border: 1px solid #a7f3d0 !important; color: #065f46 !important; }
.md-alert.danger  { background-color: #fef2f2 !important; border: 1px solid #fca5a5 !important; color: #991b1b !important; }
.md-alert.warning { background-color: #fffbeb !important; border: 1px solid #fde68a !important; color: #92400e !important; }
.md-alert.info    { background-color: #f0f9ff !important; border: 1px solid #bae6fd !important; color: #075985 !important; }
.md-alert.success i { color: #047857 !important; }
.md-alert.danger i  { color: #b91c1c !important; }
.md-alert.warning i { color: #b45309 !important; }
.md-alert.info i    { color: #0369a1 !important; }

[data-theme="dark"] .md-alert.success { background-color: rgba(16, 185, 129, 0.16) !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; color: #34d399 !important; }
[data-theme="dark"] .md-alert.danger  { background-color: rgba(239, 68, 68, 0.16) !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; color: #f87171 !important; }
[data-theme="dark"] .md-alert.warning { background-color: rgba(245, 158, 11, 0.16) !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; color: #fbbf24 !important; }
[data-theme="dark"] .md-alert.info    { background-color: rgba(56, 189, 248, 0.16) !important; border: 1px solid rgba(56, 189, 248, 0.35) !important; color: #38bdf8 !important; }
[data-theme="dark"] .md-alert.success i { color: #6ee7b7 !important; }
[data-theme="dark"] .md-alert.danger i  { color: #fca5a5 !important; }
[data-theme="dark"] .md-alert.warning i { color: #fde047 !important; }
[data-theme="dark"] .md-alert.info i    { color: #7dd3fc !important; }

.md-alert-close {
    margin-left: auto; background: none; border: none; cursor: pointer;
    color: inherit; opacity: .7; padding: 0; line-height: 1; font-size: 1rem;
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
.md-table tbody tr:hover { background: rgba(102, 163, 191, 0.05); }
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
    background: rgba(102, 163, 191, 0.15) !important;
    color: #35728d !important;
}
/* ── EMPTY STATE ── */
.md-empty-row {
    text-align: center !important;
    vertical-align: middle !important;
    padding: 3.5rem 1.5rem !important;
    background: transparent !important;
}
.md-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    max-width: 440px;
    margin: 0 auto;
    text-align: center;
}
.md-empty-icon-wrap {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.85rem;
    margin-bottom: 0.85rem;
    transition: transform .2s ease;
}
.md-empty-icon-wrap.blue   { background: rgba(102, 163, 191, 0.12); color: #35728d; border: 1px solid rgba(102, 163, 191, 0.25); }
.md-empty-icon-wrap.teal   { background: rgba(12, 166, 120, 0.08); color: #0ca678; border: 1px solid rgba(12, 166, 120, 0.15); }
.md-empty-icon-wrap.amber  { background: rgba(245, 158, 11, 0.08); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.15); }
.md-empty-icon-wrap.purple { background: rgba(139, 92, 246, 0.08); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.15); }
.md-empty-icon-wrap.rose   { background: rgba(239, 68, 68, 0.08);  color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.15); }
.md-empty-icon-wrap.muted  { background: rgba(100, 116, 139, 0.08);color: #64748b; border: 1px solid rgba(100, 116, 139, 0.15); }

.md-empty-title {
    font-size: 0.98rem;
    font-weight: 700;
    color: var(--tblr-heading-color, #1e293b);
    margin-bottom: 0.35rem;
    letter-spacing: -0.2px;
}
.md-empty-desc {
    font-size: 0.83rem;
    color: var(--tblr-text-muted, #64748b);
    line-height: 1.5;
    margin-bottom: 0.85rem;
}
.md-empty-desc:last-child {
    margin-bottom: 0;
}
.md-empty-action {
    margin-top: 0.4rem;
}

/* ── BADGES ── */
.md-badge {
    display: inline-flex; align-items: center; gap: .25rem;
    font-size: .66rem; font-weight: 700; padding: .22em .55em; border-radius: 50px;
    white-space: nowrap;
}
.md-badge.teal   { background: rgba(12,166,120,.1); border: 1px solid rgba(12,166,120,.2); color: #0ca678; }
.md-badge.blue   { background: rgba(102,163,191,.15); border: 1px solid rgba(102,163,191,.25); color: #35728d; }
.md-badge.amber  { background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.2); color: #d97706; }
.md-badge.rose   { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.2);  color: #ef4444; }
.md-badge.purple { background: rgba(139,92,246,.1); border: 1px solid rgba(139,92,246,.2); color: #8b5cf6; }
.md-badge.muted  { background: rgba(100,116,139,.1);border: 1px solid rgba(100,116,139,.2);color: #64748b; }

/* ── ACTION BUTTONS ── */
.md-action-group { display: flex; align-items: center; justify-content: flex-end; gap: .35rem; }
.md-icon-btn {
    width: 32px; height: 32px; border-radius: 8px; border: 1px solid transparent;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .95rem; cursor: pointer; background: none; transition: all .18s ease;
    text-decoration: none; font-weight: 600;
}
.md-icon-btn.blue  { color: #16465c; border-color: rgba(22, 70, 92, 0.3); background: rgba(22, 70, 92, 0.08); }
.md-icon-btn.blue:hover  { background: #16465c; color: #ffffff !important; border-color: #16465c; transform: translateY(-1px); }
.md-icon-btn.teal  { color: #0e7490; border-color: rgba(14, 116, 144, 0.3); background: rgba(14, 116, 144, 0.08); }
.md-icon-btn.teal:hover  { background: #0e7490; color: #ffffff !important; border-color: #0e7490; transform: translateY(-1px); }
.md-icon-btn.red   { color: #b91c1c; border-color: rgba(185, 28, 28, 0.3); background: rgba(220, 38, 38, 0.08); }
.md-icon-btn.red:hover   { background: #dc2626; color: #ffffff !important; border-color: #dc2626; transform: translateY(-1px); }
.md-icon-btn.green { color: #047857; border-color: rgba(4, 120, 87, 0.3); background: rgba(5, 150, 105, 0.08); }
.md-icon-btn.green:hover { background: #059669; color: #ffffff !important; border-color: #059669; transform: translateY(-1px); }
.md-icon-btn.amber { color: #b45309; border-color: rgba(180, 83, 9, 0.3); background: rgba(217, 119, 6, 0.08); }
.md-icon-btn.amber:hover { background: #d97706; color: #ffffff !important; border-color: #d97706; transform: translateY(-1px); }


/* ── MODAL ── */
.md-modal-content {
    background: var(--tblr-card-bg, #fff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 14px; padding: 1.75rem 1.5rem; text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,.12);
    pointer-events: auto;
    position: relative;
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
    background: rgba(102, 163, 191, 0.15) !important;
    color: #35728d !important;
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
.md-check-item:hover { background: rgba(102, 163, 191, 0.06); }
.md-check-item input[type="checkbox"] { width: 15px; height: 15px; cursor: pointer; accent-color: var(--tblr-primary, #66A3BF); }
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

/* ── MATERIAL CONTENT LIGHT & DARK MODE SUPPORT ── */
.article-body,
.tinymce-content,
.md-material-content {
    color: var(--tblr-body-color, #1e293b);
    line-height: 1.75;
    font-size: 0.96rem;
    word-break: break-word;
    overflow-wrap: break-word;
}
.article-body p, .tinymce-content p { margin-bottom: 1rem; }
.article-body h1, .article-body h2, .article-body h3, .article-body h4, .article-body h5, .article-body h6,
.tinymce-content h1, .tinymce-content h2, .tinymce-content h3, .tinymce-content h4, .tinymce-content h5, .tinymce-content h6 {
    color: var(--tblr-heading-color, #0f172a); font-weight: 700; margin-top: 1.25rem; margin-bottom: 0.75rem;
}
.article-body a, .tinymce-content a { color: var(--tblr-primary, #206bc4); text-decoration: underline; }
.article-body table, .tinymce-content table { width: 100% !important; max-width: 100%; margin: 1rem 0; border-collapse: collapse; border: 1px solid var(--tblr-border-color, #e2e8f0); }
.article-body th, .article-body td, .tinymce-content th, .tinymce-content td { padding: 0.6rem 0.85rem; border: 1px solid var(--tblr-border-color, #e2e8f0); color: var(--tblr-body-color, inherit); }
.article-body th, .tinymce-content th { background-color: rgba(0, 0, 0, 0.03); font-weight: 700; }
.article-body blockquote, .tinymce-content blockquote { border-left: 4px solid var(--tblr-primary, #206bc4); padding: 0.5rem 1rem; margin: 1rem 0; background: rgba(32, 107, 196, 0.05); border-radius: 0 8px 8px 0; color: var(--tblr-body-color, #334155); }
.article-body pre, .tinymce-content pre { background: #1e293b !important; color: #f8fafc !important; padding: 0.85rem 1.1rem; border-radius: 8px; overflow-x: auto; font-size: 0.88em; }
.article-body code, .tinymce-content code { background: rgba(0, 0, 0, 0.06); color: #0f172a; padding: 0.15rem 0.35rem; border-radius: 4px; font-size: 0.88em; }

[data-bs-theme="dark"] .article-body, [data-bs-theme="dark"] .tinymce-content,
[data-theme="dark"] .article-body, [data-theme="dark"] .tinymce-content,
body.theme-dark .article-body, body.theme-dark .tinymce-content,
body.dark-mode .article-body, body.dark-mode .tinymce-content {
    color: var(--tblr-body-color, #e2e8f0) !important;
}
[data-bs-theme="dark"] .article-body *, [data-bs-theme="dark"] .tinymce-content *,
[data-theme="dark"] .article-body *, [data-theme="dark"] .tinymce-content *,
body.theme-dark .article-body *, body.theme-dark .tinymce-content *,
body.dark-mode .article-body *, body.dark-mode .tinymce-content * {
    color: inherit;
}
[data-bs-theme="dark"] .article-body p, [data-bs-theme="dark"] .tinymce-content p,
[data-theme="dark"] .article-body p, [data-theme="dark"] .tinymce-content p,
body.theme-dark .article-body p, body.theme-dark .tinymce-content p {
    color: var(--tblr-body-color, #e2e8f0) !important;
}
[data-bs-theme="dark"] .article-body span, [data-bs-theme="dark"] .tinymce-content span,
[data-theme="dark"] .article-body span, [data-theme="dark"] .tinymce-content span,
body.theme-dark .article-body span, body.theme-dark .tinymce-content span {
    color: var(--tblr-body-color, #e2e8f0) !important;
    background-color: transparent !important;
}
[data-bs-theme="dark"] .article-body h1, [data-bs-theme="dark"] .article-body h2, [data-bs-theme="dark"] .article-body h3, [data-bs-theme="dark"] .article-body h4, [data-bs-theme="dark"] .article-body h5, [data-bs-theme="dark"] .article-body h6,
[data-theme="dark"] .article-body h1, [data-theme="dark"] .article-body h2, [data-theme="dark"] .article-body h3, [data-theme="dark"] .article-body h4, [data-theme="dark"] .article-body h5, [data-theme="dark"] .article-body h6,
body.theme-dark .article-body h1, body.theme-dark .article-body h2, body.theme-dark .article-body h3, body.theme-dark .article-body h4, body.theme-dark .article-body h5, body.theme-dark .article-body h6 {
    color: var(--tblr-heading-color, #f8fafc) !important;
}
[data-bs-theme="dark"] .article-body a, [data-bs-theme="dark"] .tinymce-content a,
[data-theme="dark"] .article-body a, [data-theme="dark"] .tinymce-content a,
body.theme-dark .article-body a, body.theme-dark .tinymce-content a {
    color: #60a5fa !important;
}
[data-bs-theme="dark"] .article-body table, [data-bs-theme="dark"] .tinymce-content table,
[data-theme="dark"] .article-body table, [data-theme="dark"] .tinymce-content table,
body.theme-dark .article-body table, body.theme-dark .tinymce-content table {
    border-color: #334155 !important;
}
[data-bs-theme="dark"] .article-body th, [data-bs-theme="dark"] .article-body td,
[data-bs-theme="dark"] .tinymce-content th, [data-bs-theme="dark"] .tinymce-content td,
[data-theme="dark"] .article-body th, [data-theme="dark"] .article-body td,
[data-theme="dark"] .tinymce-content th, [data-theme="dark"] .tinymce-content td,
body.theme-dark .article-body th, body.theme-dark .article-body td,
body.theme-dark .tinymce-content th, body.theme-dark .tinymce-content td {
    border-color: #334155 !important;
    color: #e2e8f0 !important;
    background-color: transparent !important;
}
[data-bs-theme="dark"] .article-body th, [data-bs-theme="dark"] .tinymce-content th,
[data-theme="dark"] .article-body th, [data-theme="dark"] .tinymce-content th,
body.theme-dark .article-body th, body.theme-dark .tinymce-content th {
    background-color: #1e293b !important;
    color: #f8fafc !important;
}
[data-bs-theme="dark"] .article-body code, [data-bs-theme="dark"] .tinymce-content code,
[data-theme="dark"] .article-body code, [data-theme="dark"] .tinymce-content code,
body.theme-dark .article-body code, body.theme-dark .tinymce-content code {
    background: #334155 !important;
    color: #f1f5f9 !important;
}

html:not([data-bs-theme="dark"]):not([data-theme="dark"]) body:not(.theme-dark):not(.dark-mode) .article-body span[style*="color: rgb(255, 255, 255)"],
html:not([data-bs-theme="dark"]):not([data-theme="dark"]) body:not(.theme-dark):not(.dark-mode) .article-body span[style*="color: #fff"],
html:not([data-bs-theme="dark"]):not([data-theme="dark"]) body:not(.theme-dark):not(.dark-mode) .article-body span[style*="color: #ffffff"],
html:not([data-bs-theme="dark"]):not([data-theme="dark"]) body:not(.theme-dark):not(.dark-mode) .article-body p[style*="color: rgb(255, 255, 255)"],
html:not([data-bs-theme="dark"]):not([data-theme="dark"]) body:not(.theme-dark):not(.dark-mode) .article-body p[style*="color: #fff"],
html:not([data-bs-theme="dark"]):not([data-theme="dark"]) body:not(.theme-dark):not(.dark-mode) .article-body p[style*="color: #ffffff"] {
    color: #0f172a !important;
}
/* ── TRUE / FALSE SELECTION CARDS ── */
.qb-tf-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    margin: 0;
}
.qb-tf-card:hover {
    border-color: #cbd5e1;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
.qb-tf-card .qb-tf-radio {
    cursor: pointer;
    width: 1.15rem;
    height: 1.15rem;
    margin: 0;
    flex-shrink: 0;
}
.qb-tf-content {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    font-size: 0.88rem;
}
.qb-tf-card-true .qb-tf-icon {
    color: #10b981;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
}
.qb-tf-card-false .qb-tf-icon {
    color: #ef4444;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
}
.qb-tf-card-true .qb-tf-title {
    color: #334155;
}
.qb-tf-card-false .qb-tf-title {
    color: #334155;
}

.qb-tf-card-true:has(.qb-tf-radio:checked) {
    background: #f0fdf4 !important;
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
}
.qb-tf-card-true:has(.qb-tf-radio:checked) .qb-tf-title {
    color: #065f46 !important;
    font-weight: 800;
}
.qb-tf-card-true:has(.qb-tf-radio:checked) .qb-tf-icon {
    color: #059669 !important;
}

.qb-tf-card-false:has(.qb-tf-radio:checked) {
    background: #fef2f2 !important;
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
}
.qb-tf-card-false:has(.qb-tf-radio:checked) .qb-tf-title {
    color: #991b1b !important;
    font-weight: 800;
}
.qb-tf-card-false:has(.qb-tf-radio:checked) .qb-tf-icon {
    color: #dc2626 !important;
}

[data-theme="dark"] .qb-tf-card {
    background: #1e293b;
    border-color: #334155;
}
[data-theme="dark"] .qb-tf-card:hover {
    background: #243044;
    border-color: #475569;
}
[data-theme="dark"] .qb-tf-card-true .qb-tf-title,
[data-theme="dark"] .qb-tf-card-false .qb-tf-title {
    color: #e2e8f0;
}
[data-theme="dark"] .qb-tf-card-true:has(.qb-tf-radio:checked) {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: #10b981 !important;
}
[data-theme="dark"] .qb-tf-card-true:has(.qb-tf-radio:checked) .qb-tf-title {
    color: #6ee7b7 !important;
}
[data-theme="dark"] .qb-tf-card-false:has(.qb-tf-radio:checked) {
    background: rgba(239, 68, 68, 0.15) !important;
    border-color: #ef4444 !important;
}
[data-theme="dark"] .qb-tf-card-false:has(.qb-tf-radio:checked) .qb-tf-title {
    color: #fca5a5 !important;
}
</style>
