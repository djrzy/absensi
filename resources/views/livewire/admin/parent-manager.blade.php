<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6" x-data="{ openModal: @entangle('showLinkModal') }">

    <!-- HEADER TITLE & STATS -->
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900">Manajemen Akun Wali Murid</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola akun login orang tua/wali murid dan penautan data siswa
                (multi-anak).</p>
        </div>
    </div>

    <!-- FLASH SUCCESS NOTIFICATION -->
    @if (session()->has('success'))
        <div
            class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium flex items-center justify-between">
            <span>✅ {{ session('success') }}</span>
        </div>
    @endif

    <!-- MAIN GRID CONTAINER -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ==================== LEFT SIDE CONTAINER: FORM TAMBAH / EDIT AKUN WALI ==================== -->
        <div class="lg:col-span-1 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-4 h-fit">
            <div class="border-b border-gray-100 pb-3 flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>👤</span> {{ $isEditMode ? 'Edit Akun Wali' : 'Buat Akun Wali Murid' }}
                    </h2>
                    <p class="text-[11px] text-gray-400 mt-0.5">
                        {{ $isEditMode ? 'Perbarui informasi akun wali murid.' : 'Daftarkan akun baru untuk orang tua murid.' }}
                    </p>
                </div>
            </div>

            <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}" class="space-y-4">
                <!-- NAMA WALI -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap
                        Wali:</label>
                    <input type="text" wire:model="name" placeholder="Contoh: Budi Santoso"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                    @error('name')
                        <span class="text-[11px] text-rose-600 block mt-1">⚠️ {{ $message }}</span>
                    @enderror
                </div>

                <!-- EMAIL / USERNAME / NISN -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Email / Username
                        / NISN:</label>
                    <input type="text" wire:model="email" placeholder="wali@sekolah.sch.id atau NISN"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                    @error('email')
                        <span class="text-[11px] text-rose-600 block mt-1">⚠️ {{ $message }}</span>
                    @enderror
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Kata Sandi (Password):
                        @if ($isEditMode)
                            <span class="text-[10px] text-gray-400 font-normal lowercase">(opsional, kosongkan jika tak
                                diubah)</span>
                        @endif
                    </label>
                    <input type="password" wire:model="password" placeholder="••••••••"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                    @error('password')
                        <span class="text-[11px] text-rose-600 block mt-1">⚠️ {{ $message }}</span>
                    @enderror
                </div>

                <!-- TAUTAN ANAK PERTAMA (HANYA MUNCUL SAAT FORM TAMBAH BARU) -->
                @if (!$isEditMode)
                    <div class="space-y-1" x-data="{ open: false }" @click.away="open = false">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                            👦 Tautkan Anak Pertama <span class="text-gray-400 font-normal lowercase">(opsional)</span>:
                        </label>

                        <div class="relative">
                            @if ($selectedStudent)
                                <div
                                    class="w-full bg-indigo-50/70 border border-indigo-200 rounded-xl px-3 py-2 flex items-center justify-between text-xs">
                                    <div class="truncate pr-2">
                                        <span
                                            class="font-bold text-indigo-900 truncate block">{{ $selectedStudent->name }}</span>
                                        <span class="text-[10px] text-indigo-600 font-mono">NISN:
                                            {{ $selectedStudent->nisn ?? '-' }} • Kelas
                                            {{ $selectedStudent->classroom->name ?? '-' }}</span>
                                    </div>
                                    <button type="button" wire:click="clearSelectedStudent"
                                        class="text-indigo-400 hover:text-rose-600 font-bold px-1.5 py-0.5 text-sm cursor-pointer">
                                        &times;
                                    </button>
                                </div>
                            @else
                                <button type="button" @click="open = !open"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2 text-left text-xs text-gray-400 flex items-center justify-between gap-2 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                                    <span>🔍 Klik untuk cari nama siswa...</span>
                                    <span class="text-[10px]" x-text="open ? '▲' : '▼'"></span>
                                </button>

                                <div x-show="open" x-cloak x-transition
                                    class="absolute left-0 right-0 mt-1 bg-white border border-gray-100 rounded-2xl shadow-2xl z-50 overflow-hidden p-2 space-y-2">

                                    <input type="text" wire:model.live.debounce.300ms="searchStudent"
                                        placeholder="Ketik nama atau NISN..."
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all">

                                    <div class="max-h-48 overflow-y-auto divide-y divide-gray-50 text-xs">
                                        @forelse ($searchedStudents as $st)
                                            <div wire:click="selectStudent({{ $st->id }})" @click="open = false"
                                                class="p-2 hover:bg-indigo-50/60 rounded-xl cursor-pointer transition-colors flex items-center justify-between gap-2">
                                                <div class="truncate">
                                                    <div class="text-gray-900 font-semibold truncate">
                                                        {{ $st->name }}</div>
                                                    <div class="text-[10px] text-gray-400 font-mono">NISN:
                                                        {{ $st->nisn }}</div>
                                                </div>
                                                <span
                                                    class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded font-mono shrink-0">
                                                    {{ $st->classroom->name ?? '-' }}
                                                </span>
                                            </div>
                                        @empty
                                            <div class="p-3 text-center text-xs text-gray-400">Siswa tidak ditemukan
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- BUTTONS ACTION -->
                <div class="flex gap-2 pt-1">
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all shadow-xs inline-flex items-center justify-center gap-2 cursor-pointer min-h-[38px]">
                        <span wire:loading.remove wire:target="{{ $isEditMode ? 'update' : 'store' }}">
                            {{ $isEditMode ? '💾 Update Akun' : '💾 Simpan Akun Wali' }}
                        </span>
                        <span wire:loading.flex wire:target="{{ $isEditMode ? 'update' : 'store' }}"
                            class="items-center gap-2">
                            <svg class="animate-spin h-3.5 w-3.5 text-white shrink-0" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>

                    @if ($isEditMode)
                        <button type="button" wire:click="cancelEdit"
                            class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                            Batal
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- ==================== RIGHT SIDE CONTAINER: DAFTAR AKUN WALI & ANAK ==================== -->
        <div class="lg:col-span-2 space-y-4">

            <!-- SEARCH, FILTER, & BULK ACTION BAR -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs space-y-3">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="relative w-full sm:w-auto flex-1">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 text-xs">🔍</span>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari wali murid, email, username, nama anak, NISN..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                    </div>

                    <div class="w-full sm:w-44">
                        <select wire:model.live="filterClassroom"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 outline-none focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                            <option value="">Semua Kelas Anak</option>
                            @foreach ($classrooms as $cls)
                                <option value="{{ $cls->id }}">Kelas {{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-36">
                        <select wire:model.live="perPage"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 outline-none focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                            <option value="10">📄 10 per Hal</option>
                            <option value="25">📄 25 per Hal</option>
                            <option value="50">📄 50 per Hal</option>
                            <option value="100">📄 100 per Hal</option>
                        </select>
                    </div>
                </div>

                <!-- BILAH AKSI HAPUS MASSAL TERPILIH -->
                @if (count($selectedParents) > 0)
                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-700">
                            Terpilih: <strong>{{ count($selectedParents) }}</strong> akun wali
                        </span>

                        <button wire:click="deleteSelected"
                            wire:confirm="Yakin ingin menghapus {{ count($selectedParents) }} akun wali murid beserta seluruh tautan anaknya secara permanen?"
                            class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <span>🗑️</span> Hapus {{ count($selectedParents) }} Akun Terpilih
                        </button>
                    </div>
                @endif
            </div>

            <!-- NOTIFIKASI BANNER: SELECT ALL MATCHED DATA -->
            @if ($selectAll)
                <div
                    class="p-3 bg-indigo-50 border border-indigo-100 text-xs text-indigo-900 rounded-2xl flex justify-between items-center px-4">
                    @if ($selectAllMatches)
                        <span>
                            🎉 Seluruh <strong>{{ count($selectedParents) }}</strong> akun wali terfilter di semua
                            halaman telah terpilih.
                        </span>
                    @else
                        <span>
                            📌 <strong>{{ count($selectedParents) }}</strong> akun wali di halaman ini terpilih.
                        </span>
                        @if ($totalParents > count($selectedParents))
                            <button type="button" wire:click="selectAllFilteredData"
                                class="text-indigo-700 font-bold hover:underline cursor-pointer ml-2">
                                Pilih seluruh {{ $totalParents }} akun wali yang terfilter →
                            </button>
                        @endif
                    @endif
                </div>
            @endif

            <!-- TABEL AKUN WALI MURID -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-max">
                    <thead>
                        <tr
                            class="bg-gray-50/80 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="p-4 w-10 text-center">
                                <input type="checkbox" wire:model.live="selectAll"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </th>
                            <th class="p-4">Akun Wali Murid</th>
                            <th class="p-4">Daftar Anak Terikat</th>
                            <th class="p-4 text-center w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-xs text-gray-700">
                        @forelse ($parents as $parent)
                            <tr
                                class="hover:bg-gray-50/30 transition-colors {{ in_array((string) $parent->id, $selectedParents) ? 'bg-indigo-50/20' : ($selectedParentId == $parent->id ? 'bg-indigo-50/30' : '') }}">
                                <td class="p-4 text-center">
                                    <input type="checkbox" wire:model.live="selectedParents"
                                        value="{{ $parent->id }}"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>

                                <td class="p-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $parent->name }}</div>
                                    <div class="text-gray-400 font-mono text-[11px] mt-0.5">
                                        Username/NISN: <strong
                                            class="text-gray-700">{{ $parent->username ?: '-' }}</strong>
                                    </div>
                                    <div class="text-gray-400 font-mono text-[10px]">{{ $parent->email }}</div>
                                </td>

                                <td class="p-4">
                                    <div class="flex flex-col flex-wrap items-start gap-1.5">
                                        @forelse ($parent->students as $child)
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-900 border border-indigo-100 px-2.5 py-1 rounded-xl text-xs font-medium">
                                                <span>👦 <strong>{{ $child->name }}</strong> (Kelas
                                                    {{ $child->classroom->name ?? '-' }})</span>
                                                <button type="button"
                                                    wire:click="unlinkStudent({{ $parent->id }}, {{ $child->id }})"
                                                    wire:confirm="Yakin ingin melepaskan tautan anak {{ $child->name }} dari akun wali ini?"
                                                    class="text-indigo-400 hover:text-rose-600 font-bold text-sm cursor-pointer ml-1"
                                                    title="Lepas Tautan">&times;</button>
                                            </span>
                                        @empty
                                            <span
                                                class="text-amber-600 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-xl text-[11px] font-semibold">
                                                ⚠️ Belum ada tautan anak
                                            </span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- TOMBOL TAMBAH TAUTAN ANAK -->
                                        <button type="button" wire:click="openLinkModal({{ $parent->id }})"
                                            class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-600 rounded-xl font-bold transition-all flex items-center gap-1 cursor-pointer whitespace-nowrap"
                                            title="Tambah Tautan Anak">
                                            <span>+ Anak</span>
                                        </button>

                                        <!-- TOMBOL EDIT AKUN WALI -->
                                        <button type="button" wire:click="edit({{ $parent->id }})"
                                            class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-600 rounded-xl font-bold transition-all cursor-pointer"
                                            title="Edit Akun Wali">
                                            ✏️
                                        </button>

                                        <!-- TOMBOL HAPUS AKUN WALI -->
                                        <button type="button" wire:click="delete({{ $parent->id }})"
                                            wire:confirm="Yakin ingin menghapus akun wali {{ $parent->name }} beserta seluruh tautannya?"
                                            class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 rounded-xl font-bold transition-all cursor-pointer"
                                            title="Hapus Akun Wali">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400">
                                    Tidak ada data wali murid yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINASI -->
            <div>
                {{ $parents->links() }}
            </div>
        </div>

    </div>

    <!-- ==================== MODAL TAUTKAN ANAK RINGKAS ==================== -->
    <div x-show="openModal" x-cloak
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4"
        @keydown.escape.window="openModal = false">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl w-full max-w-lg overflow-hidden space-y-4 p-5"
            @click.away="$wire.closeLinkModal()">

            <!-- MODAL HEADER -->
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">
                        Tautkan Anak ke Akun: <span class="text-indigo-600">{{ $targetParent->name ?? '' }}</span>
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Cari dan pilih siswa untuk ditambahkan ke akun wali
                        ini.</p>
                </div>
                <button type="button" wire:click="closeLinkModal"
                    class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <!-- ERROR ALERT IN MODAL -->
            @if (session()->has('modal_error'))
                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold">
                    ⚠️ {{ session('modal_error') }}
                </div>
            @endif

            <!-- LIVE SEARCH INPUT INSIDE MODAL -->
            <div class="space-y-3">
                <div class="relative">
                    <span
                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 text-xs">🔍</span>
                    <input type="text" wire:model.live.debounce.300ms="searchModalStudent"
                        placeholder="Ketik nama anak, NISN..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                </div>

                <!-- DAFTAR HASIL SISWA -->
                <div class="max-h-64 overflow-y-auto space-y-2 pr-1">
                    @forelse ($modalSearchedStudents as $st)
                        <div wire:click="linkStudentById({{ $st->id }})"
                            class="p-3 bg-white hover:bg-indigo-50/60 border border-gray-100 hover:border-indigo-200 rounded-xl transition-all cursor-pointer flex justify-between items-center gap-3 group shadow-2xs">

                            <div class="flex items-center gap-2.5 truncate">
                                <div
                                    class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs shrink-0">
                                    👦
                                </div>
                                <div class="truncate">
                                    <div class="text-xs font-bold text-gray-900 group-hover:text-indigo-900 truncate">
                                        {{ $st->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono">NISN: {{ $st->nisn }} • Kelas
                                        {{ $st->classroom->name ?? '-' }}</div>
                                </div>
                            </div>

                            <button type="button"
                                class="px-3 py-1 bg-indigo-50 group-hover:bg-indigo-600 text-indigo-600 group-hover:text-white text-[11px] font-bold rounded-lg transition-all shrink-0">
                                + Tautkan
                            </button>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-gray-400">Siswa tidak ditemukan</div>
                    @endforelse
                </div>
            </div>

            <!-- MODAL FOOTER -->
            <div class="border-t border-gray-100 pt-3 flex justify-end">
                <button type="button" wire:click="closeLinkModal"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
