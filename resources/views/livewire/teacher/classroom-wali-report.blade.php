<div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{
    openDrawer: false,
    drawerData: { name: '', summary: {}, monthly_breakdown: {} },
    selectedMonthKey: '',
    openModal: false,
    modalData: { student_name: '', date_text: '', status: '', details: [] },
    openPicker: false
}">

    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            nav,
            header,
            sidebar,
            aside,
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                font-size: 9pt !important;
                color: #000 !important;
                font-family: 'Times New Roman', Times, serif !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-only {
                display: block !important;
            }

            .shadow-xs,
            .shadow-2xs,
            .shadow-2xl,
            .shadow-md {
                box-shadow: none !important;
            }

            .border {
                border-color: #000 !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 4px 6px !important;
            }

            th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                font-weight: bold !important;
            }
        }

        .print-only {
            display: none;
        }
    </style>

    <!-- MODAL DETAIL AUDIT TRAIL MAPEL -->
    <div x-show="openModal"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-60 p-4 no-print"
        style="display: none;" @keydown.escape.window="openModal = false">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl w-full max-w-2xl overflow-hidden"
            @click.away="openModal = false">

            <div class="p-5 bg-gray-900 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider" x-text="modalData.student_name"></h3>
                    <p class="text-[11px] text-gray-400 mt-0.5"
                        x-text="'Histori Presensi Hari: ' + modalData.date_text"></p>
                </div>
                <button @click="openModal = false"
                    class="text-gray-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <div class="p-5 max-h-[400px] overflow-y-auto divide-y divide-gray-100">
                <template x-for="(session, index) in modalData.details" :key="index">
                    <div class="py-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-gray-100 rounded-lg text-xs font-bold text-gray-600 flex items-center justify-center font-mono shrink-0"
                                x-text="session.period"></div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900" x-text="session.subject"></h4>
                                <p class="text-[10px] text-gray-400 mt-0.5" x-text="'Waktu Sesi: ' + session.time"></p>

                                <div class="mt-2 flex flex-wrap gap-2 text-[10px]">
                                    <span
                                        class="text-gray-500 bg-gray-50 border border-gray-200/60 px-1.5 py-0.5 rounded">
                                        ✍️ Diinput oleh: <strong class="text-gray-700"
                                            x-text="session.input_by"></strong>
                                    </span>
                                    <span
                                        class="text-gray-500 bg-gray-50 border border-gray-200/60 px-1.5 py-0.5 rounded">
                                        ⏰ Jam input: <strong class="text-gray-700" x-text="session.input_at"></strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span
                                class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider block text-center"
                                :class="{
                                    'bg-emerald-50 text-emerald-700 border border-emerald-200': session
                                        .status === 'Hadir',
                                    'bg-indigo-50 text-indigo-700 border border-indigo-200': session
                                        .status === 'Terlambat',
                                    'bg-amber-50 text-amber-700 border border-amber-200': session.status === 'Sakit',
                                    'bg-blue-50 text-blue-700 border border-blue-200': session.status === 'Izin',
                                    'bg-rose-50 text-rose-700 border border-rose-200': session.status === 'Alpa'
                                }"
                                x-text="session.status">
                            </span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-4 bg-gray-50/70 border-t border-gray-100 flex justify-between items-center text-[11px]">
                <span class="text-gray-500">Status Kumulatif Hari Ini:
                    <strong class="text-gray-900 uppercase" x-text="modalData.status"></strong>
                </span>
                <button @click="openModal = false"
                    class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>

    @if (!$myClassroom)
        <!-- JIKA GURU LOGGED IN BUKAN WALI KELAS -->
        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-xs text-center space-y-3">
            <div class="text-4xl">👨‍🏫</div>
            <h2 class="text-base font-bold text-gray-900">Anda Belum Ditugaskan Sebagai Wali Kelas</h2>
            <p class="text-xs text-gray-500 max-w-md mx-auto">
                Halaman ini khusus untuk Guru yang bertindak sebagai Wali Kelas. Silakan hubungi Administrator Sekolah
                jika Anda merasa ada kesalahan.
            </p>
        </div>
    @else
        <!-- KOP DOKUMEN CETAK (PRINT ONLY) -->
        <div class="print-only mb-6">
            <div class="text-center pb-3 border-b-2 border-black space-y-1 relative">
                <h2 class="text-sm font-bold uppercase tracking-widest">DINAS PENDIDIKAN DAN KEBUDAYAAN</h2>
                <h1 class="text-xl font-extrabold uppercase tracking-wide">SEKOLAH MENENGAH PERTAMA (SMP) NEGERI 1</h1>
                <p class="text-[10pt] italic">Jl. Pendidikan No. 45, Kota Bandung, Jawa Barat • Telp: (022) 7123456</p>
                <div class="border-b-4 border-black mt-1"></div>
            </div>

            <div class="text-center my-4 space-y-1">
                <h3 class="text-base font-bold uppercase underline tracking-wider">LAPORAN REKAPITULASI PRESENSI SISWA
                    (WALI KELAS)</h3>
                <p class="text-[10pt] font-semibold">PERIODE: {{ strtoupper($periodeText) }}</p>
            </div>

            <table class="w-full text-[9pt] mb-4 border-none !border-0">
                <tr class="!border-0">
                    <td class="w-24 font-bold !border-0 p-1">Kelas</td>
                    <td class="w-4 !border-0 p-1">:</td>
                    <td class="!border-0 p-1"><strong>{{ $myClassroom->name }}</strong></td>
                    <td class="w-32 font-bold !border-0 p-1">Tanggal Cetak</td>
                    <td class="w-4 !border-0 p-1">:</td>
                    <td class="!border-0 p-1">{{ now()->locale('id')->translatedFormat('d F Y') }}</td>
                </tr>
                <tr class="!border-0">
                    <td class="font-bold !border-0 p-1">Wali Kelas</td>
                    <td class="!border-0 p-1">:</td>
                    <td class="!border-0 p-1">{{ auth()->user()->name }}</td>
                    <td class="font-bold !border-0 p-1">Tahun Ajaran</td>
                    <td class="!border-0 p-1">:</td>
                    <td class="!border-0 p-1">2026/2027</td>
                </tr>
            </table>
        </div>

        <!-- HEADER BANNER (NO PRINT) -->
        <div
            class="no-print bg-white p-5 rounded-2xl border border-gray-100 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-gray-900">Rekapitulasi Kelas Saya</h1>
                    <span
                        class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-extrabold uppercase border border-emerald-100">
                        🏫 Kelas {{ $myClassroom->name }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">Pemantauan kumulatif kehadiran harian seluruh siswa bimbingan
                    Wali Kelas.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                <!-- CUSTOM MONTH-YEAR RANGE PICKER -->
                <div class="relative">
                    <button @click="openPicker = !openPicker"
                        class="bg-gray-50 hover:bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 flex items-center gap-2 transition-all cursor-pointer shadow-2xs">
                        <span>📅 {{ $periodeText }}</span>
                        <span class="text-[10px] text-gray-400">▼</span>
                    </button>

                    <div x-show="openPicker" @click.away="openPicker = false" x-transition
                        class="absolute right-0 mt-2 w-72 bg-white rounded-2xl border border-gray-100 shadow-2xl p-4 z-50 space-y-3"
                        style="display: none;">
                        <div
                            class="text-xs font-bold text-gray-900 border-b border-gray-100 pb-2 flex justify-between items-center">
                            <span>Pilih Rentang Bulan</span>
                            <button @click="openPicker = false"
                                class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer">&times;</button>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Dari Bulan
                                    (YYYY-MM):</label>
                                <input type="month" wire:model.live.debounce.500ms="startMonth"
                                    pattern="[0-9]{4}-[0-9]{2}" maxlength="7" placeholder="2026-01"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 text-xs font-medium outline-none focus:ring-2 focus:ring-indigo-100 focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Sampai Bulan
                                    (YYYY-MM):</label>
                                <input type="month" wire:model.live.debounce.500ms="endMonth"
                                    pattern="[0-9]{4}-[0-9]{2}" maxlength="7" placeholder="2026-12"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 text-xs font-medium outline-none focus:ring-2 focus:ring-indigo-100 focus:bg-white transition-all">
                            </div>
                        </div>

                        <button @click="openPicker = false"
                            class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer">
                            Terapkan Filter
                        </button>
                    </div>
                </div>

                <button wire:click="exportExcel"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    📊 Excel
                </button>

                <button onclick="window.print()"
                    class="px-3.5 py-2 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    🖨️ Cetak / PDF
                </button>
            </div>
        </div>

        <!-- TABEL REKAPITULASI -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50/80 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4 w-12 text-center">No</th>
                        <th class="p-4">Nama Siswa</th>
                        <th class="p-4 w-28 font-mono text-xs">NISN</th>
                        <th class="p-4 text-center text-emerald-600">Hadir</th>
                        <th class="p-4 text-center text-indigo-600">Telat</th>
                        <th class="p-4 text-center text-amber-600">Sakit</th>
                        <th class="p-4 text-center text-blue-600">Izin</th>
                        <th class="p-4 text-center text-rose-600">Alpa</th>
                        <th class="p-4 text-center">% Kehadiran</th>
                        <th class="p-4 text-center w-28 no-print">Kalender</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-700">
                    @forelse ($reportData as $index => $studentData)
                        <tr class="hover:bg-indigo-50/30 transition-colors cursor-pointer"
                            @click="drawerData = {{ json_encode($studentData) }}; selectedMonthKey = Object.keys(drawerData.monthly_breakdown)[0]; openDrawer = true">
                            <td class="p-4 text-center font-mono font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="p-4 font-bold text-gray-900 text-sm">
                                {{ $studentData['name'] }}
                                @if ($studentData['percentage'] < 75)
                                    <span
                                        class="ml-1.5 text-[10px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-md font-bold no-print">⚠️
                                        Kehadiran Rendah</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono text-gray-400">{{ $studentData['nisn'] }}</td>
                            <td class="p-4 text-center font-bold text-emerald-600">
                                {{ $studentData['summary']['Hadir'] }}</td>
                            <td class="p-4 text-center font-bold text-indigo-600">
                                {{ $studentData['summary']['Terlambat'] }}</td>
                            <td class="p-4 text-center font-bold text-amber-600">
                                {{ $studentData['summary']['Sakit'] }}</td>
                            <td class="p-4 text-center font-bold text-blue-600">{{ $studentData['summary']['Izin'] }}
                            </td>
                            <td class="p-4 text-center font-bold text-rose-600">{{ $studentData['summary']['Alpa'] }}
                            </td>
                            <td class="p-4 text-center">
                                <span
                                    class="px-2.5 py-1 rounded-xl font-bold font-mono text-xs {{ $studentData['percentage'] >= 85 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $studentData['percentage'] }}%
                                </span>
                            </td>
                            <td class="p-4 text-center no-print">
                                <button
                                    class="px-3 py-1 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-600 rounded-lg text-xs font-bold transition-all flex items-center gap-1 mx-auto cursor-pointer">
                                    <span>📅</span> Buka
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-gray-400">Belum ada data presensi harian
                                untuk siswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- KOLOM TANDA TANGAN FORMAL DUA PILAR (PRINT ONLY) -->
        <div class="print-only pt-10">
            <div class="flex justify-between text-[10pt] text-center font-medium">
                <div class="space-y-16">
                    <p>Mengetahui,<br>Kepala SMP Negeri 1 Bandung</p>
                    <p class="font-bold underline uppercase">( Dr. H. Ahmad Sanusi, M.Pd )<br><span
                            class="no-underline font-normal text-[9pt]">NIP. 19750812 200003 1 002</span></p>
                </div>
                <div class="space-y-16">
                    <p>Bandung, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>Wali Kelas
                        {{ $myClassroom->name }}</p>
                    <p class="font-bold underline uppercase">( {{ auth()->user()->name }} )<br><span
                            class="no-underline font-normal text-[9pt]">NIP. -</span></p>
                </div>
            </div>
        </div>

        <!-- SIDE DRAWER PANEL -->
        <div x-show="openDrawer" class="fixed inset-0 z-50 overflow-hidden no-print" style="display: none;">
            <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"
                @click="openDrawer = false"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-lg bg-white shadow-2xl flex flex-col justify-between">

                    <!-- Drawer Header -->
                    <div class="p-5 bg-gray-900 text-white flex justify-between items-center shrink-0">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider" x-text="drawerData.name"></h3>
                            <p class="text-[11px] text-gray-400 mt-0.5">Kalender Presensi Per Bulan</p>
                        </div>
                        <button @click="openDrawer = false"
                            class="text-gray-400 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
                    </div>

                    <!-- Drawer Content -->
                    <div class="p-5 overflow-y-auto space-y-5 flex-1">

                        <!-- DROPDOWN SELECTOR BULAN -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Pilih Bulan Yang
                                Ingin Dilihat:</label>
                            <select x-model="selectedMonthKey"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-indigo-900 outline-none focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                                <template x-for="(monthData, key) in drawerData.monthly_breakdown"
                                    :key="key">
                                    <option :value="key" x-text="monthData.label"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Stats Ringkas -->
                        <div class="grid grid-cols-5 gap-1.5 text-center text-[11px] font-bold">
                            <div class="p-2 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-100">
                                <span class="block text-xs font-mono font-black"
                                    x-text="drawerData.summary ? drawerData.summary.Hadir : 0"></span> Hadir
                            </div>
                            <div class="p-2 bg-indigo-50 text-indigo-800 rounded-xl border border-indigo-100">
                                <span class="block text-xs font-mono font-black"
                                    x-text="drawerData.summary ? drawerData.summary.Terlambat : 0"></span> Telat
                            </div>
                            <div class="p-2 bg-amber-50 text-amber-800 rounded-xl border border-amber-100">
                                <span class="block text-xs font-mono font-black"
                                    x-text="drawerData.summary ? drawerData.summary.Sakit : 0"></span> Sakit
                            </div>
                            <div class="p-2 bg-blue-50 text-blue-800 rounded-xl border border-blue-100">
                                <span class="block text-xs font-mono font-black"
                                    x-text="drawerData.summary ? drawerData.summary.Izin : 0"></span> Izin
                            </div>
                            <div class="p-2 bg-rose-50 text-rose-800 rounded-xl border border-rose-100">
                                <span class="block text-xs font-mono font-black"
                                    x-text="drawerData.summary ? drawerData.summary.Alpa : 0"></span> Alpa
                            </div>
                        </div>

                        <!-- KALENDER GRID PRESISI -->
                        <template
                            x-if="drawerData.monthly_breakdown && drawerData.monthly_breakdown[selectedMonthKey]">
                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider"
                                        x-text="'Matriks: ' + drawerData.monthly_breakdown[selectedMonthKey].label">
                                    </h4>
                                    <span class="text-[10px] text-indigo-600 font-semibold">💡 Klik tanggal untuk
                                        detail mapel</span>
                                </div>

                                <div
                                    class="grid grid-cols-7 gap-2 mb-2 text-center text-[10px] font-bold text-gray-500 uppercase">
                                    <div>Sen</div>
                                    <div>Sel</div>
                                    <div>Rab</div>
                                    <div>Kam</div>
                                    <div>Jum</div>
                                    <div class="text-indigo-600">Sab</div>
                                    <div class="text-rose-600">Min</div>
                                </div>

                                <div class="grid grid-cols-7 gap-2">
                                    <template
                                        x-for="(cell, index) in drawerData.monthly_breakdown[selectedMonthKey].calendar_grid"
                                        :key="index">
                                        <div>
                                            <template x-if="cell.is_empty">
                                                <div
                                                    class="w-full p-2 rounded-xl border border-transparent min-h-[52px]">
                                                </div>
                                            </template>

                                            <template x-if="!cell.is_empty">
                                                <div>
                                                    <template x-if="cell.details && cell.details.length > 0">
                                                        <button @click="openModal = true; modalData = cell"
                                                            class="w-full p-2 rounded-xl text-center border text-xs font-bold flex flex-col items-center justify-center min-h-[52px] transition-all hover:scale-105 shadow-2xs cursor-pointer"
                                                            :class="{
                                                                'bg-emerald-500 text-white border-emerald-600': cell
                                                                    .letter === 'H',
                                                                'bg-indigo-500 text-white border-indigo-600': cell
                                                                    .letter === 'T',
                                                                'bg-amber-500 text-white border-amber-600': cell
                                                                    .letter === 'S',
                                                                'bg-blue-500 text-white border-blue-600': cell
                                                                    .letter === 'I',
                                                                'bg-rose-500 text-white border-rose-600': cell
                                                                    .letter === 'A'
                                                            }">
                                                            <span class="text-[9px] opacity-80"
                                                                x-text="cell.day_num"></span>
                                                            <span class="text-sm font-black font-mono mt-0.5"
                                                                x-text="cell.letter"></span>
                                                        </button>
                                                    </template>

                                                    <template x-if="!cell.details || cell.details.length === 0">
                                                        <div class="w-full p-2 rounded-xl text-center border text-xs font-bold flex flex-col items-center justify-center min-h-[52px]"
                                                            :class="{
                                                                'bg-gray-100 text-gray-400 border-gray-200': cell
                                                                    .letter === 'L' || cell
                                                                    .letter === 'O',
                                                                'bg-gray-50 text-gray-300 border-gray-100': cell
                                                                    .letter === '-'
                                                            }">
                                                            <span class="text-[9px] opacity-60"
                                                                x-text="cell.day_num"></span>
                                                            <span class="text-xs font-bold font-mono mt-0.5"
                                                                x-text="cell.letter"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                    </div>

                    <!-- Drawer Footer -->
                    <div
                        class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center shrink-0 text-xs">
                        <span class="text-gray-500">Pilih bulan di atas untuk berpindah kalender.</span>
                        <button @click="openDrawer = false"
                            class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl cursor-pointer">
                            Tutup Panel
                        </button>
                    </div>

                </div>
            </div>
        </div>

    @endif

</div>
