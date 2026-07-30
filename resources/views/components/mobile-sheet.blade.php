@props([
    'id' => 'mobileSheet',
    'title' => 'Actions',
    'icon' => 'fa-solid fa-layer-group'
])

<div class="mobile-sheet-overlay d-md-none" id="{{ $id }}Overlay" onclick="closeMobileSheet('{{ $id }}')"></div>
<div class="mobile-sheet d-md-none" id="{{ $id }}" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}Title">
    <div class="mobile-sheet-handle-bar" onclick="closeMobileSheet('{{ $id }}')">
        <div class="sheet-handle"></div>
    </div>
    <div class="mobile-sheet-header">
        <div class="d-flex align-items-center gap-2">
            <i class="{{ $icon }} text-success"></i>
            <h5 class="mobile-sheet-title mb-0" id="{{ $id }}Title">{{ $title }}</h5>
        </div>
        <button type="button" class="btn-close-sheet" onclick="closeMobileSheet('{{ $id }}')" aria-label="Close sheet">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="mobile-sheet-body">
        {{ $slot }}
    </div>
</div>

<style>
    .mobile-sheet-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(3px);
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    .mobile-sheet-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .mobile-sheet {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        z-index: 1060;
        max-height: 85vh;
        display: flex;
        flex-direction: column;
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 -8px 30px rgba(0,0,0,0.18);
        padding-bottom: max(16px, env(safe-area-inset-bottom));
    }

    [data-theme="dark"] .mobile-sheet {
        background: #111827;
        color: #f1f5f9;
        box-shadow: 0 -8px 30px rgba(0,0,0,0.5);
    }

    .mobile-sheet.show {
        transform: translateY(0);
    }

    .mobile-sheet-handle-bar {
        width: 100%;
        padding: 10px 0 6px;
        display: flex;
        justify-content: center;
        cursor: pointer;
    }

    .sheet-handle {
        width: 36px;
        height: 5px;
        border-radius: 10px;
        background: #cbd5e1;
    }

    [data-theme="dark"] .sheet-handle {
        background: #475569;
    }

    .mobile-sheet-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 20px 14px;
        border-bottom: 1px solid var(--border-color, #edebe9);
    }

    .mobile-sheet-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1rem;
    }

    .btn-close-sheet {
        background: rgba(0,0,0,0.05);
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        cursor: pointer;
    }

    [data-theme="dark"] .btn-close-sheet {
        background: rgba(255,255,255,0.1);
        color: #94a3b8;
    }

    .mobile-sheet-body {
        padding: 20px;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>

<script>
    function openMobileSheet(sheetId) {
        document.getElementById(sheetId)?.classList.add('show');
        document.getElementById(sheetId + 'Overlay')?.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSheet(sheetId) {
        document.getElementById(sheetId)?.classList.remove('show');
        document.getElementById(sheetId + 'Overlay')?.classList.remove('show');
        document.body.style.overflow = '';
    }
</script>
