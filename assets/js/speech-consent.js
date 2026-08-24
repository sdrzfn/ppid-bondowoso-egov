// speech-consent.js
(function () {
    'use strict';

    const CONSENT_KEY = 'ppid_speech_consent';

    // Cek apakah user sudah memberikan consent
    function hasConsent() {
        try {
            return localStorage.getItem(CONSENT_KEY) === 'allowed';
        } catch (e) {
            return false;
        }
    }

    // Simpan consent
    function saveConsent(value) {
        try {
            localStorage.setItem(CONSENT_KEY, value);
        } catch (e) {
            // localStorage tidak tersedia
        }
    }

    // Inisialisasi speech.js jika diizinkan
    function initSpeech() {
        if (!hasConsent()) {
            return;
        }

        // Load dan inisialisasi speech.js
        const script = document.createElement('script');
        script.src = 'assets/js/speech.js';
        script.async = true;
        document.head.appendChild(script);
    }

    // Tampilkan modal consent
    function showConsentModal() {
        // Cek apakah modal sudah ada
        if (document.getElementById('speechConsentModal')) {
            return;
        }

        const modal = document.createElement('div');
        modal.id = 'speechConsentModal';
        modal.className = 'fixed bottom-4 left-4 z-50 max-w-sm';
        modal.innerHTML = `
            <div class="fixed bottom-5 right-5 z-50 max-w-sm bg-white rounded-lg shadow-2xl border border-slate-200 p-5">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">Fitur Text-to-Speech</h3>
                        <p class="text-xs text-gray-600 mb-3">
                            Website ini menggunakan teknologi text-to-speech untuk membaca teks secara otomatis saat Anda mengarahkan kursor. Apakah Anda mengizinkan fitur ini?
                        </p>
                        <div class="flex gap-2">
                            <button id="speechAllow" class="flex-1 px-3 py-1.5 bg-sky-600 text-white text-xs font-medium rounded hover:bg-sky-700 transition-colors">
                                Izinkan
                            </button>
                            <button id="speechDeny" class="flex-1 px-3 py-1.5 bg-slate-200 text-gray-700 text-xs font-medium rounded hover:bg-slate-300 transition-colors">
                                Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Event listeners
        document.getElementById('speechAllow').addEventListener('click', function () {
            saveConsent('allowed');
            modal.remove();
            initSpeech();
        });

        document.getElementById('speechDeny').addEventListener('click', function () {
            saveConsent('denied');
            modal.remove();
        });
    }

    // Tampilkan modal jika belum ada consent
    if (!hasConsent()) {
        // Tunggu DOM siap
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', showConsentModal);
        } else {
            showConsentModal();
        }
    } else if (hasConsent()) {
        // Langsung inisialisasi jika sudah diizinkan
        initSpeech();
    }
})();