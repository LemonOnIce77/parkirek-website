@extends('layouts.admin')

@section('title', 'Scan Gate')

@section('content')
    <div class="min-h-[calc(100vh-8rem)] flex flex-col lg:flex-row gap-4 lg:gap-6">

        {{-- LEFT: CAMERA --}}
        <div
            class="flex-1 bg-black rounded-2xl relative overflow-hidden shadow-lg flex flex-col justify-center items-center group
                   h-[60vh] sm:h-[65vh] lg:h-auto min-h-[360px]">

            {{-- MODE SWITCH --}}
            <div
                class="absolute top-3 left-3 sm:top-6 sm:left-6 flex bg-black/60 backdrop-blur-md rounded-lg p-1 border border-white/10 z-30">
                <button type="button" onclick="setGateMode('entry')" id="btnEntry"
                    class="px-3 sm:px-6 py-2 rounded-md text-[11px] sm:text-xs font-bold uppercase tracking-wider transition-all bg-brand-600 text-white shadow-lg">
                    IN (Masuk)
                </button>
                <button type="button" onclick="setGateMode('exit')" id="btnExit"
                    class="px-3 sm:px-6 py-2 rounded-md text-[11px] sm:text-xs font-bold uppercase tracking-wider transition-all text-white/60 hover:text-white">
                    OUT (Keluar)
                </button>
            </div>

            {{-- STATUS --}}
            <div
                class="absolute top-3 right-3 sm:top-6 sm:right-6 bg-black/60 backdrop-blur-md px-3 sm:px-4 py-2 rounded-full border border-white/10 flex items-center gap-2 sm:gap-3 z-30">
                <span id="gateLabel"
                    class="text-white/90 text-[11px] sm:text-xs font-medium uppercase tracking-wider truncate max-w-[160px] sm:max-w-none">
                    Gate A (Entry)
                </span>
                <span id="gateStatus"
                    class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse shadow-[0_0_10px_#22c55e]"></span>
            </div>

            {{-- CAMERA CONTAINER --}}
            <div class="absolute inset-0 z-0">
                <div id="qrReader" class="w-full h-full"></div>

                {{-- fallback overlay (muncul kalau auto-start gagal / permission belum dikasih) --}}
                <div id="cameraOverlay"
                    class="absolute inset-0 bg-gray-900/40 flex flex-col items-center justify-center text-gray-200 px-6 text-center">
                    <i data-lucide="camera" class="w-14 h-14 sm:w-16 sm:h-16 mb-4 opacity-70"></i>
                    <p class="text-sm font-medium tracking-wide">CAMERA READY</p>
                    <p class="text-xs opacity-80 mt-1">Tap to allow camera & start scanning</p>
                    <button type="button" onclick="startScanner()"
                        class="mt-4 px-4 py-2 text-xs font-semibold rounded-lg bg-white/10 border border-white/20 hover:bg-white/20 transition">
                        Start Camera
                    </button>
                </div>
            </div>

            {{-- SCAN FRAME --}}
            <div class="absolute inset-0 border-2 border-white/20 z-10 m-4 sm:m-8 rounded-xl pointer-events-none overflow-hidden">
                <div
                    class="absolute top-0 left-0 w-full h-0.5 bg-brand-500 shadow-[0_0_30px_rgba(220,38,38,1)] animate-scan">
                </div>
                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-brand-500 rounded-tl-lg"></div>
                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-brand-500 rounded-tr-lg"></div>
                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-brand-500 rounded-bl-lg"></div>
                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-brand-500 rounded-br-lg"></div>
            </div>

            {{-- ACTION BUTTONS (Desktop only untuk Start/Stop) --}}
            <div class="absolute bottom-3 sm:bottom-8 left-0 right-0 flex justify-center z-20 px-3 sm:px-4">
                <div class="hidden sm:flex flex-wrap justify-center gap-3">
                    <button type="button" onclick="startScanner()"
                        class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-full font-medium shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2 backdrop-blur-sm border border-white/20">
                        <i data-lucide="play" class="w-5 h-5"></i> Start
                    </button>

                    <button type="button" onclick="stopScanner()"
                        class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-full font-medium shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2 backdrop-blur-sm border border-white/20">
                        <i data-lucide="square" class="w-5 h-5"></i> Stop
                    </button>

                    {{-- Kalau mau simulate (opsional, idealnya local saja) --}}
                    {{-- @if(app()->environment('local'))
                        <button type="button" onclick="simulateScan('valid')"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-full font-medium shadow-lg transition-transform active:scale-95 flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i> Simulate Valid
                        </button>
                        <button type="button" onclick="simulateScan('invalid')"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-full font-medium shadow-lg transition-transform active:scale-95 flex items-center gap-2">
                            <i data-lucide="x-circle" class="w-5 h-5"></i> Simulate Error
                        </button>
                    @endif --}}
                </div>
            </div>
        </div>

        {{-- RIGHT: LIVE LOG --}}
        <div
            class="w-full lg:w-96 bg-white border border-gray-200 rounded-2xl shadow-sm flex flex-col overflow-hidden
                   h-[42vh] sm:h-[45vh] lg:h-auto min-h-[280px]">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-brand-600"></i> Live Log
                </h3>
                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border border-gray-200" id="scanCount">0 Scans</span>
            </div>

            <div id="scanLog" class="flex-1 overflow-y-auto p-4 space-y-3 relative">
                <div id="emptyLogState" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <i data-lucide="qr-code" class="w-12 h-12 mb-3 opacity-20"></i>
                    <p class="text-sm">Ready to scan...</p>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50">
                <form onsubmit="handleManualInput(event)" class="relative">
                    <input type="text" id="manualInput" placeholder="Manual QR URL or Token..."
                        class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none text-sm transition-all shadow-sm">
                    <button type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition-colors">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- RESULT MODAL --}}
    <div id="resultModal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="closeResultModal()"></div>

        <div class="bg-white w-[420px] max-w-[95%] rounded-2xl shadow-2xl z-10 overflow-hidden transform transition-all scale-95 opacity-0"
            id="resultContent">

            <div id="resHeader"
                class="bg-green-500 h-28 sm:h-32 flex items-center justify-center relative overflow-hidden transition-colors duration-300">
                <div class="absolute inset-0 bg-white/10 pattern-dots"></div>
                <div class="bg-white/20 p-4 rounded-full backdrop-blur-sm relative z-10 shadow-lg">
                    <i id="resIcon" data-lucide="check" class="w-10 h-10 sm:w-12 sm:h-12 text-white stroke-[3]"></i>
                </div>
            </div>

            <div class="p-5 sm:p-6 text-center">
                <h2 id="resTitle" class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">Access Granted</h2>
                <p id="resMsg" class="text-sm text-gray-500 mb-5 sm:mb-6">Gate is opening...</p>

                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-left space-y-3 mb-5 sm:mb-6">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Mode</span>
                        <span id="resMode"
                            class="text-xs font-bold text-white bg-green-600 px-2 py-0.5 rounded uppercase">ENTRY</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-xs text-gray-500 uppercase shrink-0">Plate Number</span>
                        <span id="resPlate" class="text-sm font-bold text-gray-900 font-mono truncate">-</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-xs text-gray-500 uppercase shrink-0">Owner</span>
                        <span id="resOwner" class="text-sm font-medium text-gray-900 truncate">-</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-xs text-gray-500 uppercase shrink-0">Timestamp</span>
                        <span id="resTime" class="text-sm font-medium text-gray-900 truncate">-</span>
                    </div>
                </div>

                <button type="button" onclick="closeResultModal()"
                    class="w-full py-3 bg-gray-900 text-white rounded-xl font-medium hover:bg-black transition-colors shadow-lg active:scale-95 transform">
                    Scan Next Vehicle
                </button>
            </div>
        </div>
    </div>

    <audio id="scanSoundSuccess" src="https://assets.mixkit.co/active_storage/sfx/2578/2578-preview.mp3"></audio>
    <audio id="scanSoundError" src="https://assets.mixkit.co/active_storage/sfx/2572/2572-preview.mp3"></audio>
@endsection

@push('scripts')
    <style>
        @keyframes scan-move {
            0%, 100% { top: 0; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan {
            animation: scan-move 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        .pattern-dots {
            background-image: radial-gradient(rgba(255, 255, 255, 0.2) 1px, transparent 1px);
            background-size: 10px 10px;
        }
        #qrReader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
    </style>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        let scanCount = 0;
        let currentMode = 'entry';

        let html5Qr = null;
        let isScanning = false;
        let lastScanned = '';
        let lastScannedAt = 0;

        function setGateMode(mode) {
            currentMode = mode;

            const btnEntry = document.getElementById('btnEntry');
            const btnExit = document.getElementById('btnExit');
            const gateLabel = document.getElementById('gateLabel');
            const scanLine = document.querySelector('.animate-scan');

            const activeClass =
                "px-3 sm:px-6 py-2 rounded-md text-[11px] sm:text-xs font-bold uppercase tracking-wider transition-all text-white shadow-lg transform scale-105";
            const inactiveClass =
                "px-3 sm:px-6 py-2 rounded-md text-[11px] sm:text-xs font-bold uppercase tracking-wider transition-all text-white/60 hover:text-white";

            if (mode === 'entry') {
                btnEntry.className = activeClass + " bg-brand-600";
                btnExit.className = inactiveClass;
                gateLabel.innerText = "Gate A (Entry)";
                scanLine.classList.remove('bg-orange-500');
                scanLine.classList.add('bg-brand-500');
            } else {
                btnEntry.className = inactiveClass;
                btnExit.className = activeClass + " bg-orange-600";
                gateLabel.innerText = "Gate A (Exit)";
                scanLine.classList.remove('bg-brand-500');
                scanLine.classList.add('bg-orange-500');
            }
        }

        function getCsrf() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function extractTokenFromQrText(text) {
            try {
                if (!text) return null;
                if (!text.includes('/') && !text.includes('http')) return text.trim();

                const url = new URL(text.trim(), window.location.origin);
                const parts = url.pathname.split('/').filter(Boolean);
                if (parts.length >= 2 && parts[0] === 'v') return parts[1];
                return parts[parts.length - 1] || null;
            } catch (e) {
                return (text || '').trim() || null;
            }
        }

        async function sendScanToServer(rawQrText) {
            const token = extractTokenFromQrText(rawQrText);
            if (!token) {
                showResult({ ok: false, message: 'QR tidak terbaca / token kosong.', mode: currentMode });
                addLogItem("Invalid QR", new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }), false, currentMode);
                return;
            }

            const endpoint = currentMode === 'entry'
                ? "{{ route('admin.scan.masuk') }}"
                : "{{ route('admin.scan.keluar') }}";

            const payload = { qr_token: token, action: currentMode === 'entry' ? 'masuk' : 'keluar' };

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrf(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    showResult({
                        ok: false,
                        message: data.message || 'Scan gagal (server error).',
                        mode: currentMode,
                        plate: data.plate || 'Unknown',
                        owner: data.owner || '-',
                        timestamp: data.timestamp || new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
                    });
                    addLogItem(data.plate || "Unknown / Error", data.timestamp || new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }), false, currentMode);
                    return;
                }

                const ok = !!data.ok;

                showResult({
                    ok,
                    message: data.message || (ok ? 'Gate opening...' : 'Access denied.'),
                    mode: currentMode,
                    plate: data.plate || '-',
                    owner: data.owner || '-',
                    timestamp: data.timestamp || new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
                });

                addLogItem(data.plate || 'Unknown', data.timestamp || new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }), ok, currentMode);
            } catch (err) {
                showResult({
                    ok: false,
                    message: 'Network error: tidak bisa konek ke server.',
                    mode: currentMode,
                    plate: 'Unknown',
                    owner: '-',
                    timestamp: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
                });
                addLogItem("Network Error", new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }), false, currentMode);
            }
        }

        async function startScanner() {
            if (isScanning) return;

            const overlay = document.getElementById('cameraOverlay');
            if (overlay) overlay.style.display = 'none';

            if (!html5Qr) html5Qr = new Html5Qrcode("qrReader");

            try {
                isScanning = true;

                const isMobile = window.matchMedia("(max-width: 640px)").matches;
                const boxSize = isMobile ? 220 : 260;

                const config = {
                    fps: 10,
                    qrbox: { width: boxSize, height: boxSize }
                };

                await html5Qr.start(
                    { facingMode: "environment" },
                    config,
                    async (decodedText) => {
                        const now = Date.now();
                        if (decodedText === lastScanned && (now - lastScannedAt) < 2000) return;
                        lastScanned = decodedText;
                        lastScannedAt = now;

                        await sendScanToServer(decodedText);
                    },
                    () => {}
                );
            } catch (e) {
                isScanning = false;
                if (overlay) overlay.style.display = 'flex';
                showResult({ ok: false, message: 'Tidak bisa akses kamera. Cek permission browser.', mode: currentMode });
            }

            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        async function stopScanner() {
            if (!html5Qr || !isScanning) return;

            try {
                await html5Qr.stop();
                await html5Qr.clear();
            } catch (e) {}

            isScanning = false;

            const overlay = document.getElementById('cameraOverlay');
            if (overlay) overlay.style.display = 'flex';
        }

        function showResult({ ok, message, mode, plate, owner, timestamp }) {
            const modal = document.getElementById('resultModal');
            const content = document.getElementById('resultContent');
            const header = document.getElementById('resHeader');

            const time = timestamp || new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            header.className = "h-28 sm:h-32 flex items-center justify-center relative overflow-hidden transition-colors duration-300";

            const icon = document.getElementById('resIcon');
            const title = document.getElementById('resTitle');
            const msg = document.getElementById('resMsg');

            const modeBadge = document.getElementById('resMode');
            const resPlate = document.getElementById('resPlate');
            const resOwner = document.getElementById('resOwner');
            const resTime = document.getElementById('resTime');

            if (ok) {
                header.classList.add('bg-green-500');
                title.innerText = mode === 'entry' ? "Welcome!" : "Goodbye!";
                msg.innerText = message || (mode === 'entry' ? "Gate opening for entry..." : "Gate opening for exit...");

                if (icon) icon.setAttribute('data-lucide', 'check');

                const sound = document.getElementById('scanSoundSuccess');
                if (sound) { sound.currentTime = 0; sound.play().catch(() => {}); }

                modeBadge.innerText = mode === 'entry' ? 'ENTRY' : 'EXIT';
                modeBadge.className = mode === 'entry'
                    ? "text-xs font-bold text-white bg-brand-600 px-2 py-0.5 rounded uppercase"
                    : "text-xs font-bold text-white bg-orange-500 px-2 py-0.5 rounded uppercase";
            } else {
                header.classList.add('bg-red-500');
                title.innerText = "Access Denied";
                msg.innerText = message || "Invalid ticket / not allowed.";

                if (icon) icon.setAttribute('data-lucide', 'x');

                const sound = document.getElementById('scanSoundError');
                if (sound) { sound.currentTime = 0; sound.play().catch(() => {}); }

                modeBadge.innerText = "ERROR";
                modeBadge.className = "text-xs font-bold text-white bg-red-600 px-2 py-0.5 rounded uppercase";
            }

            resPlate.innerText = plate || '-';
            resOwner.innerText = owner || '-';
            resTime.innerText = time;

            modal.classList.remove('hidden');
            void modal.offsetWidth;
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);

            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function closeResultModal() {
            const modal = document.getElementById('resultModal');
            const content = document.getElementById('resultContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => modal.classList.add('hidden'), 250);
        }

        function addLogItem(title, time, success, mode) {
            const logContainer = document.getElementById('scanLog');
            const emptyState = document.getElementById('emptyLogState');
            if (emptyState) emptyState.style.display = 'none';

            const item = document.createElement('div');
            item.className = "flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100";

            const iconName = success ? 'check' : 'x';
            const iconBg = success ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600';
            const modeLabel = mode === 'entry' ? 'IN' : 'OUT';
            const modeColor = mode === 'entry' ? 'text-brand-600' : 'text-orange-600';

            item.innerHTML = `
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-full ${iconBg} flex items-center justify-center shrink-0">
                        <i data-lucide="${iconName}" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-bold text-gray-900 truncate">${title}</p>
                            <span class="text-[10px] font-bold ${modeColor} bg-white border border-gray-200 px-1 rounded">${modeLabel}</span>
                        </div>
                        <p class="text-xs text-gray-500">${success ? (mode === 'entry' ? 'Allowed Entry' : 'Allowed Exit') : 'Blocked'}</p>
                    </div>
                </div>
                <span class="text-xs font-mono text-gray-400 shrink-0">${time}</span>
            `;

            logContainer.insertBefore(item, logContainer.firstChild);
            scanCount++;
            document.getElementById('scanCount').innerText = `${scanCount} Scans`;

            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function handleManualInput(e) {
            e.preventDefault();
            const input = document.getElementById('manualInput');
            const text = (input.value || '').trim();
            if (!text) return;

            sendScanToServer(text);
            input.value = "";
        }

        document.addEventListener('DOMContentLoaded', function() {
            setGateMode('entry');
            if (typeof lucide !== 'undefined') lucide.createIcons();

            // AUTO START khusus mobile (kalau gagal, overlay tetap muncul untuk tap)
            const isMobile = window.matchMedia("(max-width: 768px)").matches;
            if (isMobile) {
                setTimeout(() => startScanner(), 500);
            }
        });
    </script>
@endpush
