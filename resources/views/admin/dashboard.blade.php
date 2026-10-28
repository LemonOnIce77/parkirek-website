@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $summary = $summary ?? [];
    $today = $today ?? now();

    // sesuai controller baru
    $vehiclesIn  = $summary['vehicles_in_today']  ?? 0;
    $vehiclesOut = $summary['vehicles_out_today'] ?? 0;

    $violationsOpen = $summary['total_pelanggaran_open'] ?? 0;

    // tanggal di header
    $todayLabel = \Illuminate\Support\Carbon::parse($today)->translatedFormat('l, d F Y');
@endphp

{{-- HEADER --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Today's Overview</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $todayLabel }}</p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('admin.scan') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-medium hover:bg-brand-700 transition shadow-sm">
            <i data-lucide="qr-code" class="w-4 h-4"></i>
            Open Scan
        </a>
    </div>
</div>

{{-- METRIC CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

    {{-- Vehicles In --}}
    <button type="button" onclick="openMetricModal('in')"
        class="text-left group bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md hover:border-brand-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <i data-lucide="arrow-down-circle"
           class="absolute -right-4 -bottom-4 w-24 h-24 text-brand-50 opacity-50 group-hover:scale-110 transition-transform"></i>

        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-brand-50 rounded-lg text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                    <i data-lucide="car" class="w-6 h-6"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full border border-green-100 flex items-center gap-1">
                    <i data-lucide="activity" class="w-3 h-3"></i> Today
                </span>
            </div>

            <h3 class="text-gray-500 text-sm font-medium">Vehicles In</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($vehiclesIn) }}</p>
        </div>
    </button>

    {{-- Vehicles Out --}}
    <button type="button" onclick="openMetricModal('out')"
        class="text-left group bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md hover:border-orange-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <i data-lucide="arrow-up-circle"
           class="absolute -right-4 -bottom-4 w-24 h-24 text-orange-50 opacity-50 group-hover:scale-110 transition-transform"></i>

        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-orange-50 rounded-lg text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition-colors">
                    <i data-lucide="log-out" class="w-6 h-6"></i>
                </div>
                <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full border border-orange-100 flex items-center gap-1">
                    <i data-lucide="activity" class="w-3 h-3"></i> Today
                </span>
            </div>

            <h3 class="text-gray-500 text-sm font-medium">Vehicles Out</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($vehiclesOut) }}</p>
        </div>
    </button>

    {{-- Violations --}}
    <a href="{{ route('admin.violations') }}"
        class="group bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300 hover:-translate-y-1 cursor-pointer relative overflow-hidden">
        <i data-lucide="alert-octagon"
           class="absolute -right-4 -bottom-4 w-24 h-24 text-red-50 opacity-50 group-hover:scale-110 transition-transform"></i>

        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-red-50 rounded-lg text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full border border-red-100">
                    Open
                </span>
            </div>

            <h3 class="text-gray-500 text-sm font-medium">Violations</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($violationsOpen) }}</p>
        </div>
    </a>
</div>

{{-- CHART + RECENT --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

    {{-- Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            @php
                $statsUrl = \Illuminate\Support\Facades\Route::has('admin.statistics')
                    ? route('admin.statistics')
                    : '#';
            @endphp

            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-gray-900">Traffic Statistics</h2>
                @if($statsUrl !== '#')
                    <a href="{{ $statsUrl }}" class="text-brand-600 hover:underline text-sm font-medium inline-flex items-center gap-1">
                        Open <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>

            <select id="chartFilter" onchange="updateChartData(this.value)"
                class="bg-gray-50 border border-gray-200 text-sm text-gray-600 rounded-lg px-3 py-2 outline-none cursor-pointer hover:bg-gray-100 focus:ring-2 focus:ring-brand-200 transition-all">
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </select>
        </div>

        <div class="relative h-72 w-full">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">Recent Activity</h2>
        </div>

        <div class="space-y-3 flex-1 overflow-y-auto pr-2 custom-scroll" style="max-height: 320px;">
            @forelse(($scanTerbaru ?? []) as $row)
                @php
                    $plat = $row->plat_snapshot ?? '-';

                    $inAt  = $row->jam_masuk ? \Illuminate\Support\Carbon::parse($row->jam_masuk) : null;
                    $outAt = $row->jam_keluar ? \Illuminate\Support\Carbon::parse($row->jam_keluar) : null;

                    $isOutEvent = $outAt && (!$inAt || $outAt->greaterThan($inAt));
                    $label = $isOutEvent ? 'OUT' : 'IN';
                    $time  = ($isOutEvent ? $outAt : $inAt)?->diffForHumans() ?? '-';

                    $badgeClass = $isOutEvent
                        ? 'text-orange-700 bg-orange-50 border-orange-100'
                        : 'text-green-700 bg-green-50 border-green-100';
                @endphp

                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors border border-transparent hover:border-gray-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full text-brand-600 bg-brand-50 ring-brand-100 ring-1 flex items-center justify-center shrink-0">
                            <i data-lucide="car" class="w-5 h-5"></i>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $plat }}</p>
                            <span class="inline-flex items-center text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $badgeClass }}">
                                {{ $label }}
                            </span>
                        </div>
                    </div>

                    <span class="text-xs font-medium text-gray-400 shrink-0">{{ $time }}</span>
                </div>
            @empty
                <div class="text-sm text-gray-500">No recent activity.</div>
            @endforelse
        </div>

        <button type="button"
            onclick="window.location.reload()"
            class="w-full mt-5 py-2 text-sm text-brand-600 font-medium hover:bg-brand-50 rounded-lg transition-colors border border-dashed border-brand-200 hover:border-brand-300">
            Refresh
        </button>
    </div>
</div>

{{-- METRIC MODAL --}}
<div id="metricModal" class="hidden fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center backdrop-blur-sm px-4">
    <div class="bg-white rounded-xl shadow-2xl w-[600px] max-w-[95%] overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b border-gray-200">
            <div>
                <h3 class="text-lg font-bold text-gray-900" id="metricModalTitle">Detail Metric</h3>
                <p class="text-xs text-gray-500" id="metricModalSubtitle">Hourly Breakdown</p>
            </div>
            <button type="button" onclick="closeMetricModal()"
                class="text-gray-400 hover:text-gray-600 bg-white hover:bg-gray-100 p-1.5 rounded-full border border-gray-200 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="p-6">
            <div class="relative h-64 w-full">
                <canvas id="metricChart"></canvas>
            </div>

            <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-blue-100 rounded-full text-blue-600 shrink-0">
                        <i data-lucide="lightbulb" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">Insight</h4>
                        <p class="text-xs text-blue-700 mt-1" id="metricModalInsight">Analysis data will appear here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="aiChatModal" class="hidden fixed bottom-24 right-6 w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden flex flex-col" style="height: 500px;">
    <div class="bg-brand-600 p-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2 text-white">
            <i data-lucide="bot" class="w-5 h-5"></i><span class="font-bold">Chat ParkiRek</span>
        </div>
        <button type="button" onclick="toggleChat()" class="text-brand-100 hover:text-white">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 scroll-smooth">
        <div class="text-sm text-gray-500">Chat placeholder (boleh tempel versi kamu yang lama).</div>
    </div>

    <div class="p-3 bg-white border-t border-gray-100 shrink-0">
        <input type="text" placeholder="Type a message..."
               class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // =========================
    // Traffic chart (REAL DATA)
    // =========================
const trafficChart = @json($trafficChart ?? null) || {
    week:  { labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'], data: [0,0,0,0,0,0,0] },
    month: { labels: ['Week 1','Week 2','Week 3','Week 4'], data: [0,0,0,0] },
    year:  { labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'], data: [0,0,0,0,0,0,0,0,0,0,0,0] },
};


    const getPack = (key) => {
        const pack = trafficChart?.[key];
        if (!pack || !Array.isArray(pack.labels) || !Array.isArray(pack.data)) return null;
        return pack;
    };

    // default follow dropdown (week)
    let currentKey = 'week';
    const initialPack = getPack(currentKey) || getPack('month') || getPack('year');

    const canvas = document.getElementById('revenueChart');
    let chart = null;

    if (canvas && typeof Chart !== 'undefined' && initialPack) {
        chart = new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: initialPack.labels,
                datasets: [{
                    label: 'Traffic',
                    data: initialPack.data,
                    borderColor: '#9F1421',
                    backgroundColor: 'rgba(159, 20, 33, 0.08)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#9F1421',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } } }
            }
        });
    }

    window.updateChartData = (val) => {
        if (!chart) return;
        const pack = getPack(val);
        if (!pack) return;

        currentKey = val;
        chart.data.labels = pack.labels;
        chart.data.datasets[0].data = pack.data;
        chart.update();
    };

    // Sync select default
    const select = document.getElementById('chartFilter');
    if (select) select.value = currentKey;

    // =========================
    // Metric modal (masih dummy)
    // =========================
    let metricChartInstance = null;

    window.openMetricModal = (type) => {
        const modal = document.getElementById('metricModal');
        const title = document.getElementById('metricModalTitle');
        const subtitle = document.getElementById('metricModalSubtitle');
        const insight = document.getElementById('metricModalInsight');
        const metricCanvas = document.getElementById('metricChart');

        if (!modal || !metricCanvas || typeof Chart === 'undefined') return;

        modal.classList.remove('hidden');
        if (metricChartInstance) metricChartInstance.destroy();

        let chartData = { labels: [], datasets: [] };

        if (type === 'in') {
            title.textContent = 'Incoming Traffic Analysis';
            subtitle.textContent = 'Hourly Entry Breakdown (Today)';
            insight.textContent = 'Peak entry time insight can be calculated from real data later.';
            chartData = {
                labels: ['06:00','08:00','10:00','12:00','14:00','16:00','18:00'],
                datasets: [{ label: 'Entries', data: [20,150,80,40,60,30,20], tension: 0.4 }]
            };
        } else if (type === 'out') {
            title.textContent = 'Outgoing Traffic Analysis';
            subtitle.textContent = 'Hourly Exit Breakdown (Today)';
            insight.textContent = 'Exit peak insight can be calculated from real data later.';
            chartData = {
                labels: ['06:00','08:00','10:00','12:00','14:00','16:00','18:00'],
                datasets: [{ label: 'Exits', data: [5,10,30,80,90,140,100], tension: 0.4 }]
            };
        }

        metricChartInstance = new Chart(metricCanvas.getContext('2d'), {
            type: 'line',
            data: chartData,
            options: { responsive: true, maintainAspectRatio: false }
        });

        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    window.closeMetricModal = () => document.getElementById('metricModal')?.classList.add('hidden');

    // =========================
    // Chat toggle (optional)
    // =========================
    window.toggleChat = () => document.getElementById('aiChatModal')?.classList.toggle('hidden');
});
</script>
@endpush
