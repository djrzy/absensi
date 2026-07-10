<div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{ openModal: false, modalData: { student_name: '', date_text: '', status: '', details: [] } }">

    <div x-show="openModal"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs flex items-center justify-center z-50 p-4"
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

    <div
        class="mb-6 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Rekapitulasi Absensi Bulanan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Laporan rekap kehadiran siswa per matriks tanggal dilengkapi
                penelusuran log audit.</p>
        </div>

        <div class="flex flex-wrap gap-3 w-full md:w-auto">
            <select wire:model.live="selectedClassroomId"
                class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
                @foreach ($classrooms as $class)
                    <option value="{{ $class->id }}">Kelas: {{ $class->name }}</option>
                @endforeach
            </select>

            <input type="month" wire:model.live="selectedMonth"
                class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-max">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4 bg-white sticky left-0 z-10 w-48 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">Nama
                            Siswa</th>
                        @foreach ($daysInMonth as $day)
                            <th class="p-2 w-8 text-center border-r border-gray-100/50">{{ $day }}</th>
                        @endforeach
                        <th class="p-2 text-center bg-gray-50 font-bold text-gray-900 border-l border-gray-200">H</th>
                        <th class="p-2 text-center bg-gray-50 font-bold text-gray-900">T</th>
                        <th class="p-2 text-center bg-gray-50 font-bold text-gray-900">S</th>
                        <th class="p-2 text-center bg-gray-50 font-bold text-gray-900">I</th>
                        <th class="p-2 text-center bg-gray-50 font-bold text-gray-900">A</th>
                        <th class="p-4 text-center bg-gray-900 text-white rounded-tr-2xl">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-700">
                    @if (empty($reportData))
                        <tr>
                            <td colspan="{{ count($daysInMonth) + 7 }}" class="p-8 text-center text-gray-400">Tidak ada
                                data untuk ditampilkan.</td>
                        </tr>
                    @else
                        @foreach ($reportData as $studentData)
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td
                                    class="p-4 font-bold text-gray-900 bg-white sticky left-0 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    {{ $studentData['name'] }}
                                </td>

                                @foreach ($studentData['days'] as $day => $dayValue)
                                    @php
                                        $letter = is_array($dayValue) ? $dayValue['letter'] : $dayValue;
                                        $bgClass = match ($letter) {
                                            'H' => 'bg-emerald-500 text-white cursor-pointer hover:scale-110',
                                            'T' => 'bg-indigo-500 text-white cursor-pointer hover:scale-110',
                                            'S' => 'bg-amber-500 text-white cursor-pointer hover:scale-110',
                                            'I' => 'bg-blue-500 text-white cursor-pointer hover:scale-110',
                                            'A' => 'bg-rose-500 text-white cursor-pointer hover:scale-110',
                                            'L' => 'bg-gray-100 text-gray-400',
                                            'O' => 'bg-gray-50 text-gray-300',
                                            default => 'bg-gray-50 text-gray-200',
                                        };
                                    @endphp

                                    <td class="p-1 text-center border border-gray-100">
                                        @if (is_array($dayValue) && count($dayValue['details']) > 0)
                                            <button @click="openModal = true; modalData = {{ json_encode($dayValue) }}"
                                                class="w-6 h-6 rounded-md text-[10px] font-black tracking-tighter shadow-2xs transition-all flex items-center justify-center mx-auto {{ $bgClass }}">
                                                {{ $letter }}
                                            </button>
                                        @else
                                            <div
                                                class="w-6 h-6 rounded-md text-[10px] font-bold mx-auto flex items-center justify-center {{ $bgClass }}">
                                                {{ $letter }}
                                            </div>
                                        @endif
                                    </td>
                                @endforeach

                                <td
                                    class="p-2 text-center font-bold text-emerald-600 border-l border-gray-200 bg-gray-50/30">
                                    {{ $studentData['summary']['Hadir'] }}</td>
                                <td class="p-2 text-center font-bold text-indigo-600 bg-gray-50/30">
                                    {{ $studentData['summary']['Terlambat'] }}</td>
                                <td class="p-2 text-center font-bold text-amber-500 bg-gray-50/30">
                                    {{ $studentData['summary']['Sakit'] }}</td>
                                <td class="p-2 text-center font-bold text-blue-500 bg-gray-50/30">
                                    {{ $studentData['summary']['Izin'] }}</td>
                                <td class="p-2 text-center font-bold text-rose-600 bg-gray-50/30">
                                    {{ $studentData['summary']['Alpa'] }}</td>

                                <td class="p-4 text-center font-bold bg-gray-950 text-white">
                                    {{ $studentData['percentage'] }}%
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
