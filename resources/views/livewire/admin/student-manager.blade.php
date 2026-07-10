<div class="p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

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
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Penempatan Kelas</label>
                <select wire:model="classroom_id"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($classrooms as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
                @error('classroom_id')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
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
                            <div class="font-bold text-gray-900">{{ $st->name }}</div>
                            <div class="text-[10px] text-gray-400 font-mono mt-0.5">NISN: {{ $st->nisn }} •
                                {{ $st->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                        </td>
                        <td class="p-4">
                            <span
                                class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md font-bold text-xs uppercase">
                                {{ $st->classroom->name ?? '-' }}
                            </span>
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
