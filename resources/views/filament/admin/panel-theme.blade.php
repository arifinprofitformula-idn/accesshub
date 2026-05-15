<style>
    .fi-body.fi-panel-admin {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(56, 189, 248, 0.16), transparent 24%),
            radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.14), transparent 22%),
            linear-gradient(180deg, #020617 0%, #08111f 100%) !important;
        color: #e2e8f0;
    }

    .fi-body.fi-panel-admin::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(148, 163, 184, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(148, 163, 184, 0.05) 1px, transparent 1px);
        background-size: 34px 34px;
        pointer-events: none;
        z-index: 0;
    }

    .fi-body.fi-panel-admin main,
    .fi-body.fi-panel-admin .fi-main,
    .fi-body.fi-panel-admin .fi-page,
    .fi-body.fi-panel-admin .fi-ta,
    .fi-body.fi-panel-admin .fi-wi,
    .fi-body.fi-panel-admin .fi-section {
        position: relative;
        z-index: 1;
    }

    .fi-body.fi-panel-admin .fi-topbar {
        background: rgba(2, 6, 23, 0.72) !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
        backdrop-filter: blur(22px);
    }

    .fi-body.fi-panel-admin .fi-sidebar {
        background:
            linear-gradient(180deg, rgba(2, 6, 23, 0.94) 0%, rgba(8, 17, 31, 0.92) 100%) !important;
        border-inline-end: 1px solid rgba(148, 163, 184, 0.1) !important;
        backdrop-filter: blur(24px);
    }

    .fi-body.fi-panel-admin .fi-sidebar-header,
    .fi-body.fi-panel-admin .fi-topbar-start {
        color: #f8fafc;
    }

    .fi-body.fi-panel-admin .fi-logo {
        filter: drop-shadow(0 0 18px rgba(34, 211, 238, 0.22));
    }

    .fi-body.fi-panel-admin .fi-sidebar-group-label,
    .fi-body.fi-panel-admin .fi-sidebar-item-label,
    .fi-body.fi-panel-admin .fi-topbar-item-label,
    .fi-body.fi-panel-admin .fi-breadcrumbs-item-label,
    .fi-body.fi-panel-admin .fi-dropdown-list-item-label,
    .fi-body.fi-panel-admin label,
    .fi-body.fi-panel-admin legend,
    .fi-body.fi-panel-admin .fi-fo-field-label,
    .fi-body.fi-panel-admin .fi-ta-cell-label {
        color: #dbeafe !important;
    }

    .fi-body.fi-panel-admin .fi-sidebar-group-btn {
        color: #7dd3fc !important;
        font-size: 0.72rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    .fi-body.fi-panel-admin .fi-sidebar-item-btn {
        border: 1px solid transparent;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.03) !important;
        margin-bottom: 0.35rem;
        transition: 0.2s ease;
    }

    .fi-body.fi-panel-admin .fi-sidebar-item-btn:hover,
    .fi-body.fi-panel-admin .fi-topbar-item-btn:hover,
    .fi-body.fi-panel-admin .fi-icon-btn:hover {
        background: rgba(255, 255, 255, 0.08) !important;
    }

    .fi-body.fi-panel-admin .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
    .fi-body.fi-panel-admin .fi-sidebar-item.fi-sidebar-item-has-active-child-items > .fi-sidebar-item-btn {
        border-color: rgba(125, 211, 252, 0.22) !important;
        background: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 18px 40px -28px rgba(34, 211, 238, 0.85);
    }

    .fi-body.fi-panel-admin .fi-sidebar-item-icon,
    .fi-body.fi-panel-admin .fi-topbar-item-icon,
    .fi-body.fi-panel-admin .fi-icon-btn-icon {
        color: #cbd5e1 !important;
    }

    .fi-body.fi-panel-admin .fi-sidebar-item a[href*="/admin/links"] .fi-sidebar-item-icon {
        color: #67e8f9 !important;
    }

    .fi-body.fi-panel-admin .fi-sidebar-item a[href*="/admin/access-items"] .fi-sidebar-item-icon {
        color: #fbbf24 !important;
    }

    .fi-body.fi-panel-admin .fi-sidebar-item a[href*="/admin/categories"] .fi-sidebar-item-icon {
        color: #4ade80 !important;
    }

    .fi-body.fi-panel-admin .fi-sidebar-item a[href*="/admin/users"] .fi-sidebar-item-icon {
        color: #e879f9 !important;
    }

    .fi-body.fi-panel-admin .fi-badge {
        border-radius: 999px;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    .fi-body.fi-panel-admin .fi-header {
        gap: 0.85rem;
    }

    .fi-body.fi-panel-admin .fi-header-heading,
    .fi-body.fi-panel-admin .fi-ta-header-heading {
        color: #f8fafc !important;
        font-size: clamp(1.4rem, 2vw, 2rem);
        letter-spacing: -0.03em;
        font-weight: 700;
    }

    .fi-body.fi-panel-admin .fi-header-subheading,
    .fi-body.fi-panel-admin .fi-ta-header-description,
    .fi-body.fi-panel-admin .fi-breadcrumbs,
    .fi-body.fi-panel-admin .fi-ta-filter-indicators-label {
        color: #94a3b8 !important;
        max-width: 56ch;
    }

    .fi-body.fi-panel-admin .fi-section,
    .fi-body.fi-panel-admin .fi-ta-ctn,
    .fi-body.fi-panel-admin .fi-dropdown-panel,
    .fi-body.fi-panel-admin .fi-modal-window,
    .fi-body.fi-panel-admin .fi-in-entry,
    .fi-body.fi-panel-admin .fi-fo-builder-item-picker {
        background: rgba(8, 17, 31, 0.76) !important;
        border: 1px solid rgba(148, 163, 184, 0.12) !important;
        border-radius: 1.4rem !important;
        box-shadow: 0 22px 70px -42px rgba(34, 211, 238, 0.35) !important;
        backdrop-filter: blur(24px);
    }

    .fi-body.fi-panel-admin .fi-section-header-heading,
    .fi-body.fi-panel-admin .fi-fo-section-header-heading,
    .fi-body.fi-panel-admin .fi-ta-group-heading,
    .fi-body.fi-panel-admin .fi-ta-empty-state-heading {
        color: #f8fafc !important;
    }

    .fi-body.fi-panel-admin .fi-section-header-description,
    .fi-body.fi-panel-admin .fi-fo-section-header-description,
    .fi-body.fi-panel-admin .fi-ta-empty-state-description {
        color: #94a3b8 !important;
    }

    .fi-body.fi-panel-admin .fi-input-wrp,
    .fi-body.fi-panel-admin .fi-select-input,
    .fi-body.fi-panel-admin .choices__inner,
    .fi-body.fi-panel-admin .fi-ta-search-field,
    .fi-body.fi-panel-admin .fi-fo-rich-editor-toolbar {
        background: rgba(2, 6, 23, 0.72) !important;
        border-color: rgba(148, 163, 184, 0.14) !important;
        border-radius: 1rem !important;
    }

    .fi-body.fi-panel-admin .fi-input,
    .fi-body.fi-panel-admin .fi-select-input,
    .fi-body.fi-panel-admin .choices__input,
    .fi-body.fi-panel-admin .choices__item,
    .fi-body.fi-panel-admin .fi-fo-field-label-content,
    .fi-body.fi-panel-admin .fi-fo-field-wrp-helper-text,
    .fi-body.fi-panel-admin .fi-fo-field-wrp-hint,
    .fi-body.fi-panel-admin .fi-fo-placeholder,
    .fi-body.fi-panel-admin .fi-ta-cell,
    .fi-body.fi-panel-admin .fi-dropdown-list-item,
    .fi-body.fi-panel-admin .fi-modal-description,
    .fi-body.fi-panel-admin .fi-dropdown-header {
        color: #dbeafe !important;
    }

    .fi-body.fi-panel-admin .fi-input::placeholder,
    .fi-body.fi-panel-admin .choices__input::placeholder,
    .fi-body.fi-panel-admin textarea::placeholder {
        color: #94a3b8 !important;
    }

    .fi-body.fi-panel-admin .fi-ta-header-cell,
    .fi-body.fi-panel-admin .fi-dropdown-list-item-description,
    .fi-body.fi-panel-admin .fi-modal-description,
    .fi-body.fi-panel-admin .fi-fo-field-wrp-helper-text,
    .fi-body.fi-panel-admin .fi-fo-field-wrp-hint {
        color: #cbd5e1 !important;
    }

    .fi-body.fi-panel-admin .fi-btn,
    .fi-body.fi-panel-admin .fi-icon-btn {
        border-radius: 1rem !important;
    }

    .fi-body.fi-panel-admin .fi-btn.fi-color-primary {
        background: linear-gradient(135deg, #67e8f9 0%, #38bdf8 48%, #0ea5e9 100%) !important;
        color: #082f49 !important;
        border-color: transparent !important;
        box-shadow: 0 18px 40px -24px rgba(34, 211, 238, 0.85) !important;
    }

    .fi-body.fi-panel-admin .fi-btn:not(.fi-color-primary),
    .fi-body.fi-panel-admin .fi-icon-btn {
        background: rgba(255, 255, 255, 0.06) !important;
        border-color: rgba(148, 163, 184, 0.12) !important;
        color: #e2e8f0 !important;
    }

    .fi-body.fi-panel-admin .fi-ta-header,
    .fi-body.fi-panel-admin .fi-ta-header-toolbar,
    .fi-body.fi-panel-admin .fi-ta-filters,
    .fi-body.fi-panel-admin .fi-ta-group-header,
    .fi-body.fi-panel-admin .fi-ta-selection-indicator {
        background: rgba(255, 255, 255, 0.025) !important;
        border-color: rgba(148, 163, 184, 0.08) !important;
        border-radius: 1.15rem !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    .fi-body.fi-panel-admin .fi-ta-table,
    .fi-body.fi-panel-admin .fi-ta-row,
    .fi-body.fi-panel-admin .fi-ta-cell,
    .fi-body.fi-panel-admin .fi-ta-header-cell {
        border-color: rgba(148, 163, 184, 0.07) !important;
    }

    .fi-body.fi-panel-admin .fi-ta-table {
        border-collapse: separate !important;
        border-spacing: 0 0.65rem !important;
        background: transparent !important;
    }

    .fi-body.fi-panel-admin .fi-ta-row,
    .fi-body.fi-panel-admin .fi-ta-record {
        background: rgba(255, 255, 255, 0.025) !important;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.025),
            0 14px 34px -26px rgba(15, 23, 42, 0.85);
        transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .fi-body.fi-panel-admin .fi-ta-cell,
    .fi-body.fi-panel-admin .fi-ta-header-cell,
    .fi-body.fi-panel-admin .fi-ta-group-header-cell {
        background: transparent !important;
        backdrop-filter: blur(10px);
    }

    .fi-body.fi-panel-admin .fi-ta-cell:first-child,
    .fi-body.fi-panel-admin .fi-ta-header-cell:first-child,
    .fi-body.fi-panel-admin .fi-ta-group-header-cell:first-child {
        border-top-left-radius: 1rem !important;
        border-bottom-left-radius: 1rem !important;
    }

    .fi-body.fi-panel-admin .fi-ta-cell:last-child,
    .fi-body.fi-panel-admin .fi-ta-header-cell:last-child,
    .fi-body.fi-panel-admin .fi-ta-group-header-cell:last-child {
        border-top-right-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
    }

    .fi-body.fi-panel-admin .fi-ta-header-cell {
        background: rgba(255, 255, 255, 0.03) !important;
        color: #dbeafe !important;
        font-weight: 600 !important;
        letter-spacing: 0.02em;
    }

    .fi-body.fi-panel-admin .fi-ta-cell-label {
        color: #94a3b8 !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .fi-body.fi-panel-admin .fi-ta-col,
    .fi-body.fi-panel-admin .fi-ta-record-content,
    .fi-body.fi-panel-admin .fi-ta-group-description {
        color: #dbeafe !important;
    }

    .fi-body.fi-panel-admin .fi-ta-group-description,
    .fi-body.fi-panel-admin .fi-ta-filter-indicators-badges-ctn,
    .fi-body.fi-panel-admin .fi-ta-record-content-ctn {
        opacity: 0.92;
    }

    .fi-body.fi-panel-admin .fi-ta-cell .fi-badge,
    .fi-body.fi-panel-admin .fi-ta-header .fi-badge {
        background: rgba(255, 255, 255, 0.06) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    .fi-body.fi-panel-admin .fi-ta-row:hover,
    .fi-body.fi-panel-admin .fi-ta-record:hover {
        background: rgba(255, 255, 255, 0.045) !important;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.04),
            0 18px 40px -28px rgba(34, 211, 238, 0.3) !important;
        transform: translateY(-1px);
    }

    .fi-body.fi-panel-admin .fi-ta-row.fi-ta-summary-row,
    .fi-body.fi-panel-admin .fi-ta-summary-header-row,
    .fi-body.fi-panel-admin .fi-ta-group-header-row {
        background: rgba(255, 255, 255, 0.018) !important;
        box-shadow: none !important;
    }

    .fi-body.fi-panel-admin .fi-ta-empty-state,
    .fi-body.fi-panel-admin .fi-ta-filter-indicators {
        border-radius: 1.2rem !important;
        background: rgba(255, 255, 255, 0.02) !important;
        border: 1px solid rgba(148, 163, 184, 0.08) !important;
    }

    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-ctn,
    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-header,
    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-header-toolbar,
    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-filters,
    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-empty-state,
    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-filter-indicators {
        background:
            linear-gradient(180deg, rgba(56, 189, 248, 0.08) 0%, rgba(14, 165, 233, 0.04) 100%) !important;
        border-color: rgba(125, 211, 252, 0.14) !important;
    }

    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-row,
    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-record {
        background:
            linear-gradient(180deg, rgba(125, 211, 252, 0.07) 0%, rgba(56, 189, 248, 0.035) 100%) !important;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.03),
            0 16px 36px -28px rgba(14, 165, 233, 0.45) !important;
    }

    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-header-cell,
    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-group-header-cell {
        background:
            linear-gradient(180deg, rgba(125, 211, 252, 0.08) 0%, rgba(56, 189, 248, 0.03) 100%) !important;
        border-color: rgba(125, 211, 252, 0.12) !important;
    }

    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-row:hover,
    .fi-body.fi-panel-admin .fi-page.fi-resource-access-items .fi-ta-record:hover {
        background:
            linear-gradient(180deg, rgba(125, 211, 252, 0.1) 0%, rgba(56, 189, 248, 0.05) 100%) !important;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.04),
            0 20px 42px -28px rgba(56, 189, 248, 0.42) !important;
    }

    .fi-body.fi-panel-admin .fi-ta-empty-state-icon-bg {
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.18), rgba(14, 165, 233, 0.08)) !important;
    }

    .fi-body.fi-panel-admin .fi-wi-stats-overview-stat {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(148, 163, 184, 0.1) !important;
        border-radius: 1.35rem !important;
        box-shadow: 0 22px 60px -42px rgba(34, 211, 238, 0.5);
    }

    .fi-body.fi-panel-admin .fi-wi-stats-overview-stat-label,
    .fi-body.fi-panel-admin .fi-wi-stats-overview-stat-description {
        color: #cbd5e1 !important;
    }

    .fi-body.fi-panel-admin .fi-wi-stats-overview-stat-value {
        color: #f8fafc !important;
    }

    .fi-admin-quick-actions {
        margin-bottom: 1rem;
    }

    .admin-quick-shell {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 1.5rem;
        background:
            radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 26%),
            linear-gradient(180deg, rgba(8, 17, 31, 0.88) 0%, rgba(2, 6, 23, 0.94) 100%);
        padding: 1.4rem;
        box-shadow: 0 28px 70px -44px rgba(34, 211, 238, 0.5);
    }

    .admin-quick-eyebrow {
        margin: 0;
        color: #67e8f9;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    .admin-quick-heading {
        margin: 0.6rem 0 0;
        color: #f8fafc;
        font-size: 1.2rem;
        line-height: 1.3;
        font-weight: 700;
    }

    .admin-quick-grid {
        margin-top: 1.2rem;
        display: grid;
        gap: 0.9rem;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .admin-quick-card {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        border-radius: 1.2rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        background: rgba(255, 255, 255, 0.04);
        padding: 1rem;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .admin-quick-card:hover {
        transform: translateY(-1px);
        background: rgba(255, 255, 255, 0.07);
    }

    .admin-quick-icon {
        display: inline-flex;
        height: 3rem;
        width: 3rem;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
        color: #082f49;
        box-shadow: 0 18px 36px -26px rgba(15, 23, 42, 0.9);
    }

    .admin-quick-icon svg {
        width: 1.3rem;
        height: 1.3rem;
    }

    .admin-quick-text {
        display: flex;
        min-width: 0;
        flex-direction: column;
    }

    .admin-quick-label {
        color: #f8fafc;
        font-size: 0.95rem;
        font-weight: 700;
    }

    .admin-quick-hint {
        color: #94a3b8;
        font-size: 0.76rem;
        margin-top: 0.15rem;
    }

    .admin-quick-card-cyan .admin-quick-icon {
        background: linear-gradient(135deg, #67e8f9 0%, #38bdf8 100%);
    }

    .admin-quick-card-amber .admin-quick-icon {
        background: linear-gradient(135deg, #fde68a 0%, #f59e0b 100%);
    }

    .admin-quick-card-emerald .admin-quick-icon {
        background: linear-gradient(135deg, #86efac 0%, #10b981 100%);
    }

    .admin-quick-card-fuchsia .admin-quick-icon {
        background: linear-gradient(135deg, #f0abfc 0%, #a855f7 100%);
    }

    @media (max-width: 1024px) {
        .admin-quick-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .admin-quick-shell {
            padding: 1rem;
        }

        .admin-quick-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
