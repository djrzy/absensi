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
        @forelse ($todaySchedules as $item)
            @php
                $sch = $item->schedule;
            @endphp
            <div
                class="bg-white rounded-2xl border p-4 sm:p-5 shadow-xs transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 {{ $item->isExpiredUnfilled ? 'border-rose-200 bg-rose-50/20' : 'border-gray-100' }}">
                <div class="flex items-start sm:items-center gap-4">
                    <div
                        class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex flex-col items-center justify-center border border-blue-100 font-bold shrink-0">
                        <span class="text-[10px] leading-none text-blue-500 uppercase">Jam</span>
                        <span
                            class="text-base leading-tight font-mono">{{ $sch->period_start }}-{{ $sch->period_end }}</span>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-gray-900 text-sm sm:text-base">{{ $sch->subject->name }}</h3>

                            <!-- BADGE STATUS -->
                            @if ($item->isExpiredUnfilled)
                                <span
                                    class="px-2 py-0.5 bg-rose-100 text-rose-700 border border-rose-200 text-[10px] font-bold rounded-md">
                                    🛑 TERLEWAT / KEDALUWARSA
                                </span>
                            @elseif ($item->isLockedDb)
                                <span
                                    class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-md">
                                    ✓ Dikunci
                                </span>
                            @elseif ($item->isFilled)
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-md">
                                    📝 Tersimpan
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-x-2.5 gap-y-1 text-xs text-gray-500 mt-1 items-center">
                            <span
                                class="font-semibold text-gray-700 bg-gray-50 px-2 py-0.5 rounded-md border border-gray-100">
                                {{ $sch->classroom->name }}
                            </span>
                            <span>•</span>
                            <span class="font-mono">
                                {{ \Carbon\Carbon::parse($sch->time_start)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($sch->time_end)->format('H:i') }} WIB
                            </span>
                        </div>
                    </div>
                </div>

                <!-- KONTROL TOMBOL -->
                <div class="w-full sm:w-auto text-right">
                    @if ($item->timeStatus === 'UPCOMING')
                        <!-- 1. BELUM MASUK JAM -->
                        <button disabled
                            class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 text-gray-400 text-xs font-bold rounded-xl cursor-not-allowed border border-gray-200 flex items-center justify-center gap-1.5">
                            <span>🔒</span> Belum Waktunya
                        </button>
                    @elseif ($item->isExpiredUnfilled)
                        <!-- 2. JAM SUDAH LEWAT & BELUM DIABSEN (DIBLOKIR TOTAL) -->
                        <button disabled
                            class="w-full sm:w-auto px-4 py-2.5 bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-xl cursor-not-allowed flex items-center justify-center gap-1.5">
                            <span>🚫</span> Waktu Absen Habis
                        </button>
                    @else
                        <!-- 3. JAM AKTIF ATAU SUDAH PERNAH DIABSEN (BOLEH BUKA / EDIT) -->
                        <a href="{{ route('absensi.take', $sch->id) }}" wire:navigate
                            class="w-full sm:w-auto inline-block text-center px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                            {{ $item->isFilled ? 'Lihat / Edit Presensi' : 'Buka Presensi' }}
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center text-gray-400 shadow-xs text-xs">
                Tidak ada jadwal mengajar untuk Anda pada hari ini.
            </div>
        @endforelse
    </div>

</div>
