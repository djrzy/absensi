<div class="p-6 max-w-5xl mx-auto space-y-6">

    @if (empty($students) || count($students) === 0)
        <div
            class="bg-amber-50 border border-amber-100 text-amber-800 p-6 rounded-2xl text-center text-sm font-medium shadow-2xs">
            🔒 Akun Wali Murid Anda belum ditautkan ke data siswa mana pun oleh Administrator Sekolah.
        </div>
    @else
        <!-- BANNER INFO ANAK & SWITCHER MULTI-ANAK -->
        <div
            class="bg-gray-900 text-white p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span
                    class="text-[10px] font-bold uppercase bg-white/10 px-2 py-0.5 rounded text-gray-300 tracking-wider">
                    Portal Transparansi Ortu
                </span>
                <h1 class="text-xl font-bold mt-1">Siswa: {{ $selectedStudent->name }}</h1>
                <p class="text-xs text-gray-400 mt-0.5">
                    NISN: {{ $selectedStudent->nisn }} • Kelas: <strong
                        class="text-white">{{ $selectedStudent->classroom->name ?? '-' }}</strong>
                </p>
            </div>

            <div class="w-full sm:w-auto text-left sm:text-right space-y-1">
                @if (count($students) > 1)
                    <label class="text-[10px] text-indigo-300 font-bold uppercase block tracking-wider">
                        Pilih Anak Anda:
                    </label>
                    <select wire:model.live="selectedStudentId"
                        class="bg-gray-800 text-white border border-gray-700 rounded-xl px-3 py-1.5 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                        @foreach ($students as $child)
                            <option value="{{ $child->id }}">👦 {{ $child->name }} (Kelas
                                {{ $child->classroom->name ?? '-' }})</option>
                        @endforeach
                    </select>
                @else
                    <span class="text-xs text-gray-400 block">Periode Transparansi</span>
                    <span class="text-sm font-bold text-indigo-400 font-mono">{{ $currentMonthName }}</span>
                @endif
            </div>
        </div>

        <!-- ==================== WIDGET JADWAL MATA PELAJARAN MINGGUAN ==================== -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 space-y-4">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <div>
                    <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                        📅 Jadwal Pelajaran Mingguan
                    </h2>
                    <p class="text-[11px] text-gray-400 mt-0.5">Pantau mata pelajaran anak untuk persiapan belajar di
                        rumah.</p>
                </div>
                <span
                    class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg border border-indigo-100">
                    Hari Ini: {{ $todayName }}
                </span>
            </div>

            @if (empty($studentSchedules))
                <div class="p-6 text-center text-xs text-gray-400">
                    Belum ada jadwal mata pelajaran yang diatur untuk kelas ini.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($studentSchedules as $day => $schedules)
                        @php
                            $isToday = strtolower($day) === strtolower($todayName);
                        @endphp
                        <div
                            class="rounded-xl border transition-all {{ $isToday ? 'bg-indigo-50/30 border-indigo-200 ring-2 ring-indigo-100' : 'bg-gray-50/50 border-gray-100' }} p-3.5 space-y-2.5">
                            <div
                                class="flex justify-between items-center border-b pb-2 {{ $isToday ? 'border-indigo-100' : 'border-gray-200/60' }}">
                                <span
                                    class="text-xs font-extrabold uppercase {{ $isToday ? 'text-indigo-900' : 'text-gray-700' }}">
                                    {{ $day }}
                                </span>
                                @if ($isToday)
                                    <span class="text-[9px] bg-indigo-600 text-white font-bold px-1.5 py-0.5 rounded">
                                        HARI INI
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-2">
                                @foreach ($schedules as $sch)
                                    <div
                                        class="bg-white p-2.5 rounded-lg border border-gray-100 shadow-2xs flex justify-between items-start gap-2">
                                        <div>
                                            <div class="text-xs font-bold text-gray-900">
                                                {{ $sch->subject->name ?? 'Mapel' }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 mt-0.5">
                                                👨‍🏫 {{ $sch->teacher->name ?? 'Guru Mapel' }}
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span
                                                class="text-[9px] font-mono font-bold bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded block">
                                                Jam {{ $sch->period_start }}
                                            </span>
                                            <span class="text-[9px] text-gray-400 font-mono mt-0.5 block">
                                                {{ substr($sch->time_start, 0, 5) }} -
                                                {{ substr($sch->time_end, 0, 5) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- RINGKASAN KARTU SKOR BULANAN -->
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

        <!-- LINI MASA DETAIL PER JAM PELAJARAN -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                Histori Lini Masa Harian ({{ $currentMonthName }})
            </h2>

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
                    Belum ada rekaman data absensi untuk {{ $selectedStudent->name }} pada bulan ini.
                </div>
            @endforelse
        </div>
    @endif
</div>
