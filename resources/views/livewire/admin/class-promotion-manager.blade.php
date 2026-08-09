<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6">

    <!-- Header Panel -->
    <div
        class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900">Manajemen Pindah Kelas & Kelulusan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Fitur pemindahan massal siswa antar-kelas saat memasuki tahun ajaran
                baru.</p>
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

        <!-- PANEL KIRI: Konfigurasi Aksi Pemindahan -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit space-y-5">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">1. Atur Alur Pemindahan</h2>

            <!-- Pilih Kelas Asal -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Kelas (Asal)</label>
                <select wire:model.live="sourceClassroomId"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all font-semibold">
                    <option value="">-- Pilih Kelas Asal --</option>
                    @foreach ($classrooms as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Jenis Aksi -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Aksi Pemindahan</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="targetAction" value="promote" class="sr-only peer">
                        <span
                            class="block text-center py-2 px-3 rounded-xl border border-gray-200 text-xs font-bold text-gray-500 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all select-none">
                            ↗️ Naik Kelas
                        </span>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="targetAction" value="graduate" class="sr-only peer">
                        <span
                            class="block text-center py-2 px-3 rounded-xl border border-gray-200 text-xs font-bold text-gray-500 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 transition-all select-none">
                            🎓 Luluskan
                        </span>
                    </label>
                </div>
            </div>

            <!-- Pilih Kelas Tujuan (Jika Naik Kelas) -->
            @if ($targetAction === 'promote')
                <div class="animate-fade-in">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ke Kelas (Tujuan)</label>
                    <select wire:model="targetClassroomId"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all font-semibold">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        @foreach ($classrooms as $cls)
                            @if ($cls->id != $sourceClassroomId)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @else
                <div
                    class="p-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-[11px] font-medium">
                    🎓 Siswa yang dipilih akan dinyatakan <strong>LULUS</strong> dan dilepaskan dari daftar kelas aktif.
                </div>
            @endif

            <!-- Tombol Eksekusi -->
            <button wire:click="executePromotion" wire:loading.attr="disabled"
                wire:confirm="Yakin ingin memproses pemindahan/kelulusan untuk siswa yang dicentang?"
                class="w-full py-3 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="executePromotion">🚀 Eksekusi Proses</span>
                <span wire:loading.flex wire:target="executePromotion" class="flex items-center gap-2">
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

        <!-- PANEL KANAN: Daftar Siswa Tercentang (Checklist) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
            <div class="p-4 bg-gray-50/70 border-b border-gray-100 flex justify-between items-center gap-2">
                <div class="flex items-center gap-2">
                    <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider">2. Pilih Siswa yang Diproses
                    </h2>
                    <span wire:loading wire:target="sourceClassroomId, targetAction"
                        class="text-xs text-gray-400 animate-spin">🌀</span>
                </div>

                @if ($sourceStudents->isNotEmpty())
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-indigo-600 select-none">
                        <input type="checkbox" wire:model.live="selectAll"
                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        <span>Pilih Semua Siswa</span>
                    </label>
                @endif
            </div>

            <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto">
                @if (!$sourceClassroomId)
                    <div class="p-12 text-center text-gray-400 text-xs flex flex-col items-center justify-center">
                        <span class="text-3xl mb-2">👈</span>
                        <p>Silakan pilih <strong>Kelas Asal</strong> pada panel sebelah kiri terlebih dahulu.</p>
                    </div>
                @elseif($sourceStudents->isEmpty())
                    <div class="p-12 text-center text-gray-400 text-xs">
                        Tidak ada siswa terdaftar di kelas ini.
                    </div>
                @else
                    @foreach ($sourceStudents as $index => $student)
                        <label
                            class="p-3.5 flex items-center justify-between hover:bg-gray-50/60 transition-colors cursor-pointer select-none">
                            <div class="flex items-center gap-3 min-w-0 pr-2">
                                <input type="checkbox" wire:model.live="selectedStudentIds" value="{{ $student->id }}"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer shrink-0">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-gray-900 truncate">{{ $student->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono mt-0.5 truncate">NISN:
                                        {{ $student->nisn }}</div>
                                </div>
                            </div>
                            <span
                                class="text-[10px] font-bold px-2 py-0.5 rounded-xs shrink-0 {{ $student->gender == 'L' ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </label>
                    @endforeach
                @endif
            </div>

            @if ($sourceStudents->isNotEmpty())
                <div
                    class="p-3 bg-gray-50/30 border-t border-gray-100 text-[11px] text-gray-500 flex justify-between items-center">
                    <span>Terpilih: <strong class="text-gray-900 font-mono">{{ count($selectedStudentIds) }}</strong>
                        dari {{ $sourceStudents->count() }} Siswa</span>
                </div>
            @endif
        </div>

    </div>
</div>
