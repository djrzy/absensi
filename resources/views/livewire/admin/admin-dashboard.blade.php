<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6">

    <!-- WELCOME BANNER ADMIN -->
    <div
        class="bg-gray-900 text-white p-5 sm:p-6 rounded-2xl shadow-xs relative overflow-hidden flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="relative z-10">
            {{-- <span class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Control Tower System</span> --}}
            <h1 class="text-xl sm:text-2xl font-bold mt-1">Dashboard Administrator</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Kelola aktivitas sekolah, pantau absensi harian, dan ambil alih kontrol presensi.
            </p>
        </div>

        <div class="relative z-10 bg-white/10 backdrop-blur-md border border-white/10 px-4 py-2.5 rounded-xl text-right">
            <div class="text-[10px] text-gray-300 font-mono uppercase">Hari & Tanggal Ditinjau</div>
            <div class="text-sm font-bold text-white">{{ $dayName }}, {{ $dateFormatted }}</div>
        </div>
    </div>

    @if (session()->has('success'))
        <div
            class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium flex items-center justify-between">
            <span>✅ {{ session('success') }}</span>
        </div>
    @endif

    <!-- QUICK SYSTEM COUNTERS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="/admin/siswa"
            class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs hover:border-indigo-200 transition-all flex items-center justify-between group">
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Siswa
                    Terdaftar</span>
                <span class="text-2xl font-black font-mono text-gray-900 mt-1 block">{{ $totalStudentsCount }}</span>
            </div>
            <span class="text-2xl group-hover:scale-110 transition-transform">🎒</span>
        </a>

        <a href="/admin/kelas"
            class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs hover:border-indigo-200 transition-all flex items-center justify-between group">
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Rombel / Kelas</span>
                <span class="text-2xl font-black font-mono text-gray-900 mt-1 block">{{ $totalClassroomsCount }}</span>
            </div>
            <span class="text-2xl group-hover:scale-110 transition-transform">🏫</span>
        </a>

        <a href="/admin/guru"
            class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs hover:border-indigo-200 transition-all flex items-center justify-between group">
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Guru /
                    Pengajar</span>
                <span class="text-2xl font-black font-mono text-gray-900 mt-1 block">{{ $totalTeachersCount }}</span>
            </div>
            <span class="text-2xl group-hover:scale-110 transition-transform">👨‍🏫</span>
        </a>
    </div>

    <!-- FILTER BAR -->
    <div
        class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-center">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">📅 Tanggal
                Tinjauan:</label>
            <input type="date" wire:model.live="selectedDate"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-semibold text-gray-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all cursor-pointer">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">🏫 Filter Kelas:</label>
            <select wire:model.live="selectedClassroomId"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-semibold text-gray-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                <option value="">-- Seluruh Kelas --</option>
                @foreach ($classrooms as $cls)
                    <option value="{{ $cls->id }}">Kelas {{ $cls->name }}</option>
                @endforeach
            </select>
        </div>

        <div
            class="sm:col-span-2 lg:col-span-1 bg-indigo-50/60 border border-indigo-100 p-3 rounded-xl flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Tingkat Kehadiran
                    Siswa</span>
                <span class="text-2xl font-black font-mono text-indigo-950">{{ $attendancePercentage }}%</span>
            </div>
            <div class="text-right text-xs font-bold text-indigo-800">
                <span>{{ $totalStudentsInScope }} Siswa Terkait</span>
            </div>
        </div>
    </div>

    <!-- REKAP STATUS RINGKAS -->
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

    <!-- TABEL CONTROL TOWER & AKSI ADMIN -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden space-y-4 p-5">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Presensi Hari
                    {{ $dayName }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pantau status, edit langsung, atau buka kunci presensi jika
                    diperlukan.</p>
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
                        <th class="p-3.5">Pengisi Real</th>
                        <th class="p-3.5 text-center">Aksi Admin</th>
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
                                        📝 Terisi
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded-md font-bold text-[10px]">
                                        ⚠️ Belum Diisi
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

                            <!-- AKSI KHUSUS ADMIN -->
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- <a href="{{ route('absensi.take', $sch->id) }}"
                                        class="px-2.5 py-1 bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-bold text-[11px] transition-all">
                                        {{ $item->isFilled ? '✏️ Edit' : '➕ Isi Absen' }}
                                    </a> --}}

                                    @if ($item->isLocked)
                                        <button type="button" wire:click="unlockAttendance({{ $item->attendanceId }})"
                                            wire:confirm="Yakin ingin membuka kunci presensi kelas ini untuk perbaikan data?"
                                            class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-lg font-bold text-[11px] transition-all cursor-pointer">
                                            🔓 Buka Kunci
                                        </button>
                                    @endif
                                </div>
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
