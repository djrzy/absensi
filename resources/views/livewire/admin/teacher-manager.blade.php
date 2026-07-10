<div class="p-6 max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
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

            <button type="submit"
                class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                Daftarkan Guru
            </button>
        </form>
    </div>

    <div class="md:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-4 bg-gray-50/50 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Daftar Tenaga Pengajar</h2>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="bg-gray-50/30 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <th class="p-4">Nama Guru</th>
                    <th class="p-4">Email</th>
                    <th class="p-4 w-20 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                @foreach ($teachers as $teacher)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-4 font-bold text-gray-900">{{ $teacher->name }}</td>
                        <td class="p-4 text-xs text-gray-500 font-mono">{{ $teacher->email }}</td>
                        <td class="p-4 text-center">
                            <button wire:click="delete({{ $teacher->id }})"
                                wire:confirm="Menghapus akun guru ini akan memutus akses login dan mengosongkan status wali kelas terkait. Yakin?"
                                class="text-xs text-rose-600 hover:text-rose-900 font-bold cursor-pointer">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
