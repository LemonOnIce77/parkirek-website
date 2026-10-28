@extends('layouts.admin')

@section('title', 'Violations')

@section('content')
    @php
        $cards = $cards ?? [];
        $activeOpen = $cards['active_open'] ?? 0;
        $unpaidTotal = $cards['unpaid_total'] ?? 0;
        $resolvedToday = $cards['resolved_today'] ?? 0;

        $status = $status ?? '';
        $platNo = $platNo ?? '';

        $kendaraans = $kendaraans ?? collect();
    @endphp

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Violation Management</h1>
            <p class="text-gray-500 text-sm mt-1">Track parking violations and fines.</p>
        </div>

        <button type="button" onclick="openModal()"
            class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium shadow-sm shadow-red-200">
            <i data-lucide="triangle-alert" class="w-4 h-4"></i>
            Report Violation
        </button>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Active Violations</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($activeOpen) }}</p>
            </div>
            <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                <i data-lucide="ban" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Unpaid Fines (OPEN)</p>
                <p class="text-2xl font-bold text-orange-600 mt-1">
                    Rp {{ number_format($unpaidTotal, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Resolved Today</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($resolvedToday) }}</p>
            </div>
            <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                <i data-lucide="check-check" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        {{-- Filters (server-side) --}}
        <form method="GET"
            class="p-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row gap-4 justify-between items-center">
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <div class="relative w-full sm:w-64">
                    <i data-lucide="search"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" name="plat_no" value="{{ $platNo }}" placeholder="Search Plate No..."
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 outline-none transition-all uppercase">
                </div>

                <select name="status"
                    class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg block w-full p-2 cursor-pointer">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>All Status</option>
                    <option value="OPEN" {{ strtoupper($status) === 'OPEN' ? 'selected' : '' }}>OPEN (Unpaid)</option>
                    <option value="CLOSED" {{ strtoupper($status) === 'CLOSED' ? 'selected' : '' }}>CLOSED (Paid)</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-black transition-colors">
                    Apply
                </button>

                @if ($platNo || $status)
                    <a href="{{ url()->current() }}"
                        class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-white text-xs uppercase font-semibold text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Plate</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">STNK</th>
                        <th class="px-6 py-4">Vehicle Type</th>
                        <th class="px-6 py-4">Violation</th>
                        <th class="px-6 py-4">Fine</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($violations as $v)
                        @php
                            $owner =
                                $v->kendaraan?->user?->nama ??
                                ($v->kendaraan?->user?->name ?? ($v->kendaraan?->user?->email ?? '-'));

                            $stnk = $v->kendaraan?->stnk_number ?? '-';
                            $jenis = $v->kendaraan?->jenis_kendaraan ?? '-';
                            $isOpen = strtoupper($v->status) === 'OPEN';
                            $fine = $v->denda ? 'Rp ' . number_format($v->denda, 0, ',', '.') : '-';
                        @endphp

                        <tr class="hover:bg-red-50/20 transition-colors group">
                            <td class="px-6 py-4 font-mono font-medium text-gray-500">#V-{{ $v->id }}</td>

                            <td class="px-6 py-4 font-bold text-gray-900 group-hover:text-red-600 transition-colors">
                                {{ $v->plat_no }}
                            </td>

                            <td class="px-6 py-4">{{ $owner }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-700">{{ $stnk }}</td>
                            <td class="px-6 py-4">{{ $jenis }}</td>
                            <td class="px-6 py-4">{{ $v->jenis_pelanggaran }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $fine }}</td>

                            <td class="px-6 py-4">
                                @if ($isOpen)
                                    <span
                                        class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold border border-red-200">OPEN</span>
                                @else
                                    <span
                                        class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold border border-green-200">CLOSED</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if ($isOpen)
                                    <form method="POST" action="{{ route('admin.violations.status', $v->id) }}"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="CLOSED">
                                        <button type="submit"
                                            class="text-gray-400 hover:text-green-600 p-2 rounded-lg transition-all"
                                            title="Mark Paid">
                                            <i data-lucide="check-square" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="text-gray-300 cursor-not-allowed p-2" disabled title="Already closed">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-gray-500">No violations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span class="text-xs text-gray-500">
                Showing {{ $violations->count() }} of {{ $violations->total() }} records
            </span>
            <div class="text-sm">
                {{ $violations->links() }}
            </div>
        </div>
    </div>

    {{-- Modal: Report Violation --}}
    <div id="violationModal" class="hidden fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm">
        {{-- overlay scroll container --}}
        <div class="min-h-dvh w-full overflow-y-auto p-4 flex items-start sm:items-center justify-center" onclick="onBackdrop(event)">
            <div class="bg-white rounded-xl shadow-2xl w-full sm:w-[560px] max-w-[95%] overflow-hidden" role="dialog"
                aria-modal="true" aria-labelledby="violationModalTitle">
                <div class="max-h-[calc(100dvh-2rem)] flex flex-col">
                    {{-- header --}}
                    <div class="bg-red-600 px-6 py-4 flex justify-between items-center shrink-0">
                        <div class="text-white">
                            <h3 id="violationModalTitle" class="text-lg font-bold">Report New Violation</h3>
                            <p class="text-red-100 text-xs mt-0.5">
                                Pilih kendaraan, isi pelanggaran, denda, dan foto bukti
                            </p>
                        </div>
                        <button type="button" onclick="closeModal()"
                            class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 p-1.5 rounded-full transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.violations.store') }}" enctype="multipart/form-data"
                        class="flex flex-col min-h-0">
                        @csrf

                        {{-- body (scrollable) --}}
                        <div class="p-6 space-y-4 overflow-y-auto min-h-0">
                            {{-- Kendaraan dropdown --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Vehicle (Plate)</label>

                                <select name="kendaraan_id" id="kendaraanSelect" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-red-500 bg-white">
                                    <option value="">-- Choose plate --</option>

                                    @foreach ($kendaraans as $k)
                                        @php
                                            $ownerName = $k->user?->nama ?? ($k->user?->name ?? ($k->user?->email ?? '-'));
                                        @endphp
                                        <option value="{{ $k->id }}"
                                            data-plate="{{ strtoupper($k->plat_no) }}"
                                            data-stnk="{{ $k->stnk_number ?? '-' }}"
                                            data-owner="{{ $ownerName }}"
                                            data-jenis="{{ $k->jenis_kendaraan ?? '-' }}">
                                            {{ strtoupper($k->plat_no) }} — {{ $ownerName }}
                                        </option>
                                    @endforeach
                                </select>

                                <p class="text-xs text-gray-500 mt-1">
                                    Setelah pilih plate, STNK / Owner / Vehicle Type akan terisi otomatis.
                                </p>

                                @error('kendaraan_id')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Auto-fill preview --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="text-[11px] text-gray-500 uppercase">STNK</div>
                                    <div id="pvStnk" class="text-sm font-semibold text-gray-800">-</div>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="text-[11px] text-gray-500 uppercase">Owner</div>
                                    <div id="pvOwner" class="text-sm font-semibold text-gray-800">-</div>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="text-[11px] text-gray-500 uppercase">Vehicle Type</div>
                                    <div id="pvJenis" class="text-sm font-semibold text-gray-800">-</div>
                                </div>
                            </div>

                            {{-- Violation type --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Violation Type</label>
                                <input name="jenis_pelanggaran" type="text" required placeholder="e.g. Illegal Parking"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-red-500">
                                @error('jenis_pelanggaran')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Desc --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                                <textarea name="deskripsi" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-red-500"></textarea>
                                @error('deskripsi')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Fine --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fine Amount (Rp)</label>
                                <input name="denda" type="number" min="0" placeholder="50000"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-red-500">
                                @error('denda')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Photo --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Evidence (Photo)</label>
                                <input name="foto" type="file" accept="image/*"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-red-500 bg-white">
                                <p class="text-xs text-gray-500 mt-1">jpg/png/webp max 3MB</p>
                                @error('foto')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- footer --}}
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 shrink-0">
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm transition-colors">
                                Issue Fine
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const modalElId = 'violationModal';

        function openModal() {
            const el = document.getElementById(modalElId);
            if (!el) return;
            el.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // lock bg scroll
        }

        function closeModal() {
            const el = document.getElementById(modalElId);
            if (!el) return;
            el.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // close when click backdrop (NOT when click inside modal)
        function onBackdrop(e) {
            const modal = document.getElementById(modalElId);
            if (!modal) return;
            if (e.target === e.currentTarget) closeModal();
        }

        // close with ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        // Autofill preview dari dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('kendaraanSelect');
            const pvStnk = document.getElementById('pvStnk');
            const pvOwner = document.getElementById('pvOwner');
            const pvJenis = document.getElementById('pvJenis');

            if (!select) return;

            const fill = () => {
                const opt = select.options[select.selectedIndex];
                if (!opt || !opt.value) {
                    pvStnk.textContent = '-';
                    pvOwner.textContent = '-';
                    pvJenis.textContent = '-';
                    return;
                }

                pvStnk.textContent = opt.dataset.stnk || '-';
                pvOwner.textContent = opt.dataset.owner || '-';
                pvJenis.textContent = opt.dataset.jenis || '-';
            };

            select.addEventListener('change', fill);
            fill();
        });
    </script>
@endpush
