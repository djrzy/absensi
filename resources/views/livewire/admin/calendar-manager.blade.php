<div class="p-4 sm:p-6 max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- FORM SET HARI LIBUR SEKOLAH -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Set Hari Libur Sekolah</h2>

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
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Libur</label>
                <input type="date" wire:model="date"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                @error('date')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Keterangan / Alasan</label>
                <input type="text" wire:model="description" placeholder="Misal: Libur Tahun Baru"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                @error('description')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="store">Simpan Hari Libur</span>
                <span wire:loading wire:target="store" class="flex items-center gap-2">
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
        </form>
    </div>

    <!-- TABEL AGENDA LIBUR -->
    <div class="md:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-4 bg-gray-50/50 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Kalender Agenda Libur</h2>
        </div>

        <!-- Wrapper Responsif Tabel -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[480px]">
                <thead>
                    <tr
                        class="bg-gray-50/30 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4 w-20 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                    @forelse ($holidays as $holiday)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-4 font-mono text-xs font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($holiday->date)->locale('id')->translatedFormat('d M Y') }}
                            </td>
                            <td class="p-4 text-xs text-gray-600">{{ $holiday->description }}</td>
                            <td class="p-4 text-center">
                                <button wire:click="delete({{ $holiday->id }})" wire:loading.attr="disabled"
                                    class="text-xs text-rose-600 hover:text-rose-900 disabled:opacity-50 font-bold cursor-pointer transition-colors">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-400 text-xs">
                                Belum ada agenda libur yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
