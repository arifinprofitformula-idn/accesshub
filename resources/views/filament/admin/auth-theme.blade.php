<style>
    /* ═══════════════════════════════════════════
       PAGE BACKGROUND
    ═══════════════════════════════════════════ */
    html, body,
    .fi-body.fi-panel-admin {
        min-height: 100vh;
        background-color: #06000f !important;
        background-image:
            radial-gradient(ellipse 60% 50% at 20% 0%,   rgba(109, 40, 217, 0.28) 0%, transparent 100%),
            radial-gradient(ellipse 50% 40% at 80% 100%,  rgba(79,  70, 229, 0.20) 0%, transparent 100%),
            radial-gradient(ellipse 40% 60% at 50% 50%,   rgba(124, 58, 237, 0.06) 0%, transparent 100%) !important;
    }

    /* Dot grid */
    .fi-body.fi-panel-admin::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image: radial-gradient(rgba(139, 92, 246, 0.18) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
        z-index: 0;
    }

    /* Ambient glow orbs */
    .fi-body.fi-panel-admin::after {
        content: '';
        position: fixed;
        top: -8rem;
        left: -8rem;
        width: 32rem;
        height: 32rem;
        background: radial-gradient(circle, rgba(124, 58, 237, 0.22) 0%, transparent 70%);
        pointer-events: none;
        z-index: 0;
        animation: orb-drift 12s ease-in-out infinite alternate;
    }

    @keyframes orb-drift {
        from { transform: translate(0, 0);        }
        to   { transform: translate(3rem, 4rem);  }
    }

    /* ═══════════════════════════════════════════
       LAYOUT: center the card
    ═══════════════════════════════════════════ */
    .fi-panel-admin .fi-simple-layout {
        position: relative;
        z-index: 1;
        isolation: isolate;
    }

    .fi-panel-admin .fi-simple-main-ctn {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 100vh !important;
        padding: 2rem 1rem !important;
        position: relative;
        z-index: 1;
    }

    /* ═══════════════════════════════════════════
       CARD — kill Filament white, apply dark glass
    ═══════════════════════════════════════════ */
    .fi-panel-admin .fi-simple-main {
        background-color: transparent !important;
        background: transparent !important;
        box-shadow: none !important;
        --tw-shadow: none !important;
        --tw-ring-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: 26rem !important;
        width: 100% !important;
        border-radius: 0 !important;
    }

    .fi-panel-admin .fi-simple-page {
        width: 100%;
    }

    .fi-panel-admin .fi-simple-page-content {
        position: relative;
        background: rgba(10, 3, 28, 0.88) !important;
        backdrop-filter: blur(24px) !important;
        -webkit-backdrop-filter: blur(24px) !important;
        border: 1px solid rgba(139, 92, 246, 0.22) !important;
        border-radius: 1.5rem !important;
        padding: 2.25rem !important;
        box-shadow:
            0 0 0 1px rgba(124, 58, 237, 0.08),
            0 24px 64px rgba(0, 0, 0, 0.75),
            inset 0 1px 0 rgba(167, 139, 250, 0.08) !important;
        overflow: hidden;
    }

    /* Glowing top border line */
    .fi-panel-admin .fi-simple-page-content::before {
        content: '';
        position: absolute;
        top: 0; left: 10%; right: 10%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(167, 139, 250, 0.6), transparent);
    }

    /* ═══════════════════════════════════════════
       HEADER: logo + headings
    ═══════════════════════════════════════════ */
    .fi-panel-admin .fi-simple-header {
        text-align: center !important;
        margin-bottom: 1.5rem !important;
    }

    .fi-panel-admin .fi-simple-header .fi-logo {
        margin-inline: auto !important;
        margin-bottom: 0.875rem !important;
        filter: drop-shadow(0 0 20px rgba(167, 139, 250, 0.35)) !important;
    }

    .fi-panel-admin .fi-simple-header-heading {
        color: #ede9fe !important;
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.025em !important;
        line-height: 1.2 !important;
    }

    .fi-panel-admin .fi-simple-header-subheading {
        color: #94a3b8 !important;
        font-size: 0.8rem !important;
        margin-top: 0.35rem !important;
        line-height: 1.6 !important;
        max-width: 34ch !important;
        margin-inline: auto !important;
    }

    /* ═══════════════════════════════════════════
       FORM FIELDS
    ═══════════════════════════════════════════ */
    /* Filament 5 menggunakan .fi-fo-field-label-content untuk teks label */
    .fi-panel-admin .fi-fo-field-label-content,
    .fi-panel-admin .fi-fo-field-label-content * {
        color: #c4b5fd !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.03em !important;
        text-transform: none !important;
    }

    /* Input wrapper */
    .fi-panel-admin .fi-input-wrp {
        background: rgba(20, 8, 50, 0.8) !important;
        border: 1px solid rgba(109, 40, 217, 0.3) !important;
        border-radius: 0.75rem !important;
        box-shadow: none !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    .fi-panel-admin .fi-input-wrp:focus-within {
        border-color: rgba(139, 92, 246, 0.7) !important;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15), 0 0 16px rgba(124, 58, 237, 0.1) !important;
    }

    /* Input text */
    .fi-panel-admin .fi-input,
    .fi-panel-admin input.fi-input {
        color: #ede9fe !important;
        font-size: 0.9rem !important;
        background: transparent !important;
        caret-color: #a78bfa !important;
    }

    .fi-panel-admin .fi-input::placeholder {
        color: #8b9bb3 !important;
    }

    /* Password toggle icon */
    .fi-panel-admin .fi-input-wrp button {
        color: #6b5b9e !important;
    }
    .fi-panel-admin .fi-input-wrp button:hover {
        color: #a78bfa !important;
    }

    /* ═══════════════════════════════════════════
       CHECKBOX & REMEMBER ME
    ═══════════════════════════════════════════ */
    .fi-panel-admin .fi-checkbox [type="checkbox"] {
        accent-color: #7c3aed !important;
        border-color: rgba(109, 40, 217, 0.4) !important;
    }

    .fi-panel-admin .fi-checkbox label,
    .fi-panel-admin .fi-fo-field-wrp .fi-checkbox span {
        color: #cbd5e1 !important;
        font-size: 0.8rem !important;
    }

    /* ═══════════════════════════════════════════
       LINKS
    ═══════════════════════════════════════════ */
    .fi-panel-admin .fi-link,
    .fi-panel-admin a.fi-link {
        color: #8b5cf6 !important;
        font-size: 0.8rem !important;
        text-decoration: none !important;
        transition: color 0.15s !important;
    }

    .fi-panel-admin .fi-link:hover {
        color: #c4b5fd !important;
        text-decoration: underline !important;
    }

    /* ═══════════════════════════════════════════
       PRIMARY BUTTON
    ═══════════════════════════════════════════ */
    .fi-panel-admin .fi-btn.fi-color-primary {
        width: 100% !important;
        background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%) !important;
        border: 1px solid rgba(167, 139, 250, 0.25) !important;
        border-radius: 0.75rem !important;
        color: #f5f3ff !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        letter-spacing: 0.02em !important;
        padding-block: 0.7rem !important;
        box-shadow:
            0 4px 20px rgba(124, 58, 237, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        transition: all 0.2s ease !important;
        position: relative;
        overflow: hidden;
    }

    .fi-panel-admin .fi-btn.fi-color-primary::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .fi-panel-admin .fi-btn.fi-color-primary:hover {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%) !important;
        box-shadow:
            0 6px 28px rgba(124, 58, 237, 0.55),
            inset 0 1px 0 rgba(255, 255, 255, 0.12) !important;
        transform: translateY(-1px) !important;
    }

    .fi-panel-admin .fi-btn.fi-color-primary:active {
        transform: translateY(0) !important;
    }

    /* ═══════════════════════════════════════════
       SPACING
    ═══════════════════════════════════════════ */
    .fi-panel-admin .fi-fo-component-ctn {
        gap: 1.1rem !important;
    }

    .fi-panel-admin .fi-ac {
        margin-top: 1.25rem !important;
    }

    /* ═══════════════════════════════════════════
       MOBILE
    ═══════════════════════════════════════════ */
    @media (max-width: 640px) {
        .fi-panel-admin .fi-simple-main-ctn {
            padding: 1rem 0.75rem !important;
        }

        .fi-panel-admin .fi-simple-page-content {
            padding: 1.5rem 1.25rem !important;
            border-radius: 1.25rem !important;
        }
    }
</style>
