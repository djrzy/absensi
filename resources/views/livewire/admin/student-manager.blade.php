<div class="p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- KOLOM KIRI: Form Input & Form Upload Excel -->
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
                    <button type="submit"
                        class="flex-1 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all">
                        {{ $isEditMode ? 'Update Data' : 'Simpan Murid' }}
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
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all flex justify-center items-center gap-2">
                    <span wire:loading.remove>📥 Unggah & Impor Data</span>
                    <span wire:loading>Memproses Data Excel...</span>
                </button>
            </form>
        </div>

        <!-- LAPORAN DETAIL IMPOR (POIN D) -->
        @if ($importSummary)
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-3 animate-fade-in">
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
                                    <div>
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

        <div class="p-4 bg-gray-50/60 border-b border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari nama atau NISN..."
                    class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
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
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="bg-gray-50/30 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <th class="p-4">Murid</th>
                    <th class="p-4">Kelas</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                @forelse($students as $st)
                    <tr class="hover:bg-gray-50/30 transition-colors">
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
                        <td class="p-4 text-center space-x-2">
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
                        <td colspan="3" class="p-8 text-center text-gray-400 text-xs">
                            Tidak ditemukan murid dengan kriteria filter tersebut.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 bg-gray-50/30 border-t border-gray-100">
            {{ $students->links() }}
        </div>
    </div>
</div>
