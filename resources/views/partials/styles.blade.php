<link rel="icon" type="image/png" href="/favicon.png" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="/vendor/bootstrap-icons/bootstrap-icons.min.css" />
<link rel="stylesheet" href="/build/assets/app-DhBeqZV0.css" />
<script>
  (function() {
    const saved = localStorage.getItem('smkn1_theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
  })();
</script>
<link rel="stylesheet" href="{{ asset('css/sirani-dashboard.css') }}?v={{ filemtime(public_path('css/sirani-dashboard.css')) }}">
<style>
  /* ── Universal Perfectly Centered & Symmetrical File Input ── */
  input[type="file"],
  input[type="file"].form-control,
  input[type="file"].form-control-sm,
  input[type="file"].input-field {
    display: block !important;
    width: 100% !important;
    height: 40px !important;
    min-height: 40px !important;
    max-height: 40px !important;
    padding: 4px 6px !important;
    font-size: 12.5px !important;
    line-height: 30px !important;
    box-sizing: border-box !important;
    cursor: pointer !important;
    overflow: hidden !important;
    vertical-align: middle !important;
  }
  input[type="file"].form-control-sm {
    height: 34px !important;
    min-height: 34px !important;
    max-height: 34px !important;
    padding: 3px 5px !important;
    line-height: 26px !important;
    font-size: 11.5px !important;
  }
  input[type="file"]::file-selector-button,
  input[type="file"]::-webkit-file-upload-button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 28px !important;
    max-height: 28px !important;
    line-height: 28px !important;
    padding: 0 14px !important;
    margin: 0 12px 0 0 !important;
    background: #0f172a !important;
    color: #ffffff !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    vertical-align: middle !important;
    float: left !important;
  }
  input[type="file"].form-control-sm::file-selector-button,
  input[type="file"].form-control-sm::-webkit-file-upload-button {
    height: 24px !important;
    max-height: 24px !important;
    line-height: 24px !important;
    padding: 0 10px !important;
    margin: 0 8px 0 0 !important;
    font-size: 11px !important;
    border-radius: 5px !important;
  }
  input[type="file"]:hover::file-selector-button,
  input[type="file"]:hover::-webkit-file-upload-button {
    background: #0284c7 !important;
    color: #ffffff !important;
  }

  /* ── Universal Premium & Clean Modal Styling ── */
  .modal-backdrop {
    background-color: rgba(15, 23, 42, 0.65) !important;
    backdrop-filter: blur(4px) !important;
    -webkit-backdrop-filter: blur(4px) !important;
  }

  .modal-content {
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    border-radius: 16px !important;
    overflow: hidden !important;
    box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3), 0 0 0 1px rgba(0, 0, 0, 0.04) !important;
    background-color: #ffffff !important;
  }

  [data-theme="dark"] .modal-content {
    background-color: #0f172a !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    color: #f8fafc !important;
  }

  .modal-header {
    padding: 15px 20px !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.07) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    border-top-left-radius: 15px !important;
    border-top-right-radius: 15px !important;
  }

  [data-theme="dark"] .modal-header {
    border-bottom-color: rgba(255, 255, 255, 0.08) !important;
  }

  .modal-header.bg-primary {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    color: #ffffff !important;
    border-bottom: none !important;
  }

  .modal-header.bg-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #ffffff !important;
    border-bottom: none !important;
  }

  .modal-header.bg-dark {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
    color: #ffffff !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
  }

  .modal-header.bg-warning {
    background: #fef3c7 !important;
    color: #92400e !important;
    border-bottom: 1px solid #fde68a !important;
  }

  .modal-header.bg-light {
    background: #f8fafc !important;
    color: #1e293b !important;
    border-bottom: 1px solid #e2e8f0 !important;
  }

  [data-theme="dark"] .modal-header.bg-light {
    background: #1e293b !important;
    color: #f8fafc !important;
    border-bottom-color: rgba(255, 255, 255, 0.1) !important;
  }

  .modal-header .modal-title {
    font-weight: 800 !important;
    font-size: 15px !important;
    letter-spacing: -0.01em !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin: 0 !important;
  }

  .modal-body {
    padding: 22px 24px !important;
  }

  .modal-footer {
    padding: 14px 20px !important;
    border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
    background: #f8fafc !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 10px !important;
  }

  [data-theme="dark"] .modal-footer {
    background: #0b1120 !important;
    border-top-color: rgba(255, 255, 255, 0.08) !important;
  }

  /* Universal .btn-close (Clean Icon, Never Solid Blobs) */
  .btn-close,
  .modal-header .btn-close,
  .modal-close,
  .btn-close-modal {
    box-sizing: border-box !important;
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    min-height: 32px !important;
    max-width: 32px !important;
    max-height: 32px !important;
    padding: 0 !important;
    margin: 0 0 0 auto !important;
    border-radius: 8px !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    background-color: rgba(0, 0, 0, 0.05) !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23334155'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") !important;
    background-position: center center !important;
    background-size: 11px 11px !important;
    background-repeat: no-repeat !important;
    opacity: 0.8 !important;
    box-shadow: none !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all .15s ease-in-out !important;
    flex-shrink: 0 !important;
  }

  .btn-close:hover,
  .modal-header .btn-close:hover,
  .modal-close:hover,
  .btn-close-modal:hover {
    opacity: 1 !important;
    background-color: rgba(239, 68, 68, 0.12) !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ef4444'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") !important;
    border-color: rgba(239, 68, 68, 0.3) !important;
    transform: scale(1.06);
  }

  /* Close button on dark / colored modal headers */
  .modal-header.bg-primary .btn-close,
  .modal-header.bg-dark .btn-close,
  .modal-header.bg-success .btn-close,
  .modal-header.bg-danger .btn-close,
  .modal-header.text-white .btn-close,
  .btn-close-white {
    background-color: rgba(255, 255, 255, 0.2) !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") !important;
    border-color: rgba(255, 255, 255, 0.25) !important;
    opacity: 0.95 !important;
  }

  .modal-header.bg-primary .btn-close:hover,
  .modal-header.bg-dark .btn-close:hover,
  .modal-header.bg-success .btn-close:hover,
  .modal-header.bg-danger .btn-close:hover,
  .modal-header.text-white .btn-close:hover,
  .btn-close-white:hover {
    background-color: rgba(255, 255, 255, 0.35) !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    opacity: 1 !important;
  }

  .modal-header.bg-warning .btn-close {
    background-color: rgba(0, 0, 0, 0.08) !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2378350f'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e") !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
    opacity: 0.85 !important;
  }

  .modal-header.bg-warning .btn-close:hover {
    background-color: rgba(0, 0, 0, 0.15) !important;
    opacity: 1 !important;
  }

  .btn-close i,
  .btn-close svg {
    display: none !important;
  }

  /* Custom Modals (.modal-overlay, .modal-card, .modal-container) */
  .modal-overlay {
    position: fixed !important;
    inset: 0 !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: rgba(15, 23, 42, 0.65) !important;
    backdrop-filter: blur(4px) !important;
    -webkit-backdrop-filter: blur(4px) !important;
    z-index: 9999 !important;
    padding: 16px !important;
    box-sizing: border-box !important;
  }

  .modal-overlay.active,
  .modal-overlay.open {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }

  .modal-card,
  .modal-container {
    background-color: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    border-radius: 16px !important;
    box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.35) !important;
    color: #0f172a !important;
    box-sizing: border-box !important;
  }

  [data-theme="dark"] .modal-card,
  [data-theme="dark"] .modal-container {
    background-color: #0f172a !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    color: #f8fafc !important;
  }
</style>
