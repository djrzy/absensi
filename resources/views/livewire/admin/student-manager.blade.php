<div class="p-4 sm:p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- KOLOM KIRI: Form Input & Upload Excel -->
    <div class="space-y-6">

        <!-- FORM INPUT MANUAL -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">
                {{ $isEditMode ? 'Edit Data Murid' : 'Pendaftaran Murid Baru' }}
            </h2>

            @if (session()->has('success'))
                <div
                    class="mb-4 p-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Lengkap Murid</label>
                    <input type="text" wire:model="name" placeholder="Misal: Andi Wijaya"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                    @error('name')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NISN</label>
                    <input type="text" wire:model="nisn" placeholder="Misal: 008392102"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all font-mono">
                    @error('nisn')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Kelamin</label>
                    <select wire:model="gender"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    @error('gender')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Penempatan Kelas
                        (Opsional)</label>
                    <select wire:model="classroom_id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
                        <option value="">-- Belum Ada Kelas --</option>
                        @foreach ($classrooms as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all flex items-center justify-center gap-2">
                        <span wire:loading.remove
                            wire:target="store">{{ $isEditMode ? 'Update Data' : 'Simpan Murid' }}</span>
                        <span wire:loading.flex wire:target="store" class="flex items-center gap-1.5">
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
                    @if ($isEditMode)
                        <button type="button" wire:click="resetInputFields"
                            class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition-all cursor-pointer">
                            Batal
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- FORM UPLOAD IMPOR EXCEL -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit space-y-4">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Impor Massal via Excel</h2>

            @if (session()->has('success_import'))
                <div
                    class="p-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-medium">
                    🟢 {{ session('success_import') }}
                </div>
            @endif

            <form wire:submit.prevent="importExcel" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">File Excel (.xlsx /
                        .xls)</label>
                    <input type="file" wire:model="excelFile" accept=".xlsx, .xls"
                        class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    @error('excelFile')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all flex justify-center items-center gap-2">
                    <span wire:loading.remove wire:target="importExcel">📥 Unggah & Impor Data</span>
                    <span wire:loading.flex wire:target="importExcel" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Memproses Data Excel...
                    </span>
                </button>
            </form>
        </div>

        <!-- LAPORAN DETAIL IMPOR -->
        @if ($importSummary)
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-3">
                <h3 class="text-xs font-bold uppercase text-gray-900 tracking-wider">Laporan Hasil Impor</h3>

                <div class="grid grid-cols-2 gap-2 text-center text-xs font-bold">
                    <div class="p-3 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-100">
                        <span class="block text-lg font-mono font-black">{{ $importSummary['success'] }}</span>
                        Sukses Diimpor
                    </div>
                    <div class="p-3 bg-rose-50 text-rose-800 rounded-xl border border-rose-100">
                        <span class="block text-lg font-mono font-black">{{ $importSummary['failed'] }}</span>
                        Dilewati / Gagal
                    </div>
                </div>

                @if (!empty($importSummary['details']))
                    <div class="mt-3">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Rincian Baris Yang
                            Dilewati:</label>
                        <div
                            class="max-h-44 overflow-y-auto divide-y divide-gray-100 bg-gray-50 rounded-xl p-2.5 text-[11px]">
                            @foreach ($importSummary['details'] as $detail)
                                <div class="py-1.5 flex justify-between items-center gap-2">
                                    <div class="min-w-0">
                                        <strong class="text-gray-800">Baris {{ $detail['row'] }}:</strong>
                                        {{ $detail['name'] }}
                                        <span class="text-gray-400 font-mono">({{ $detail['nisn'] }})</span>
                                    </div>
                                    <span
                                        class="text-rose-600 font-semibold text-[10px] shrink-0">{{ $detail['reason'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>

    <!-- KOLOM KANAN: Tabel Data Siswa -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden h-fit">

        <div class="p-4 bg-gray-50/60 border-b border-gray-100 flex flex-wrap justify-between items-center gap-3">
            <a href="/admin/penetapan-kelas"
                class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold transition-all flex items-center gap-1 shrink-0">
                <span>📌 Penetapan Massal →</span>
            </a>

            <!-- BILAH AKSI HAPUS MASSAL -->
            @if (count($selectedStudents) > 0)
                <button wire:click="deleteSelected"
                    wire:confirm="Yakin ingin menghapus {{ count($selectedStudents) }} murid yang dipilih secara permanen?"
                    class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span>🗑️</span> Hapus {{ count($selectedStudents) }} Murid Terpilih
                </button>
            @endif
        </div>

        <!-- Filter Bar + Dynamic Per Page Dropdown -->
        <div class="p-4 bg-gray-50/60 border-b border-gray-100 grid grid-cols-1 sm:grid-cols-4 gap-3 items-center">
            <div class="relative sm:col-span-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari nama / NISN..."
                    class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                <span wire:loading wire:target="search"
                    class="absolute right-3 top-2.5 text-xs text-gray-400 animate-spin">🌀</span>
            </div>

            <div>
                <select wire:model.live="filterClassroom"
                    class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">🏫 Semua Kelas</option>
                    <option value="unassigned">⚠️ Belum Dimasukkan Kelas</option>
                    @foreach ($classrooms as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select wire:model.live="filterGender"
                    class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">👫 Semua Gender</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <!-- DROPDOWN PAGINATE LIMIT -->
            <div>
                <select wire:model.live="perPage"
                    class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="10">📄 10 per Hal</option>
                    <option value="25">📄 25 per Hal</option>
                    <option value="50">📄 50 per Hal</option>
                    <option value="100">📄 100 per Hal</option>
                </select>
            </div>
        </div>

        <!-- NOTIFIKASI BANNER: SELECT ALL MATCHED DATA -->
        @if ($selectAll)
            <div
                class="p-3 bg-indigo-50 border-b border-indigo-100 text-xs text-indigo-900 flex justify-between items-center px-4">
                @if ($selectAllMatches)
                    <span>
                        🎉 Seluruh <strong>{{ count($selectedStudents) }}</strong> data murid terfilter di semua
                        halaman telah terpilih.
                    </span>
                @else
                    <span>
                        📌 <strong>{{ count($selectedStudents) }}</strong> murid di halaman ini terpilih.
                    </span>
                    @if ($totalStudents > count($selectedStudents))
                        <button type="button" wire:click="selectAllFilteredData"
                            class="text-indigo-700 font-bold hover:underline cursor-pointer ml-2">
                            Pilih seluruh {{ $totalStudents }} murid yang terfilter →
                        </button>
                    @endif
                @endif
            </div>
        @endif

        <!-- Tabel Responsif -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                    <tr
                        class="bg-gray-50/30 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th class="p-4">Murid</th>
                        <th class="p-4">Kelas</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                    @forelse($students as $st)
                        <tr
                            class="hover:bg-gray-50/30 transition-colors {{ in_array((string) $st->id, $selectedStudents) ? 'bg-indigo-50/20' : '' }}">
                            <td class="p-4 text-center">
                                <input type="checkbox" wire:model.live="selectedStudents"
                                    value="{{ $st->id }}"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </td>
                            <td class="p-4">
                                <a href="{{ route('admin.students.show', $st->id) }}"
                                    class="font-bold text-gray-900 hover:text-indigo-600 transition-colors block">
                                    {{ $st->name }}
                                </a>
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5">
                                    NISN: {{ $st->nisn }} • {{ $st->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </div>
                            </td>
                            <td class="p-4">
                                @if ($st->classroom)
                                    <span
                                        class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md font-bold text-xs uppercase">
                                        {{ $st->classroom->name }}
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg font-bold text-[10px] uppercase">
                                        ⚠️ Belum Ada Kelas
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center space-x-2 shrink-0">
                                <button wire:click="edit({{ $st->id }})"
                                    class="text-xs text-indigo-600 hover:text-indigo-900 font-bold cursor-pointer">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $st->id }})"
                                    wire:confirm="Yakin ingin menghapus data murid ini?"
                                    class="text-xs text-rose-600 hover:text-rose-900 font-bold cursor-pointer">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400 text-xs">
                                Tidak ditemukan murid dengan kriteria filter tersebut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-gray-50/30 border-t border-gray-100 text-xs">
            {{ $students->links() }}
        </div>
    </div>
</div>
