<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6">

    <!-- BANNER WELCOME KEPALA SEKOLAH -->
    <div
        class="bg-gray-900 text-white p-5 sm:p-6 rounded-2xl shadow-xs relative overflow-hidden flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="relative z-10">
            <span class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Portal Monitoring Kepala
                Sekolah</span>
            <h1 class="text-xl sm:text-2xl font-bold mt-1">Eksekutif Rekapitulasi Presensi</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Tinjau tingkat kehadiran siswa dan ketaatan pengisian jurnal mengajar guru secara realtime.
            </p>
        </div>

        <div class="relative z-10 bg-white/10 backdrop-blur-md border border-white/10 px-4 py-2.5 rounded-xl text-right">
            <div class="text-[10px] text-gray-300 font-mono uppercase">Hari & Tanggal Ditinjau</div>
            <div class="text-sm font-bold text-white">{{ $dayName }}, {{ $dateFormatted }}</div>
        </div>
    </div>

    <!-- CONTROL BAR: FILTERS -->
    <div
        class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-center">
        <!-- FILTER TANGGAL -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">📅 Pilih Tanggal
                Tinjauan:</label>
            <input type="date" wire:model.live="selectedDate"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-semibold text-gray-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all cursor-pointer">
        </div>

        <!-- FILTER KELAS -->
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">🏫 Pilih Kelas /
                Rombel:</label>
            <select wire:model.live="selectedClassroomId"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-semibold text-gray-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                <option value="">-- Seluruh Kelas --</option>
                @foreach ($classrooms as $cls)
                    <option value="{{ $cls->id }}">Kelas {{ $cls->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- STATS PERSENTASE RINGKAS -->
        <div
            class="sm:col-span-2 lg:col-span-1 bg-indigo-50/60 border border-indigo-100 p-3 rounded-xl flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Tingkat Kehadiran
                    Siswa</span>
                <span class="text-2xl font-black font-mono text-indigo-950">{{ $attendancePercentage }}%</span>
            </div>
            <div class="text-right text-xs font-bold text-indigo-800">
                <span>{{ $totalStudents }} Total Siswa</span>
            </div>
        </div>
    </div>

    <!-- STATS CARDS: REKAPITULASI TOTAL HARI INI -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-emerald-100 bg-emerald-50/20 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-emerald-600 tracking-wider">Hadir</span>
            <div class="text-2xl font-black font-mono text-emerald-900 mt-1">{{ $summary['Hadir'] }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-indigo-100 bg-indigo-50/20 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-indigo-600 tracking-wider">Terlambat</span>
            <div class="text-2xl font-black font-mono text-indigo-900 mt-1">{{ $summary['Terlambat'] }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-amber-100 bg-amber-50/20 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-amber-600 tracking-wider">Sakit</span>
            <div class="text-2xl font-black font-mono text-amber-900 mt-1">{{ $summary['Sakit'] }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-blue-100 bg-blue-50/20 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-blue-600 tracking-wider">Izin</span>
            <div class="text-2xl font-black font-mono text-blue-900 mt-1">{{ $summary['Izin'] }}</div>
        </div>

        <div class="col-span-2 sm:col-span-1 bg-white p-4 rounded-2xl border border-rose-100 bg-rose-50/20 shadow-xs">
            <span class="text-[10px] font-bold uppercase text-rose-600 tracking-wider">Alpa</span>
            <div class="text-2xl font-black font-mono text-rose-900 mt-1">{{ $summary['Alpa'] }}</div>
        </div>
    </div>

    <!-- MONITORING KETAATAN GURU MENGAJAR PER JAM PELAJARAN -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden space-y-4 p-5">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Monitoring Jurnal & Presensi Kelas
                    Hari {{ $dayName }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">Memantau kelas mana saja yang sudah di-absen oleh guru
                    pengampu/piket.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-max">
                <thead>
                    <tr
                        class="bg-gray-50/80 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-3.5">Jam Ke</th>
                        <th class="p-3.5">Kelas & Mapel</th>
                        <th class="p-3.5">Guru Jadwal</th>
                        <th class="p-3.5">Status Pengisian</th>
                        <th class="p-3.5">Pengisi Absen Real</th>
                        <th class="p-3.5 text-center">Rincian Siswa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-700">
                    @forelse ($scheduleMonitoring as $item)
                        @php $sch = $item->schedule; @endphp
                        <tr class="hover:bg-gray-50/40 transition-colors">
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-800 rounded-lg font-mono font-bold">
                                    Jam {{ $sch->period_start }}-{{ $sch->period_end }}
                                </span>
                            </td>

                            <td class="p-3.5">
                                <div class="font-bold text-gray-900">{{ $sch->subject->name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">Kelas: {{ $sch->classroom->name }}
                                </div>
                            </td>

                            <td class="p-3.5 font-medium text-gray-800">
                                {{ $sch->teacher->name ?? '-' }}
                            </td>

                            <td class="p-3.5">
                                @if ($item->isLocked)
                                    <span
                                        class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-md font-bold text-[10px]">
                                        🔒 Dikunci Permanen
                                    </span>
                                @elseif ($item->isFilled)
                                    <span
                                        class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-md font-bold text-[10px]">
                                        📝 Terisi (Belum Dikunci)
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded-md font-bold text-[10px]">
                                        ⚠️ Belum Diisi Guru
                                    </span>
                                @endif
                            </td>

                            <td class="p-3.5">
                                <span class="font-semibold text-gray-900">{{ $item->inputBy }}</span>
                                @if ($item->notes !== '-')
                                    <div class="text-[10px] text-gray-400 truncate max-w-xs"
                                        title="{{ $item->notes }}">
                                        Jurnal: {{ $item->notes }}
                                    </div>
                                @endif
                            </td>

                            <td class="p-3.5 text-center">
                                @if ($item->isFilled)
                                    <span class="text-emerald-700 font-bold font-mono">{{ $item->totalPresent }}
                                        Hadir</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-rose-600 font-bold font-mono">{{ $item->totalAbsent }} Tidak
                                        Hadir</span>
                                @else
                                    <span class="text-gray-400 font-mono">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400 text-xs">
                                Tidak ada jadwal mata pelajaran pada hari {{ $dayName }} untuk kelas terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
