<div class="p-4 sm:p-6 max-w-4xl mx-auto space-y-6">

    <!-- HEADER PANEL -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
        <h1 class="text-lg sm:text-xl font-bold text-gray-900">Pusat Guru Piket / Infal</h1>
        <p class="text-xs text-gray-500 mt-1">
            Daftar kelas hari ini yang belum melakukan presensi. Anda dapat mengambil alih sebagai guru pengganti.
        </p>
    </div>

    <!-- FLASH NOTIFICATION -->
    @if (session()->has('success'))
        <div
            class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold shadow-2xs">
            🟢 {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold shadow-2xs">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <!-- DAFTAR JADWAL TERSEDIA FOR INFAL -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-4 bg-gray-50/50 border-b border-gray-100">
            <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                Jadwal Tersedia untuk Digantikan
            </h2>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse ($availableSchedules as $schedule)
                <div
                    class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-gray-50/40 transition-colors">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex flex-col items-center justify-center font-bold border border-indigo-100 text-xs shrink-0 font-mono">
                            <span class="text-[9px] uppercase leading-none opacity-70">Jam</span>
                            <span
                                class="text-sm leading-none mt-0.5">{{ $schedule->period_start }}-{{ $schedule->period_end }}</span>
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 text-sm">{{ $schedule->subject->name }}</span>
                                <span
                                    class="px-2 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-bold rounded border border-gray-200 uppercase">
                                    {{ $schedule->classroom->name }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                Guru Utama: <span
                                    class="text-gray-600 font-medium">{{ $schedule->teacher->name }}</span>
                                • Waktu: <span
                                    class="font-mono">{{ \Carbon\Carbon::parse($schedule->time_start)->format('H:i') }}
                                    WIB</span>
                            </p>
                        </div>
                    </div>

                    <button wire:click="takeOver({{ $schedule->id }})" wire:loading.attr="disabled"
                        wire:target="takeOver({{ $schedule->id }})"
                        class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer flex items-center justify-center gap-2 shrink-0">
                        <span wire:loading.remove wire:target="takeOver({{ $schedule->id }})"
                            class="flex items-center gap-1">
                            <span>📝</span> Absen sebagai Pengganti
                        </span>
                        <span wire:loading.flex wire:target="takeOver({{ $schedule->id }})"
                            class="flex items-center gap-2">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Mengambil Alih...
                        </span>
                    </button>
                </div>
            @empty
                <div class="bg-emerald-50/60 p-8 text-center text-emerald-800">
                    <span class="text-2xl block mb-1">✅</span>
                    <p class="text-sm font-medium">Luar biasa! Semua jadwal pelajaran hari ini telah terisi absensinya.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

</div>
