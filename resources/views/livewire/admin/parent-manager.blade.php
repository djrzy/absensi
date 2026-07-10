<div class="p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- KOLOM KIRI: Form Registrasi Akun & Assign Siswa -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Registrasi Akun Wali</h2>

        @if (session()->has('success'))
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
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password Awal Login</label>
                <input type="password" wire:model="password" placeholder="Minimal 6 karakter"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
                @error('password')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- KOMPONEN ASSIGN/TAUTKAN KE MURID -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tautkan ke Murid (Anak)</label>
                <select wire:model="student_id"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach ($allStudents as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} (NISN: {{ $student->nisn }})</option>
                    @endforeach
                </select>
                @error('student_id')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all">
                Daftarkan & Tautkan Wali
            </button>
        </form>
    </div>

    <!-- KOLOM KANAN: Daftar Akun Wali Terdaftar -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden h-fit">

        <!-- Filter Bar -->
        <div class="p-4 bg-gray-50/60 border-b border-gray-100">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari nama atau email wali..."
                class="w-full max-w-md bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all">
        </div>

        <!-- Tabel Data -->
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="bg-gray-50/30 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <th class="p-4">Akun Orang Tua / Wali</th>
                    <th class="p-4">Wali Dari Siswa</th>
                    <th class="p-4 w-24 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                @forelse($parents as $parent)
                    @php
                        // Ambil data nama anak dari tabel jembatan secara manual/query baris
                        $relation = DB::table('student_parents')
                            ->join('students', 'student_parents.student_id', '=', 'students.id')
                            ->join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
                            ->where('student_parents.user_id', $parent->id)
                            ->select('students.name as student_name', 'classrooms.name as class_name')
                            ->first();
                    @endphp
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-4">
                            <div class="font-bold text-gray-900">{{ $parent->name }}</div>
                            <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $parent->email }}</div>
                        </td>
                        <td class="p-4">
                            @if ($relation)
                                <div class="text-xs font-semibold text-gray-800">{{ $relation->student_name }}</div>
                                <div class="text-[10px] text-indigo-600 font-bold uppercase mt-0.5">Kelas:
                                    {{ $relation->class_name }}</div>
                            @else
                                <span class="text-xs text-rose-500 italic">Belum menautkan anak</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <button wire:click="delete({{ $parent->id }})"
                                wire:confirm="Menghapus akun wali ini akan mencabut hak akses login laporan ortu. Yakin?"
                                class="text-xs text-rose-600 hover:text-rose-900 font-bold cursor-pointer">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-400 text-xs">
                            Belum ada data wali murid yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-100">
            {{ $parents->links() }}
        </div>
    </div>
</div>
