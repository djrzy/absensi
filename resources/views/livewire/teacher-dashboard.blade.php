<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-8 bg-gray-900 text-white p-6 rounded-2xl shadow-sm relative overflow-hidden">
        <div class="relative z-10">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Selamat Datang Kembali</span>
            <h1 class="text-2xl font-bold mt-1">{{ auth()->user()->name }}</h1>
            <p class="text-sm text-gray-400 mt-2">
                Hari ini adalah <span
                    class="text-white font-medium">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
            </p>
        </div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-gray-800 rounded-full opacity-50 blur-lg"></div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800">Jadwal Mengajar Hari Ini</h2>
        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-md">
            {{ $todaySchedules->count() }} Jadwal Kelas
        </span>
    </div>

    @if ($todaySchedules->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-500 shadow-sm">
            <p class="text-sm">Tidak ada jadwal mengajar untuk Anda pada hari ini.</p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach ($todaySchedules as $schedule)
                <div
                    class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md hover:border-gray-200 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex flex-col items-center justify-center border border-blue-100 font-bold shrink-0">
                            <span class="text-xs leading-none text-blue-500">Jam</span>
                            <span
                                class="text-lg leading-tight">{{ $schedule->period_start }}-{{ $schedule->period_end }}</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">{{ $schedule->subject->name }}</h3>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500 mt-1 items-center">
                                <span
                                    class="font-semibold text-gray-700 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">{{ $schedule->classroom->name }}</span>
                                <span>•</span>
                                <span>{{ \Carbon\Carbon::parse($schedule->time_start)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($schedule->time_end)->format('H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('absensi.take', $schedule->id) }}"
                        class="w-full sm:w-auto text-center px-4 py-2 bg-gray-100 hover:bg-gray-900 hover:text-white text-gray-700 text-xs font-bold rounded-xl transition-all shadow-sm">
                        Buka Presensi
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
