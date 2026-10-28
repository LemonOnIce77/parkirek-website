@extends('layouts.admin')

@section('title', 'Entry & Exit Logs')

@section('content')
    @php
        $cards = $cards ?? [];

        $todayEntry = $cards['today_entry'] ?? 0;
        $todayExit = $cards['today_exit'] ?? 0;
        $currentlyParked = $cards['currently_parked'] ?? 0;
        $avgMinutes = $cards['avg_minutes'] ?? 0;

        $avgH = intdiv((int) $avgMinutes, 60);
        $avgM = (int) $avgMinutes % 60;

        $status = $status ?? '';
        $platNo = $platNo ?? '';
    @endphp

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Entry & Exit Logs</h1>
            <p class="text-gray-500 text-sm mt-1">Real-time monitoring of vehicle movements.</p>
        </div>

    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Today's Entry</p>
            <h3 class="text-2xl font-bold text-brand-600 mt-1">{{ number_format($todayEntry) }}</h3>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Today's Exit</p>
            <h3 class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($todayExit) }}</h3>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Currently Parked</p>
            <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($currentlyParked) }}</h3>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 font-semibold uppercase">Avg. Duration</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-1">
                @if ($avgMinutes > 0)
                    {{ $avgH }}h {{ $avgM }}m
                @else
                    -
                @endif
            </h3>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        {{-- Filters (pakai form GET biar nyambung controller) --}}
        <form method="GET" class="p-4 border-b border-gray-200 flex gap-4">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="plat_no" value="{{ $platNo }}" placeholder="Search License Plate..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-200 outline-none">
            </div>

            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-600 outline-none">
                <option value="" {{ $status === '' ? 'selected' : '' }}>All Status</option>
                <option value="ACTIVE" {{ strtoupper($status) === 'ACTIVE' ? 'selected' : '' }}>Parked</option>
                <option value="CLOSED" {{ strtoupper($status) === 'CLOSED' ? 'selected' : '' }}>Exited</option>
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
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500">
                    <tr>
                        <th class="px-6 py-4">License Plate</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Entry Time</th>
                        <th class="px-6 py-4">Exit Time</th>
                        <th class="px-6 py-4">Duration</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($records as $r)
                        @php
                            $plate = $r->plat_snapshot ?? '-';

                            $inAt = $r->jam_masuk ? \Illuminate\Support\Carbon::parse($r->jam_masuk) : null;
                            $outAt = $r->jam_keluar ? \Illuminate\Support\Carbon::parse($r->jam_keluar) : null;

                            $isExited = $outAt !== null;
                            $durText = '-';

                            if ($inAt && $outAt) {
                                $mins = $inAt->diffInMinutes($outAt);
                                $h = intdiv($mins, 60);
                                $m = $mins % 60;
                                $durText = ($h ? "{$h}h " : '') . "{$m}m";
                            } elseif ($inAt && !$outAt) {
                                $durText = 'Running...';
                            }

                            // type optional: kalau kamu punya kolom jenis
                            $typeLabel = $r->kendaraan?->jenis_kendaraan ?? null;
                            $isMotor = $typeLabel && str_contains(strtolower($typeLabel), 'motor');
                            $typeText = $typeLabel ?: 'Vehicle';

                            $statusText = strtoupper($r->status ?? ($isExited ? 'CLOSED' : 'ACTIVE'));
                        @endphp

                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $plate }}</td>

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded
                                {{ $isMotor ? 'bg-orange-50 text-orange-700 border border-orange-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}
                                text-xs">
                                    <i data-lucide="{{ $isMotor ? 'bike' : 'car' }}" class="w-3 h-3"></i>
                                    {{ $typeText }}
                                </span>
                            </td>

                            <td class="px-6 py-4 {{ $inAt ? 'text-green-700 font-medium' : 'text-gray-400' }}">
                                {{ $inAt ? $inAt->format('d M Y, H:i') : '-' }}
                            </td>

                            <td class="px-6 py-4 {{ $outAt ? 'text-orange-700 font-medium' : 'text-gray-400' }}">
                                {{ $outAt ? $outAt->format('d M Y, H:i') : '-' }}
                            </td>

                            <td class="px-6 py-4 {{ $durText === 'Running...' ? 'text-blue-600 font-medium' : '' }}">
                                {{ $durText }}
                            </td>

                            <td class="px-6 py-4">
                                @if ($isExited || $statusText === 'CLOSED' || $statusText === 'DONE')
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-semibold">
                                        Exited
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">
                                        Parked
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer pagination --}}
        <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span class="text-xs text-gray-500">
                Showing {{ $records->count() }} of {{ $records->total() }} records
            </span>

            <div class="text-sm">
                {{ $records->links() }}
            </div>
        </div>
    </div>
@endsection
