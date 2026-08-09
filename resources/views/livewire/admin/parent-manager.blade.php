<div class="p-4 sm:p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- KOLOM KIRI: Form Registrasi Akun -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Registrasi Akun Wali</h2>

        @if (session()->has('success') && !$showLinkModal)
            <div
                class="mb-4 p-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="store" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Lengkap Wali</label>
                <input type="text" wire:model="name" placeholder="Misal: Ir. Hermawan"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                @error('name')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Login Wali</label>
                <input type="email" wire:model="email" placeholder="hermawan@gmail.com"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                @error('email')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password Login</label>
                <input type="password" wire:model="password" placeholder="Minimal 6 karakter"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                @error('password')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tautkan Anak Pertama
                    (Opsional)</label>
                <select wire:model="student_id"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach ($allStudents as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} (Kelas
                            {{ $student->classroom->name ?? '-' }})</option>
                    @endforeach
                </select>
                @error('student_id')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="store">Daftarkan Akun Wali</span>
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

    <!-- KOLOM KANAN: Daftar Akun Wali Terdaftar -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden h-fit">

        <!-- Filter Bar (Search + Filter Kelas) -->
        <div class="p-4 bg-gray-50/60 border-b border-gray-100 flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="🔍 Cari nama ortu, email, atau nama anak..."
                    class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                <span wire:loading wire:target="search"
                    class="absolute right-3 top-2.5 text-xs text-gray-400 animate-spin">🌀</span>
            </div>
            <div class="w-full sm:w-48">
                <select wire:model.live="filterClassroom"
                    class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 transition-all font-medium">
                    <option value="">🏫 Semua Kelas</option>
                    @foreach ($classrooms as $cls)
                        <option value="{{ $cls->id }}">Kelas {{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tabel Data Responsif -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr
                        class="bg-gray-50/30 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Akun Orang Tua / Wali</th>
                        <th class="p-4">Anak Yang Diwali (Multi-Anak)</th>
                        <th class="p-4 w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                    @forelse($parents as $parent)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-4 align-top">
                                <div class="font-bold text-gray-900">{{ $parent->name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $parent->email }}</div>
                            </td>
                            <td class="p-4 align-top">
                                <div class="flex flex-wrap gap-2 items-center">
                                    @forelse($parent->students as $child)
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-indigo-50 border border-indigo-100 text-indigo-900 px-2.5 py-1 rounded-xl text-xs font-medium">
                                            <span>👦 <strong>{{ $child->name }}</strong>
                                                ({{ $child->classroom->name ?? 'Belum ada kelas' }})
                                            </span>

                                            <!-- Tombol Lepas Tautan -->
                                            <button
                                                wire:click="unlinkStudent({{ $parent->id }}, {{ $child->id }})"
                                                wire:confirm="Lepaskan tautan anak ini dari akun wali?"
                                                class="text-indigo-400 hover:text-rose-600 font-bold ml-1 cursor-pointer transition-colors p-0.5"
                                                title="Lepaskan Tautan">
                                                &times;
                                            </button>
                                        </span>
                                    @empty
                                        <span class="text-xs text-rose-500 italic">Belum ada anak ditautkan</span>
                                    @endforelse

                                    <!-- Tombol + Tautkan Anak -->
                                    <button wire:click="openLinkModal({{ $parent->id }})"
                                        class="px-2.5 py-1 bg-gray-100 hover:bg-indigo-600 hover:text-white text-indigo-600 rounded-lg text-[11px] font-bold transition-all cursor-pointer">
                                        + Tautkan Anak
                                    </button>
                                </div>
                            </td>
                            <td class="p-4 text-center align-top">
                                <button wire:click="delete({{ $parent->id }})"
                                    wire:confirm="Menghapus akun wali ini akan mencabut seluruh hak akses login laporan ortu. Yakin?"
                                    class="text-xs text-rose-600 hover:text-rose-900 font-bold cursor-pointer transition-colors">
                                    Hapus Akun
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-400 text-xs">
                                Tidak ditemukan akun wali murid dengan kriteria pencarian tersebut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-100 text-xs">
            {{ $parents->links() }}
        </div>
    </div>

    <!-- MODAL POP-UP: TAUTKAN ANAK TAMBAHAN -->
    @if ($showLinkModal)
        <div x-cloak class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div
                class="bg-white rounded-2xl border border-gray-100 shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
                <div class="p-4 bg-gray-900 text-white flex justify-between items-center">
                    <h3 class="text-xs font-bold uppercase tracking-wider">Tautkan Anak ke:
                        {{ $targetParent->name ?? '' }}</h3>
                    <button wire:click="closeLinkModal"
                        class="text-gray-400 hover:text-white font-bold cursor-pointer text-lg">&times;</button>
                </div>

                <div class="p-5 space-y-4">
                    @if (session()->has('modal_error'))
                        <div class="p-3 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs font-medium">
                            {{ session('modal_error') }}
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Siswa / Anak</label>
                        <select wire:model="additional_student_id"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all font-medium">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach ($allStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} (NISN: {{ $student->nisn }}
                                    - Kelas {{ $student->classroom->name ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('additional_student_id')
                            <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeLinkModal"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="button" wire:click="linkStudent" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer flex items-center gap-1.5">
                            <span wire:loading.remove wire:target="linkStudent">Simpan Tautan</span>
                            <span wire:loading wire:target="linkStudent">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
