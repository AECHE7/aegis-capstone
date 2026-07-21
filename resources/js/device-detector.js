/**
 * AEGIS Adaptive Device & Viewport Detector Engine (AegisDeviceDetector)
 * Dynamically identifies screen dimensions, device types (Mobile, Tablet, Desktop, Touch),
 * orientation, and accessibility states, injecting live HTML attributes for CSS & JS components.
 */
(function() {
    'use strict';

    let focusTimeout = null;
    let isInputFocused = false;

    function getViewportCategory(width) {
        if (width < 576) return 'xs';
        if (width < 768) return 'sm';
        if (width < 992) return 'md';
        if (width < 1200) return 'lg';
        return 'xl';
    }

    function detectDevice() {
        const width = window.innerWidth;
        const height = window.innerHeight;
        const isTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
        const orientation = width > height ? 'landscape' : 'portrait';
        const category = getViewportCategory(width);

        let device = 'desktop';
        if (width < 768) {
            device = 'mobile';
        } else if (width < 992 && isTouch) {
            device = 'tablet';
        }

        const html = document.documentElement;
        html.setAttribute('data-device', device);
        html.setAttribute('data-touch', isTouch ? 'true' : 'false');
        html.setAttribute('data-viewport', category);
        html.setAttribute('data-orientation', orientation);

        window.AegisDeviceState = {
            device,
            isTouch,
            viewport: category,
            orientation,
            width,
            height,
            isInputFocused
        };
    }

    // Debounced resize & orientation listener using requestAnimationFrame
    let rAFPending = false;
    function updateOnResize() {
        if (!rAFPending) {
            rAFPending = true;
            window.requestAnimationFrame(() => {
                detectDevice();
                rAFPending = false;
            });
        }
    }

    // Virtual Keyboard / Focus Handler for Sticky Action Bars
    function setupKeyboardFocusGuard() {
        const updateKeyboardState = (focused) => {
            isInputFocused = focused;
            if (window.AegisDeviceState) {
                window.AegisDeviceState.isInputFocused = focused;
            }
            if (focused) {
                document.body.classList.add('keyboard-open');
            } else {
                document.body.classList.remove('keyboard-open');
            }
        };

        // Modern VisualViewport API Listener
        if (window.visualViewport) {
            let initialHeight = window.visualViewport.height;
            window.visualViewport.addEventListener('resize', () => {
                const currentHeight = window.visualViewport.height;
                const isKeyboardVisible = (initialHeight - currentHeight) > 150;
                updateKeyboardState(isKeyboardVisible);
            });
        }

        // Fallback focusin / focusout Event Listener for older mobile webviews
        document.addEventListener('focusin', (e) => {
            const target = e.target;
            if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT')) {
                if (focusTimeout) clearTimeout(focusTimeout);
                updateKeyboardState(true);
            }
        });

        document.addEventListener('focusout', (e) => {
            const target = e.target;
            if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT')) {
                if (focusTimeout) clearTimeout(focusTimeout);
                // 200ms debounce to prevent flicker during rapid input tabbing
                focusTimeout = setTimeout(() => {
                    updateKeyboardState(false);
                }, 200);
            }
        });

        // Touchstart Override for Sticky Action Buttons: Execute immediately on first touch tap
        document.addEventListener('touchstart', (e) => {
            const stickyBtn = e.target.closest('.sticky-action-bar button, .sticky-action-bar a, [data-sticky-override="true"]');
            if (stickyBtn) {
                if (focusTimeout) clearTimeout(focusTimeout);
                updateKeyboardState(false);
            }
        }, { passive: true });
    }

    document.addEventListener('DOMContentLoaded', () => {
        detectDevice();
        setupKeyboardFocusGuard();
        window.addEventListener('resize', updateOnResize);
        window.addEventListener('orientationchange', updateOnResize);
    });

    // Run initial detection immediately before DOMContentLoaded to set html attributes early
    detectDevice();
})();
