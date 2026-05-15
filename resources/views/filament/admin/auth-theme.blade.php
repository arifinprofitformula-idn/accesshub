<style>
    .fi-body.fi-panel-admin {
        background:
            radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 28%),
            radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.14), transparent 30%),
            linear-gradient(180deg, #020617 0%, #08111f 100%);
    }

    .fi-body.fi-panel-admin::before,
    .fi-body.fi-panel-admin::after {
        content: '';
        position: fixed;
        inset: auto;
        border-radius: 999px;
        pointer-events: none;
        filter: blur(80px);
        opacity: 0.85;
        z-index: 0;
    }

    .fi-body.fi-panel-admin::before {
        top: 5%;
        left: -6rem;
        width: 20rem;
        height: 20rem;
        background: rgba(56, 189, 248, 0.2);
    }

    .fi-body.fi-panel-admin::after {
        right: -5rem;
        bottom: 4%;
        width: 18rem;
        height: 18rem;
        background: rgba(14, 165, 233, 0.18);
    }

    .fi-panel-admin .fi-simple-layout {
        position: relative;
        min-height: 100vh;
        isolation: isolate;
    }

    .fi-panel-admin .fi-simple-main-ctn {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 2rem 1rem;
        position: relative;
        z-index: 1;
    }

    .fi-panel-admin .fi-simple-main {
        width: 100%;
        max-width: 32rem;
    }

    .fi-panel-admin .fi-simple-page-content {
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 1.75rem;
        background: rgba(8, 17, 31, 0.86);
        backdrop-filter: blur(14px);
        box-shadow: 0 28px 80px rgba(2, 6, 23, 0.45);
        padding: 2rem;
    }

    .fi-panel-admin .fi-simple-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .fi-panel-admin .fi-simple-header .fi-logo {
        margin-inline: auto;
        margin-bottom: 1rem;
        width: auto;
        filter: drop-shadow(0 0 24px rgba(56, 189, 248, 0.16));
    }

    .fi-panel-admin .fi-simple-header-heading {
        color: #e2e8f0;
        font-size: clamp(1.8rem, 3vw, 2.35rem);
        line-height: 1.1;
        letter-spacing: -0.03em;
    }

    .fi-panel-admin .fi-simple-header-subheading {
        color: #94a3b8;
        max-width: 34ch;
        margin-inline: auto;
        line-height: 1.7;
    }

    .fi-panel-admin .fi-fo-field-wrp-label,
    .fi-panel-admin .fi-input-wrp,
    .fi-panel-admin .fi-checkbox label,
    .fi-panel-admin .fi-link {
        color: #cbd5e1;
    }

    .fi-panel-admin .fi-input-wrp {
        border-radius: 1rem;
        background: rgba(15, 23, 42, 0.88);
        border-color: rgba(148, 163, 184, 0.2);
        box-shadow: none;
    }

    .fi-panel-admin .fi-input-wrp:focus-within {
        border-color: rgba(56, 189, 248, 0.55);
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
    }

    .fi-panel-admin .fi-input {
        color: #e2e8f0;
    }

    .fi-panel-admin .fi-input::placeholder {
        color: #64748b;
    }

    .fi-panel-admin .fi-btn.fi-color-primary {
        border-radius: 1rem;
        color: #082f49;
        background: linear-gradient(135deg, #67e8f9 0%, #38bdf8 48%, #0ea5e9 100%);
        box-shadow: 0 18px 40px -24px rgba(56, 189, 248, 0.8);
    }

    .fi-panel-admin .fi-btn.fi-color-primary:hover {
        filter: brightness(1.04);
    }

    .fi-panel-admin .fi-link {
        color: #7dd3fc;
    }

    .fi-panel-admin .fi-link:hover {
        color: #bae6fd;
    }

    .fi-panel-admin .fi-fo-component-ctn {
        gap: 1rem;
    }

    .fi-panel-admin .fi-ac {
        margin-top: 1.25rem;
    }

    @media (max-width: 640px) {
        .fi-panel-admin .fi-simple-main-ctn {
            padding: 1rem;
        }

        .fi-panel-admin .fi-simple-page-content {
            padding: 1.4rem;
            border-radius: 1.4rem;
        }
    }
</style>
