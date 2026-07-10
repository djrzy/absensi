<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-4 flex justify-between items-center">
        <a href="/dashboard" class="text-xs font-semibold text-gray-500 hover:text-gray-900 transition-colors">
            ← Kembali ke Dashboard
        </a>

        @if (!$isLocked)
            <button wire:click="lockAttendance"
                wire:confirm="PERINGATAN: Mengunci absensi akan membuat data ini permanen dan tidak bisa diedit lagi oleh Guru. Yakin?"
                class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg transition-all cursor-pointer shadow-xs">
                🔒 Kunci Absensi Hari Ini
            </button>
        @else
            <span
                class="px-3 py-1 bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-lg shadow-xs flex items-center gap-1">
                🛑 Status: Terkunci Permanen
            </span>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if ($isLateForAttendance && !$isLocked)
        <div
            class="mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-950 rounded-xl text-xs flex items-center gap-2">
            <span>⚠️</span>
            <p><strong>Perhatian Guru:</strong> Waktu pengisian telah melewati 15 menit dari jam mulai jadwal
                ({{ \Carbon\Carbon::parse($schedule->time_start)->format('H:i') }}). Silahkan gunakan opsi status
                <strong>T (Terlambat)</strong> jika diperlukan.
            </p>
        </div>
    @endif

    @if ($holidayDescription)
        <div
            class="mb-4 p-5 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl text-xs font-medium shadow-2xs flex items-center gap-3">
            <span class="text-xl">🏖️</span>
            <div>
                <strong>Pengisian Absen Ditutup:</strong> Hari ini dinyatakan libur sekolah efektif berdasarkan Kalender
                Akademik resmi.
                <div class="text-rose-600 font-bold mt-1">Alasan: {{ $holidayDescription }}</div>
            </div>
        </div>
    @endif

    <div
        class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden {{ $isLocked ? 'opacity-70 pointer-events-none' : '' }}">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50/70 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4 w-16 text-center">No</th>
                        <th class="p-4">Nama Siswa</th>
                        <th class="p-4 w-72 text-center">Status Kehadiran</th>
                        <th class="p-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                    @foreach ($students as $index => $student)
                        <tr
                            class="transition-colors {{ isset($inheritedStatuses[$student->id]) && $inheritedStatuses[$student->id] ? 'bg-amber-50/40 hover:bg-amber-50/60' : 'hover:bg-gray-50/30' }}">
                            <td class="p-4 text-center text-gray-400 font-medium">{{ $index + 1 }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-1.5">
                                    <div class="font-semibold text-gray-900">{{ $student->name }}</div>
                                    @if (isset($inheritedStatuses[$student->id]) && $inheritedStatuses[$student->id])
                                        <span
                                            class="text-[10px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded-md font-medium">🔄
                                            Auto</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 font-mono">NISN: {{ $student->nisn }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-center gap-1">
                                    @foreach ([
        'Hadir' => 'peer-checked:bg-emerald-600 peer-checked:text-white',
        'Terlambat' => 'peer-checked:bg-indigo-600 peer-checked:text-white',
        'Sakit' => 'peer-checked:bg-amber-500 peer-checked:text-white',
        'Izin' => 'peer-checked:bg-blue-600 peer-checked:text-white',
        'Alpa' => 'peer-checked:bg-rose-600 peer-checked:text-white',
    ] as $status => $activeClass)
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="attendanceData.{{ $student->id }}"
                                                value="{{ $status }}" {{ $isLocked ? 'disabled' : '' }}
                                                class="sr-only peer">
                                            <span
                                                class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-400 inline-block transition-all hover:bg-gray-50 {{ $activeClass }}">
                                                {{ substr($status, 0, 1) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4">
                                <input type="text" wire:model="studentNotes.{{ $student->id }}"
                                    {{ $isLocked ? 'disabled' : '' }} placeholder="Catatan..."
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div
            class="p-6 bg-gray-50/50 border-t border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="w-full md:w-2/3">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jurnal
                    Mengajar</label>
                <textarea wire:model="notes" {{ $isLocked ? 'disabled' : '' }} placeholder="Materi yang diajarkan hari ini..."
                    rows="2"
                    class="w-full bg-white border border-gray-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all shadow-2xs"></textarea>
            </div>
            <div class="w-full md:w-auto self-end">
                @if (!$isLocked)
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="w-full md:w-auto px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove>Simpan Data Absensi</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
