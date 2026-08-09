<div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 p-4 sm:p-6">
    <!-- Form Tambah Tahun Ajaran -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Tambah Tahun Ajaran</h2>

        @if (session()->has('success'))
            <div
                class="mb-4 p-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-3 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs font-medium">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="store" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode Tahun</label>
                <input type="text" wire:model="year" placeholder="Contoh: 2026/2027"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                @error('year')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Semester</label>
                <select wire:model="semester"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
                    <option value="">-- Pilih Semester --</option>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
                @error('semester')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="store">Simpan Tahun Ajaran</span>
                <span wire:loading wire:target="store" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Menyimpan...
                </span>
            </button>
        </form>
    </div>

    <!-- Data Master Tahun Ajaran Table -->
    <div class="md:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-4 bg-gray-50/50 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Data Master Tahun Ajaran</h2>
        </div>

        <!-- Wrapper overflow-x-auto untuk perlindungan responsif tabel di mobile -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[480px] sm:min-w-full">
                <thead>
                    <tr
                        class="bg-gray-50/30 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Tahun Ajaran / Semester</th>
                        <th class="p-4 w-32 text-center">Status</th>
                        <th class="p-4 w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                    @forelse ($years as $yr)
                        <tr
                            class="hover:bg-gray-50/30 transition-colors {{ $yr->is_active ? 'bg-emerald-50/10' : '' }}">
                            <td class="p-4 font-semibold text-gray-900">
                                {{ $yr->year }} <span class="text-xs text-gray-500 font-medium">-
                                    {{ $yr->semester }}</span>
                            </td>
                            <td class="p-4 text-center">
                                @if ($yr->is_active)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-lg uppercase tracking-wide">
                                        🟢 Aktif
                                    </span>
                                @else
                                    <button wire:click="activate({{ $yr->id }})" wire:loading.attr="disabled"
                                        wire:confirm="Mengaktifkan tahun ajaran ini akan otomatis me-nonaktifkan tahun ajaran berjalan. Lanjutkan?"
                                        class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 disabled:opacity-50 text-gray-600 text-[10px] font-bold rounded-lg uppercase tracking-wide cursor-pointer transition-colors">
                                        Set Aktif
                                    </button>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if (!$yr->is_active)
                                    <button wire:click="delete({{ $yr->id }})" wire:loading.attr="disabled"
                                        wire:confirm="Yakin ingin menghapus tahun ajaran ini?"
                                        class="text-xs text-rose-600 hover:text-rose-900 font-bold cursor-pointer disabled:opacity-50 transition-colors">
                                        Hapus
                                    </button>
                                @else
                                    <span class="text-xs text-gray-300 cursor-not-allowed">Hapus</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-xs text-gray-400">
                                Belum ada data tahun ajaran. Silakan tambah data baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
