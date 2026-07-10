<div class="p-6 max-w-5xl mx-auto space-y-6">

    @if (!$student)
        <div class="bg-amber-50 border border-amber-100 text-amber-800 p-6 rounded-2xl text-center text-sm font-medium">
            🔒 Akun Wali Murid Anda belum ditautkan ke data siswa mana pun oleh Administrator Sekolah.
        </div>
    @else
        <div
            class="bg-gray-900 text-white p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span
                    class="text-[10px] font-bold uppercase bg-white/10 px-2 py-0.5 rounded text-gray-300 tracking-wider">Portal
                    Wali Murid</span>
                <h1 class="text-xl font-bold mt-1">Laporan Presensi: {{ $student->name }}</h1>
                <p class="text-xs text-gray-400 mt-0.5">NISN: {{ $student->nisn }} • Kelas: <strong
                        class="text-white">{{ $student->classroom->name }}</strong></p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-xs text-gray-400 block">Periode Transparansi</span>
                <span class="text-sm font-bold text-indigo-400 font-mono">{{ $currentMonthName }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            @foreach ([
        'Hadir' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Total Hadir'],
        'Terlambat' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-100', 'text' => 'text-indigo-800', 'label' => 'Total Telat'],
        'Sakit' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-100', 'text' => 'text-amber-800', 'label' => 'Total Sakit'],
        'Izin' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-100', 'text' => 'text-blue-800', 'label' => 'Total Izin'],
        'Alpa' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-100', 'text' => 'text-rose-800', 'label' => 'Total Alpa'],
    ] as $status => $style)
                <div class="{{ $style['bg'] }} border {{ $style['border'] }} p-4 rounded-xl text-center shadow-2xs">
                    <span
                        class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">{{ $style['label'] }}</span>
                    <span class="text-xl font-black {{ $style['text'] }} font-mono">{{ $summary[$status] }}</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">Jam Pelajaran</span>
                </div>
            @endforeach
        </div>

        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Histori Lini Masa Harian</h2>

            @forelse($attendanceLogs as $dayLog)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                    <div class="bg-gray-50/70 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-800">📅 {{ $dayLog['formatted_date'] }}</span>
                        <span class="text-[10px] bg-gray-200/60 px-2 py-0.5 rounded text-gray-500 font-semibold">
                            {{ $dayLog['records']->count() }} Sesi Diikuti
                        </span>
                    </div>

                    <div class="divide-y divide-gray-50">
                        @foreach ($dayLog['records'] as $record)
                            <div
                                class="p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-3 hover:bg-gray-50/10 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 bg-gray-900 text-white font-bold rounded-xl text-xs flex flex-col items-center justify-center font-mono shrink-0">
                                        <span class="text-[9px] text-gray-400 leading-none uppercase">Jam</span>
                                        <span
                                            class="text-sm leading-none mt-0.5">{{ $record->attendance->schedule->period_start ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-gray-900">
                                            {{ $record->attendance->schedule->subject->name ?? 'Mata Pelajaran' }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">
                                            Waktu:
                                            {{ substr($record->attendance->schedule->time_start ?? '00:00', 0, 5) }} -
                                            {{ substr($record->attendance->schedule->time_end ?? '00:00', 0, 5) }}
                                            @if ($record->notes)
                                                • <span class="text-amber-600 italic">Catatan:
                                                    "{{ $record->notes }}"</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    @php
                                        $badgeStyle = match ($record->status) {
                                            'Hadir' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                                            'Terlambat' => 'bg-indigo-50 border-indigo-200 text-indigo-700',
                                            'Sakit' => 'bg-amber-50 border-amber-200 text-amber-700',
                                            'Izin' => 'bg-blue-50 border-blue-200 text-blue-700',
                                            default => 'bg-rose-50 border-rose-200 text-rose-700',
                                        };
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-xl border text-[11px] font-bold tracking-wide block text-center sm:inline shadow-2xs {{ $badgeStyle }}">
                                        {{ $record->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div
                    class="bg-white p-12 text-center text-gray-400 rounded-2xl border border-gray-100 text-xs shadow-xs">
                    Belum ada rekaman data absensi untuk anak Anda pada bulan ini.
                </div>
            @endforelse
        </div>
    @endif
</div>
