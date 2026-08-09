<div class="p-4 sm:p-6 max-w-4xl mx-auto space-y-6">

    <!-- BANNER WELCOME GURU -->
    <div class="bg-gray-900 text-white p-5 sm:p-6 rounded-2xl shadow-xs relative overflow-hidden">
        <div class="relative z-10">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Selamat Datang Kembali</span>
            <h1 class="text-xl sm:text-2xl font-bold mt-1">{{ auth()->user()->name }}</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-2">
                Hari ini adalah <span
                    class="text-white font-medium">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
            </p>
        </div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-gray-800 rounded-full opacity-50 blur-lg"></div>
    </div>

    <!-- HEADER JADWAL -->
    <div class="flex items-center justify-between">
        <h2 class="text-base sm:text-lg font-bold text-gray-800">Jadwal Mengajar Hari Ini</h2>
        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg font-mono">
            Total: {{ $todaySchedules->count() }} Kelas
        </span>
    </div>

    <!-- DAFTAR JADWAL MENGAJAR -->
    <div class="grid gap-3.5">
        @forelse ($todaySchedules as $schedule)
            <div
                class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-xs hover:shadow-md hover:border-gray-200 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex flex-col items-center justify-center border border-blue-100 font-bold shrink-0">
                        <span class="text-[10px] leading-none text-blue-500 uppercase">Jam</span>
                        <span
                            class="text-base leading-tight font-mono">{{ $schedule->period_start }}-{{ $schedule->period_end }}</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm sm:text-base">{{ $schedule->subject->name }}</h3>
                        <div class="flex flex-wrap gap-x-2.5 gap-y-1 text-xs text-gray-500 mt-1 items-center">
                            <span
                                class="font-semibold text-gray-700 bg-gray-50 px-2 py-0.5 rounded-md border border-gray-100">
                                {{ $schedule->classroom->name }}
                            </span>
                            <span>•</span>
                            <span class="font-mono">
                                {{ \Carbon\Carbon::parse($schedule->time_start)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($schedule->time_end)->format('H:i') }} WIB
                            </span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('absensi.take', $schedule->id) }}" wire:navigate
                    class="w-full sm:w-auto text-center px-4 py-2.5 bg-gray-100 hover:bg-gray-900 hover:text-white text-gray-700 text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                    Buka Presensi
                </a>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center text-gray-400 shadow-xs text-xs">
                Tidak ada jadwal mengajar untuk Anda pada hari ini.
            </div>
        @endforelse
    </div>

</div>
