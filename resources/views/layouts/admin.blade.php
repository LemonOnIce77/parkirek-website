<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkiRek - @yield('title', 'Admin')</title>

    <!-- 1. Tailwind CSS CDN (Wajib untuk Admin UI) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- 2. Chart.js & Icons & Fonts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- 3. Konfigurasi Warna Admin -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fbf4f5',
                            100: '#f6e3e5',
                            200: '#ecc9cd',
                            300: '#dfa2a9',
                            400: '#cc6e78',
                            500: '#b64451',
                            600: '#9F1421',
                            700: '#85111b',
                            800: '#6f111a',
                            900: '#5e131a',
                            950: '#33080c',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .typing-dot {
            animation: typing 1.4s infinite ease-in-out both;
        }

        .typing-dot:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-dot:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typing {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        .toast {
            animation: slideIn 0.3s ease-out forwards;
        }

        .toast.hiding {
            animation: fadeOut 0.3s ease-in forwards;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased overflow-hidden">

    <div id="toast-container" class="fixed top-5 right-5 z-[100] flex flex-col gap-2"></div>

    <div class="flex h-screen">

        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="transform -translate-x-full md:translate-x-0 fixed md:relative z-40 w-64 h-full bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out flex flex-col shadow-xl md:shadow-none">
            <div class="h-16 flex items-center px-6 border-b border-gray-100">
                <a href="{{ url('/admin/dashboard') }}"
                    class="flex items-center gap-3 font-bold text-xl text-brand-600 hover:opacity-90 transition-all">
                    <div class="w-9 h-9 bg-gradient-to-br from-brand-600 to-brand-800 text-white rounded-xl flex items-center justify-center shadow-md shadow-brand-900/15">
                        <i data-lucide="parking-square" class="w-5 h-5"></i>
                    </div>
                    <span class="tracking-tight">ParkiRek</span>
                </a>

                <button id="close-sidebar" class="md:hidden ml-auto text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                @php
                    $navBase =
                        'flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all hover:translate-x-1';
                    $navActive = 'text-brand-600 bg-brand-50 shadow-sm ring-1 ring-brand-100';
                    $navInactive = 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
                @endphp

                <div class="mb-4">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Menu</p>

                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ $navBase }} {{ request()->routeIs('admin.dashboard') ? $navActive : $navInactive }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                    </a>

                    <a href="{{ route('admin.scan') }}"
                        class="{{ $navBase }} {{ request()->routeIs('admin.scan') ? $navActive : $navInactive }}">
                        <i data-lucide="qr-code" class="w-5 h-5"></i> Scan Gate
                    </a>

                    <a href="{{ route('admin.vehicle_logs') }}"
                        class="{{ $navBase }} {{ request()->routeIs('admin.vehicle_logs') ? $navActive : $navInactive }}">
                        <i data-lucide="arrow-right-left" class="w-5 h-5"></i> Entry/Exit Logs
                    </a>

                    <a href="{{ route('admin.violations') }}"
                        class="{{ $navBase }} {{ request()->routeIs('admin.violations') ? $navActive : $navInactive }}">
                        <i data-lucide="alert-octagon" class="w-5 h-5"></i> Violations
                    </a>
                </div>
            </nav>

            <div class="border-t border-gray-100 p-4">
                @auth
                    <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-3 w-full p-2 rounded-lg text-gray-600 hover:text-red-600 hover:bg-gray-50 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span class="text-sm font-medium">Logout</span>
                        </button>
                    </form>
                @endauth
            </div>

        </aside>

        <!-- Overlay Mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/50 z-30 hidden md:hidden glass-effect"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full overflow-hidden relative w-full">
            <header
                class="h-16 bg-white/90 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <nav class="hidden sm:flex text-sm text-gray-500">
                        <ol class="flex items-center space-x-2">
                            <li class="font-semibold text-brand-600">ParkiRek</li>
                            <li>/</li>
                            <li class="font-medium text-gray-900">@yield('title', 'Page')</li>
                        </ol>
                    </nav>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-100">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Gate Active</span>
                    </div>

                    @auth
                    <div class="flex items-center gap-2.5 pl-3 border-l border-gray-200">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-700 to-brand-500 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
                        </div>
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-xs font-bold text-gray-800 leading-tight">{{ auth()->user()->nama ?? 'Security Admin' }}</span>
                            <span class="text-[10px] text-gray-400 font-medium">{{ auth()->user()->area ?? 'Gerbang Utama' }}</span>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Global JS for Admin --}}
    <script>
        // init icons (safe)
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });

        // Sidebar
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-sidebar');

        function toggleSidebar() {
            if (!sidebar) return;
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                if (overlay) overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
            }
        }

        if (mobileBtn) mobileBtn.addEventListener('click', toggleSidebar);
        if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

        // Toast helper
        window.showToast = (msg, type = 'info') => {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const bg = type === 'success' ? 'bg-green-600' : (type === 'error' ? 'bg-red-600' : 'bg-gray-800');
            const icon = type === 'success' ? 'check' : (type === 'error' ? 'x' : 'info');

            const box = document.createElement('div');
            box.className = `${bg} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 text-sm toast`;
            box.innerHTML = `<i data-lucide="${icon}" class="w-4 h-4"></i><span>${msg}</span>`;

            container.appendChild(box);
            if (typeof lucide !== 'undefined') lucide.createIcons();

            setTimeout(() => {
                box.classList.add('hiding');
                setTimeout(() => box.remove(), 300);
            }, 3000);
        };

        // ✅ GLOBAL fetch helper (for scan & others)
        window.postScan = async (url, payload) => {
            const meta = document.querySelector('meta[name="csrf-token"]');
            const token = meta ? meta.getAttribute('content') : null;

            if (!token) throw new Error('CSRF token meta tag not found');

            const res = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin', // wajib untuk session cookie
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const text = await res.text();
                throw new Error(text);
            }

            return res.json().catch(() => ({}));
        };
    </script>

    {{-- Page specific scripts --}}
    @stack('scripts')
</body>

</html>
