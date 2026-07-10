<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Modul Absensi Sekolah</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 antialiased font-sans">

    @auth
        <nav class="bg-white border-b border-gray-100 py-3 px-6 flex justify-between items-center shadow-2xs">
            <div class="flex items-center gap-3">
                <span class="font-bold text-gray-900 tracking-tight text-sm">Modul Absensi</span>
                {{-- <span
                    class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md text-[10px] font-bold uppercase tracking-wider">
                    Role: {{ auth()->user()->role }}
                </span> --}}
            </div>

            <div class="flex items-center gap-4 text-xs" x-data="{ open: false }">
                <span class="text-gray-500 hidden lg:block">Halo, <strong
                        class="text-gray-900">{{ auth()->user()->name }}</strong></span>

                <!-- Wrapper Dropdown (Klik di luar untuk menutup) -->
                <div class="relative" @click.away="open = false">
                    <!-- Tombol Pemicu Menu -->
                    <button @click="open = !open"
                        class="flex items-center gap-1 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold px-3 py-1.5 rounded-xl transition-all cursor-pointer outline-none">
                        <span>🧭 Menu Aplikasi</span>
                        <svg class="w-3 h-3 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <!-- Kotak Menu Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-52 bg-white rounded-2xl border border-gray-100 shadow-xl py-2 z-50 origin-top-right"
                        style="display: none;">

                        <span class="text-gray-500 px-4 py-3 lg:hidden mt-3 block">Halo, <br><strong
                                class="text-gray-900">{{ auth()->user()->name }}</strong></span>

                        <!-- Judul Kelompok Menu -->
                        <div
                            class="px-4 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 mb-1">
                            Navigasi {{ auth()->user()->role }}
                        </div>

                        <!-- Tautan Dinamis Sesuai Role -->
                        @if (auth()->user()->role === 'Admin')
                            <a href="/admin/tahun-ajaran"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50/50 hover:text-indigo-600 font-medium transition-colors">
                                <span>📆</span> Tahun Ajaran
                            </a>
                            <a href="/admin/kelas"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50/50 hover:text-indigo-600 font-medium transition-colors">
                                <span>🏫</span> Kelas
                            </a>
                            <a href="/admin/siswa"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50/50 hover:text-indigo-600 font-medium transition-colors">
                                <span>🎒</span> Siswa
                            </a>
                            <a href="/admin/wali-murid"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50/50 hover:text-indigo-600 font-medium transition-colors">
                                <span>👨‍👩‍👧‍👦</span> Wali Murid
                            </a>
                            <a href="/admin/guru"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50/50 hover:text-indigo-600 font-medium transition-colors">
                                <span>👨‍🏫</span> Guru
                            </a>
                            <a href="/admin/mapel"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50/50 hover:text-indigo-600 font-medium transition-colors">
                                <span>📚</span> Mata Pelajaran
                            </a>
                            <a href="/admin/jadwal"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50/50 hover:text-indigo-600 font-medium transition-colors">
                                <span>📅</span> Jadwal Mapel
                            </a>
                            <a href="/admin/kalender"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50/50 hover:text-indigo-600 font-medium transition-colors">
                                <span>🏖️</span> Kalender
                            </a>
                            <a href="/admin/rekap"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-indigo-50/50 hover:text-indigo-600 font-medium transition-colors">
                                <span>📊</span> Rekap Absen
                            </a>
                        @else
                            <a href="/dashboard"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-blue-50/50 hover:text-blue-600 font-medium transition-colors">
                                <span>📈</span> Dashboard
                            </a>
                            <a href="/piket"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-blue-50/50 hover:text-blue-600 font-medium transition-colors">
                                <span>🛡️</span> Pusat Piket
                            </a>
                        @endif
                        <hr class="my-1 border-gray-200">
                        <a href="{{ route('logout') }}"
                            class="text-rose-600 hover:text-rose-900 font-bold border-l border-gray-200 pl-10 py-4 transition-colors">
                            Logout →
                        </a>
                    </div>
                </div>

            </div>
        </nav>
    @endauth

    <main class="py-4">
        {{ $slot }}
    </main>

</body>

</html>
