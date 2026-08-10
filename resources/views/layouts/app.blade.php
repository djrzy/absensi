<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Modul Absensi Sekolah' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Menghindari efek mengedip (flicker/flash) saat Alpine load -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased font-sans min-h-screen flex flex-col">

    @auth
        <nav
            class="bg-white border-b border-gray-200 py-3 px-4 sm:px-6 flex justify-between items-center shadow-xs sticky top-0 z-40">
            <!-- Brand / Title -->
            <div class="flex items-center gap-3">
                <span class="font-bold text-gray-900 tracking-tight text-sm sm:text-base">Modul Absensi</span>
            </div>

            <!-- User Info & Dropdown Navigation -->
            <div class="flex items-center gap-3 sm:gap-4 text-xs" x-data="{ open: false }">
                <span class="text-gray-500 hidden sm:block">
                    Halo, <strong class="text-gray-900">{{ auth()->user()->name }}</strong>
                </span>

                <!-- Wrapper Dropdown -->
                <div class="relative" @click.away="open = false">
                    <!-- Tombol Pemicu Menu -->
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold px-3 py-1.5 rounded-xl transition-colors cursor-pointer outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <span>🧭 Menu</span>
                        <svg class="w-3 h-3 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <!-- Kotak Menu Dropdown -->
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 sm:w-60 bg-white rounded-2xl border border-gray-100 shadow-xl py-2 z-50 origin-top-right max-h-[80vh] overflow-y-auto">

                        <!-- Salam Mobile -->
                        <div class="px-4 py-2 border-b border-gray-100 sm:hidden">
                            <span class="text-gray-500 text-[11px]">Login sebagai:</span>
                            <p class="font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        </div>

                        <!-- Judul Kelompok Menu -->
                        <div
                            class="px-4 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 my-1">
                            Navigasi {{ auth()->user()->role }}
                        </div>

                        <!-- Tautan Dinamis Sesuai Role -->
                        @if (auth()->user()->role === 'Admin')
                            <a href="/admin/tahun-ajaran"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>📆</span> Tahun Ajaran
                            </a>
                            <a href="/admin/kelas"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>🏫</span> Kelas
                            </a>
                            <a href="/admin/siswa"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>🎒</span> Siswa
                            </a>
                            <a href="/admin/wali-murid"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>👨‍👩‍👧‍👦</span> Wali Murid
                            </a>
                            <a href="/admin/guru"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>👨‍🏫</span> Guru
                            </a>
                            <a href="/admin/mapel"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>📚</span> Mata Pelajaran
                            </a>
                            <a href="/admin/jadwal"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>📅</span> Jadwal Mapel
                            </a>
                            <a href="/admin/kalender"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>🏖️</span> Kalender
                            </a>
                            <a href="/admin/rekap"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>📊</span> Rekap Absen
                            </a>
                            <a href="/admin/pindah-kelas"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>🔄</span> Pindah Kelas / Lulus
                            </a>
                        @elseif (auth()->user()->role === 'Guru')
                            <a href="/dashboard"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-blue-50/60 hover:text-blue-600 font-medium transition-colors">
                                <span>📈</span> Dashboard
                            </a>
                            <a href="/rekap-mapel"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-blue-50/60 hover:text-blue-600 font-medium transition-colors">
                                <span>📚</span> Rekap Presensi Mapel
                            </a>
                            <a href="/rekap-wali-kelas"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-blue-50/60 hover:text-blue-600 font-medium transition-colors">
                                <span>🏫</span> Rekap Wali Kelas
                            </a>
                            <a href="/piket"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-blue-50/60 hover:text-blue-600 font-medium transition-colors">
                                <span>🛡️</span> Pusat Piket
                            </a>
                        @elseif (auth()->user()->role === 'Kepala')
                            <a href="/kepala/dashboard"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-indigo-50/60 hover:text-indigo-600 font-medium transition-colors">
                                <span>📊</span> Dashboard Kepala Sekolah
                            </a>
                        @else
                            <a href="/dashboard"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-blue-50/60 hover:text-blue-600 font-medium transition-colors">
                                <span>📈</span> Dashboard
                            </a>
                        @endif

                        <hr class="my-1 border-gray-100">

                        <!-- Form Logout Resmi Laravel -->
                        <form method="GET" action="{{ route('logout') }}">
                            <button type="submit"
                                class="w-full flex items-center justify-between px-4 py-2 text-rose-600 hover:bg-rose-50 font-bold transition-colors text-left">
                                <span>Logout</span>
                                <span>→</span>
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </nav>
    @endauth

    <!-- Content Area -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

</body>

</html>
