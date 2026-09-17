@props(['autoShow' => true])

<!-- ── NPC SEAL OF REGISTRATION MODAL (R.A. 10173 Compliance) ── -->
<div id="corSealModal" class="cor-seal-overlay d-none" style="display: none; opacity: 0;" role="dialog" aria-modal="true" aria-labelledby="corSealTitle">
    <div class="cor-seal-content" id="corSealModalContent">

        <!-- Modal Header -->
        <div class="cor-seal-header">
            <div class="cor-seal-header-left">
                <div class="cor-seal-icon-box">
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="cor-seal-title" id="corSealTitle">SEAL OF REGISTRATION</h3>
                    <p class="cor-seal-subtitle">CLSU Official Data</p>
                </div>
            </div>
            <button type="button" onclick="closeCorSealModal()" class="cor-seal-close" aria-label="Close Seal of Registration Modal">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="cor-seal-body">
            <div class="cor-seal-image-wrap">
                <img src="{{ asset('images/CORSeal.jpg') }}" 
                     alt="National Privacy Commission DPO/DPS Seal of Registration - Central Luzon State University" 
                     class="cor-seal-img" 
                     loading="eager"
                     width="320"
                     height="453">
            </div>

            <div class="cor-seal-info">
                <p>This seal authenticates Certificate of Registration from CLSU.</p>
            </div>

            <div class="cor-seal-actions">
                <button type="button" onclick="closeCorSealModal()" class="cor-seal-btn">
                    I Understand
                </button>
            </div>
        </div>

    </div>
</div>

<style>
    /* Overlay Backdrop */
    .cor-seal-overlay {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        overflow-y: auto;
        z-index: 9999999;
        background-color: rgba(0, 0, 0, 0.72);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    /* Modal Dialog Container */
    .cor-seal-content {
        max-width: 440px;
        width: 100%;
        overflow: hidden;
        border-radius: 14px;
        transform: scale(0.92);
        opacity: 0;
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.1);
        background-color: #ffffff;
        margin: auto;
    }

    .cor-seal-content.cor-scale-in {
        transform: scale(1);
        opacity: 1;
    }

    .cor-seal-content.cor-scale-out {
        transform: scale(0.92);
        opacity: 0;
    }

    /* Modal Header */
    .cor-seal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        background: linear-gradient(135deg, #0C4E2D 0%, #00754A 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }

    .cor-seal-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cor-seal-icon-box {
        width: 32px;
        height: 32px;
        min-width: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cor-seal-icon-box svg {
        width: 24px;
        height: 24px;
        color: #fde047;
        fill: #fde047;
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
    }

    .cor-seal-title {
        font-size: 15.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #ffffff;
        margin: 0;
        line-height: 1.25;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .cor-seal-subtitle {
        color: #d1fae5;
        font-size: 11.5px;
        margin: 0;
        line-height: 1.25;
        font-weight: 500;
        letter-spacing: 0.2px;
    }

    /* Close Button */
    .cor-seal-close {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.75);
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.18s ease, background 0.18s ease;
    }

    .cor-seal-close:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
    }

    .cor-seal-close svg {
        width: 22px;
        height: 22px;
    }

    /* Modal Body */
    .cor-seal-body {
        background-color: #ffffff;
    }

    .cor-seal-image-wrap {
        padding: 20px 16px 12px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #ffffff;
    }

    .cor-seal-img {
        width: 100%;
        max-width: 310px;
        height: auto;
        max-height: 52vh;
        object-fit: contain;
        display: block;
        filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.08));
    }

    .cor-seal-info {
        padding: 8px 24px;
        text-align: center;
    }

    .cor-seal-info p {
        color: #4b5563;
        font-size: 13.5px;
        line-height: 1.45;
        margin: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .cor-seal-actions {
        padding: 14px 20px 22px;
    }

    .cor-seal-btn {
        width: 100%;
        padding: 12px 18px;
        background-color: #16a34a;
        color: #ffffff;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.3px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .cor-seal-btn:hover {
        background-color: #15803d;
        box-shadow: 0 6px 18px rgba(21, 128, 61, 0.45);
        transform: translateY(-1px);
    }

    .cor-seal-btn:active {
        transform: translateY(0) scale(0.98);
    }
</style>

<script>
    (function () {
        window.closeCorSealModal = function () {
            const modal = document.getElementById('corSealModal');
            const modalContent = document.getElementById('corSealModalContent');
            if (!modal) return;
            
            if (modalContent) {
                modalContent.classList.remove('cor-scale-in');
                modalContent.classList.add('cor-scale-out');
            }
            modal.style.opacity = '0';
            
            setTimeout(function () {
                modal.classList.add('d-none');
                modal.style.display = 'none';
            }, 280);
        };

        window.showCorSealModal = function () {
            const modal = document.getElementById('corSealModal');
            const modalContent = document.getElementById('corSealModalContent');
            if (!modal) return;

            modal.classList.remove('d-none');
            modal.style.display = 'flex';
            modal.style.opacity = '0';

            if (modalContent) {
                modalContent.classList.remove('cor-scale-out');
            }

            requestAnimationFrame(function () {
                modal.style.opacity = '1';
                if (modalContent) {
                    modalContent.classList.add('cor-scale-in');
                }
            });
        };

        // Backdrop click to dismiss
        document.addEventListener('click', function (event) {
            const modal = document.getElementById('corSealModal');
            if (modal && event.target === modal) {
                closeCorSealModal();
            }
        });

        // ESC key to dismiss
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' || event.key === 'Esc') {
                const modal = document.getElementById('corSealModal');
                if (modal && modal.style.display !== 'none' && !modal.classList.contains('d-none')) {
                    closeCorSealModal();
                }
            }
        });

        @if($autoShow)
        // Automatically trigger modal display on page load after 500ms (matching CLSU Admissions)
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                showCorSealModal();
            }, 500);
        });
        @endif
    })();
</script>
