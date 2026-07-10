<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Pusat Guru Piket / Infal</h1>
        <p class="text-xs text-gray-500 mt-1">Daftar kelas hari ini yang belum melakukan presensi. Anda dapat mengambil
            alih sebagai guru pengganti.</p>
    </div>

    @if ($availableSchedules->isEmpty())
        <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-2xl text-center text-emerald-800 shadow-xs">
            <span class="text-xl">✅</span>
            <p class="text-sm font-medium mt-2">Luar biasa! Semua jadwal pelajaran hari ini telah terisi absensinya.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
            <div class="p-4 bg-gray-50/50 border-b border-gray-100">
                <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Jadwal Tersedia untuk Digantikan
                </h2>
            </div>

            <div class="divide-y divide-gray-50">
                @foreach ($availableSchedules as $schedule)
                    <div
                        class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-gray-50/40 transition-colors">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex flex-col items-center justify-center font-bold border border-indigo-100 text-xs shrink-0">
                                <span>Jam</span>
                                <span
                                    class="text-sm leading-none mt-0.5">{{ $schedule->period_start }}-{{ $schedule->period_end }}</span>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-900 text-sm">{{ $schedule->subject->name }}</span>
                                    <span
                                        class="px-2 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-bold rounded border border-gray-200 uppercase">{{ $schedule->classroom->name }}</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">
                                    Guru Utama: <span
                                        class="text-gray-600 font-medium">{{ $schedule->teacher->name }}</span>
                                    • Waktu: {{ \Carbon\Carbon::parse($schedule->time_start)->format('H:i') }} WIB
                                </p>
                            </div>
                        </div>

                        <button wire:click="takeOver({{ $schedule->id }})"
                            class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer flex items-center justify-center gap-1">
                            <span>📝</span> Absen sebagai Pengganti
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
