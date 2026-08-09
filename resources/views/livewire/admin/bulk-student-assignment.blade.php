<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6">

    <!-- Header Banner -->
    <div
        class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900">Penetapan Kelas Massal (Bulk Assignment)</h1>
            <p class="text-xs text-gray-500 mt-0.5">Atur penempatan kelas untuk siswa baru hasil impor Excel atau
                pemindahan kelompok siswa.</p>
        </div>
        <div
            class="w-full sm:w-auto text-center px-4 py-2 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs font-bold font-mono shrink-0">
            ⚠️ {{ $totalUnassigned }} Siswa Belum Memiliki Kelas
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('success'))
        <div
            class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold shadow-2xs">
            🟢 {{ session('success') }}
        </div>
    @endif

    <!-- Alert Error -->
    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold shadow-2xs">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- PANEL KIRI: Eksekusi Penempatan Kelas -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit space-y-5">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">1. Pilih Kelas Tujuan</h2>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pindahkan Ke Kelas</label>
                <select wire:model="targetClassroomId"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">-- Pilih Kelas Tujuan --</option>
                    @foreach ($classrooms as $cls)
                        <option value="{{ $cls->id }}">🏫 {{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl space-y-2 border border-gray-100">
                <div class="text-xs text-gray-500 flex justify-between items-center">
                    <span>Siswa Dicentang:</span>
                    <strong
                        class="text-gray-900 font-mono text-sm bg-white px-2 py-0.5 rounded-lg border border-gray-200">
                        {{ count($selectedStudentIds) }} Orang
                    </strong>
                </div>
            </div>

            <button wire:click="assignStudents" wire:loading.attr="disabled"
                wire:confirm="Yakin ingin memasukkan {{ count($selectedStudentIds) }} siswa yang dicentang ke kelas tujuan?"
                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="assignStudents">🚀 Masukkan Ke Kelas</span>
                <span wire:loading wire:target="assignStudents" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </div>

        <!-- PANEL KANAN: Filter & Checklist Siswa -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">

            <!-- Filter Bar -->
            <div class="p-4 bg-gray-50/70 border-b border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Filter Asal Siswa:</label>
                    <select wire:model.live="filterSourceClassroom"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                        <option value="unassigned">⚠️ Belum Ada Kelas</option>
                        @foreach ($classrooms as $cls)
                            <option value="{{ $cls->id }}">🏫 Dari Kelas {{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Cari Siswa:</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Nama atau NISN..."
                            class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                        <span wire:loading wire:target="search"
                            class="absolute right-3 top-2 text-xs text-gray-400 animate-spin">🌀</span>
                    </div>
                </div>
            </div>

            <!-- Header Checklist -->
            <div
                class="px-4 py-2.5 bg-gray-100/60 border-b border-gray-100 flex justify-between items-center text-xs font-bold text-gray-600">
                <label class="flex items-center gap-2 cursor-pointer text-indigo-600 select-none">
                    <input type="checkbox" wire:model.live="selectAll"
                        class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    <span>Pilih Semua Di Halaman Ini</span>
                </label>
                <span class="text-[11px] text-gray-400">Daftar Siswa Filtered</span>
            </div>

            <!-- List Siswa -->
            <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto">
                @forelse($students as $st)
                    <label
                        class="p-3.5 flex items-center justify-between hover:bg-gray-50/60 transition-colors cursor-pointer select-none">
                        <div class="flex items-center gap-3 min-w-0 pr-2">
                            <input type="checkbox" wire:model.live="selectedStudentIds" value="{{ $st->id }}"
                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer shrink-0">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-gray-900 truncate">{{ $st->name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5 truncate">
                                    NISN: {{ $st->nisn }} • {{ $st->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </div>
                            </div>
                        </div>

                        <span
                            class="text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 {{ $st->classroom ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-800 border border-amber-100' }}">
                            {{ $st->classroom->name ?? 'Belum Ada Kelas' }}
                        </span>
                    </label>
                @empty
                    <div class="p-12 text-center text-gray-400 text-xs">
                        Tidak ada siswa yang sesuai dengan filter pencarian.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="p-3 bg-gray-50/30 border-t border-gray-100 text-xs">
                {{ $students->links() }}
            </div>
        </div>

    </div>
</div>
