/**
 * Finez SEO - Luxury Loader Controller & Global Progress Suite
 * Handles:
 * 1. Full Page Branded Preloader with smooth dismiss & safety timeout
 * 2. Top Nano Progress Bar for AJAX calls & page transitions
 * 3. Table & Container Skeleton/Spinner helpers
 * 4. Button Loading States with double-submit prevention
 */

(function (window, document) {
    'use strict';

    var FinezLoader = {
        _topTimer: null,
        _topProgress: 0,
        _ajaxCount: 0,
        _isDismissed: false,

        // =====================================================================
        // 1. Full Page Preloader
        // =====================================================================
        dismissPreloader: function () {
            if (FinezLoader._isDismissed) return;
            FinezLoader._isDismissed = true;

            var loader = document.getElementById('finez-page-loader');
            if (loader) {
                loader.classList.add('loaded');
                setTimeout(function () {
                    if (loader && loader.parentNode) {
                        loader.style.display = 'none';
                    }
                }, 400);
            }
        },

        initPreloader: function () {
            // Dismiss smoothly when page assets finish loading
            if (document.readyState === 'complete') {
                setTimeout(FinezLoader.dismissPreloader, 180);
            } else {
                window.addEventListener('load', function () {
                    setTimeout(FinezLoader.dismissPreloader, 180);
                });
            }

            // Safety timeout: ensure loader never hangs if external assets stall
            setTimeout(FinezLoader.dismissPreloader, 850);

            // Handle browser back/forward cache (bfcache)
            window.addEventListener('pageshow', function () {
                FinezLoader.dismissPreloader();
                FinezLoader.top.done();
            });
        },

        // =====================================================================
        // 2. Top Nano Progress Bar (YouTube / GitHub / Vercel style)
        // =====================================================================
        top: {
            getEl: function () {
                var el = document.getElementById('finez-top-loader');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'finez-top-loader';
                    document.body.appendChild(el);
                }
                return el;
            },

            start: function () {
                var el = FinezLoader.top.getEl();
                clearInterval(FinezLoader._topTimer);
                FinezLoader._topProgress = 15;
                el.style.opacity = '1';
                el.style.width = '15%';
                el.classList.add('active');

                FinezLoader._topTimer = setInterval(function () {
                    if (FinezLoader._topProgress < 50) {
                        FinezLoader._topProgress += Math.random() * 12 + 4;
                    } else if (FinezLoader._topProgress < 85) {
                        FinezLoader._topProgress += Math.random() * 5 + 1.5;
                    } else if (FinezLoader._topProgress < 94) {
                        FinezLoader._topProgress += 0.5;
                    }
                    if (FinezLoader._topProgress > 94) FinezLoader._topProgress = 94;
                    el.style.width = FinezLoader._topProgress + '%';
                }, 200);
            },

            set: function (pct) {
                var el = FinezLoader.top.getEl();
                FinezLoader._topProgress = Math.min(100, Math.max(0, pct));
                el.style.width = FinezLoader._topProgress + '%';
            },

            done: function () {
                var el = FinezLoader.top.getEl();
                clearInterval(FinezLoader._topTimer);
                FinezLoader._topProgress = 100;
                el.style.width = '100%';

                setTimeout(function () {
                    el.style.opacity = '0';
                    setTimeout(function () {
                        el.classList.remove('active');
                        el.style.width = '0%';
                        FinezLoader._topProgress = 0;
                    }, 300);
                }, 180);
            }
        },

        // =====================================================================
        // 3. Card & Table Content Loaders
        // =====================================================================
        cardLoader: function (targetSelector, message) {
            var msg = message || 'Loading records...';
            var html = '<div class="finez-table-loader-wrap">' +
                '<div class="finez-spinner-pulse"></div>' +
                '<div class="finez-table-loader-text">' + msg + '</div>' +
                '</div>';
            if (window.jQuery && $(targetSelector).length) {
                $(targetSelector).html(html);
            } else {
                var target = document.querySelector(targetSelector);
                if (target) target.innerHTML = html;
            }
        },

        tableLoader: function (tableBodySelector, message, colSpan) {
            var cols = colSpan || 10;
            var msg = message || 'Loading records...';
            var html = '<tr><td colspan="' + cols + '" style="padding: 0; border: none;">' +
                '<div class="finez-table-loader-wrap">' +
                '<div class="finez-spinner-pulse"></div>' +
                '<div class="finez-table-loader-text">' + msg + '</div>' +
                '</div>' +
                '</td></tr>';
            if (window.jQuery && $(tableBodySelector).length) {
                $(tableBodySelector).html(html);
            } else {
                var target = document.querySelector(tableBodySelector);
                if (target) target.innerHTML = html;
            }
        },

        // =====================================================================
        // 4. Button Loading States
        // =====================================================================
        btnStart: function (btnSelector, loadingText) {
            var $btn = window.jQuery ? $(btnSelector) : null;
            if (!$btn || !$btn.length) return;

            if (!$btn.data('orig-html')) {
                $btn.data('orig-html', $btn.html());
            }

            var text = loadingText || 'Processing...';
            $btn.addClass('is-loading').prop('disabled', true);
            $btn.html('<span class="btn-spinner"></span> ' + text);
        },

        btnStop: function (btnSelector) {
            var $btn = window.jQuery ? $(btnSelector) : null;
            if (!$btn || !$btn.length) return;

            var orig = $btn.data('orig-html');
            if (orig) {
                $btn.html(orig);
            }
            $btn.removeClass('is-loading').prop('disabled', false);
        },

        // =====================================================================
        // 5. Live Sync Floating Pill
        // =====================================================================
        showSyncPill: function (message) {
            var msg = message || 'Syncing data...';
            var pill = document.getElementById('finez-sync-pill');
            if (!pill) {
                pill = document.createElement('div');
                pill.id = 'finez-sync-pill';
                pill.className = 'finez-sync-pill';
                document.body.appendChild(pill);
            }
            pill.innerHTML = '<span class="sync-spinner"></span> <span>' + msg + '</span>';
            pill.classList.add('show');
        },

        hideSyncPill: function () {
            var pill = document.getElementById('finez-sync-pill');
            if (pill) {
                pill.classList.remove('show');
            }
        }
    };

    // Initialize preloader immediately
    FinezLoader.initPreloader();

    // Hook into jQuery AJAX & DOM when ready
    function setupDOMHooks() {
        if (window.jQuery) {
            var $ = window.jQuery;

            // Global Top Loader AJAX Hooks
            $(document).ajaxStart(function () {
                FinezLoader._ajaxCount++;
                FinezLoader.top.start();
            });

            $(document).ajaxStop(function () {
                FinezLoader._ajaxCount = 0;
                FinezLoader.top.done();
            });

            $(document).ajaxError(function () {
                FinezLoader._ajaxCount = 0;
                FinezLoader.top.done();
            });

            // Smooth top-progress when navigating via links
            $(document).on('click', '#side-menu a, .falcon-topbar a:not([href="#"]):not([href^="javascript"]):not([target="_blank"])', function (e) {
                var href = $(this).attr('href');
                if (href && href !== '#' && href.indexOf('javascript') === -1) {
                    FinezLoader.top.start();
                }
            });

            // Form Submit Button Loader
            $(document).on('submit', 'form:not(.no-loader)', function () {
                var submitBtn = $(this).find('button[type="submit"]:not(.no-loader), input[type="submit"]:not(.no-loader)');
                if (submitBtn.length && !submitBtn.hasClass('is-loading')) {
                    FinezLoader.btnStart(submitBtn, 'Please wait...');
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupDOMHooks);
    } else {
        setupDOMHooks();
    }

    // Expose globally
    window.FinezLoader = FinezLoader;

})(window, document);
