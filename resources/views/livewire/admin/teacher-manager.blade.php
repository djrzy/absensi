<div class="p-4 sm:p-6 max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ openEdit: @entangle('showEditModal') }">

    <!-- FORM REGISTRASI GURU BARU -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Registrasi Guru Baru</h2>

        @if (session()->has('success'))
            <div
                class="mb-4 p-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="store" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Lengkap & Gelar</label>
                <input type="text" wire:model="name" placeholder="Misal: Ahmad Fauzi, S.Pd"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                @error('name')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat Email Resmi</label>
                <input type="email" wire:model="email" placeholder="fauzi@school.id"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                @error('email')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password Awal Login</label>
                <input type="password" wire:model="password" placeholder="Minimal 6 karakter"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                @error('password')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="store">Daftarkan Guru</span>
                <span wire:loading.flex wire:target="store" class="flex items-center gap-2">
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

    <!-- TABEL DAFTAR TENAGA PENGAJAR -->
    <div class="md:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Daftar Tenaga Pengajar</h2>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-200 text-gray-600 rounded-md font-mono">
                Total: {{ count($teachers) }} Guru
            </span>
        </div>

        <!-- Wrapper Responsif Tabel -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[480px]">
                <thead>
                    <tr
                        class="bg-gray-50/30 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Nama Guru</th>
                        <th class="p-4">Email</th>
                        <th class="p-4 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                    @forelse ($teachers as $teacher)
                        <tr wire:key="teacher-row-{{ $teacher->id }}" class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-4 font-bold text-gray-900">{{ $teacher->name }}</td>
                            <td class="p-4 text-xs text-gray-500 font-mono">{{ $teacher->email }}</td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" wire:click="edit({{ $teacher->id }})"
                                        class="px-2 py-1 bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-600 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                        Edit
                                    </button>
                                    <button type="button"
                                        onclick="confirm('Menghapus akun guru ini akan memutus akses login dan mengosongkan status wali kelas terkait. Yakin?') || event.stopImmediatePropagation()"
                                        wire:click="delete({{ $teacher->id }})"
                                        class="px-2 py-1 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-400 text-xs">
                                Belum ada data guru terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL EDIT DATA GURU (TANPA PASS) -->
    <div x-show="openEdit" x-cloak
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4"
        @keydown.escape.window="openEdit = false">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl w-full max-w-md overflow-hidden space-y-4 p-5"
            @click.away="$wire.closeEditModal()">

            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">✏️ Edit Data Guru</h3>
                <button type="button" wire:click="closeEditModal"
                    class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <form wire:submit.prevent="update" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" wire:model="edit_name" placeholder="Misal: Ahmad Fauzi, S.Pd"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all font-medium">
                    @error('edit_name')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat Email Resmi</label>
                    <input type="email" wire:model="edit_email" placeholder="fauzi@school.id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all font-medium">
                    @error('edit_email')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" wire:click="closeEditModal"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="update">Simpan Perubahan</span>
                        <span wire:loading.flex wire:target="update" class="items-center gap-2">
                            <svg class="animate-spin h-3.5 w-3.5 text-white shrink-0" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
