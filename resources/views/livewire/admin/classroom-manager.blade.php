<div class="p-4 sm:p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- KOLOM 1: Form Tambah / Edit Kelas -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">
            {{ $editingClassroomId ? 'Edit Data Kelas' : 'Buat Kelas Baru' }}
        </h2>

        @if (session()->has('success') && !$selectedClassroomId)
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

        @if (!$editingClassroomId)
            <!-- FORM STORE/INPUT BARU -->
            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Kelas</label>
                    <input type="text" wire:model="name" placeholder="Misal: VII-A, VIII-1, IX-Cemara"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all uppercase">
                    @error('name')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Wali Kelas (Opsional)</label>
                    <select wire:model="teacher_id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
                        <option value="">-- Belum Ditentukan --</option>
                        @foreach ($availableTeachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="store">Simpan Kelas</span>
                    <span wire:loading wire:target="store" class="flex items-center gap-1.5">
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
        @else
            <!-- FORM UPDATE/EDIT -->
            <form wire:submit.prevent="updateClassroom" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Kelas</label>
                    <input type="text" wire:model="edit_name"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all uppercase">
                    @error('edit_name')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Wali Kelas</label>
                    <select wire:model="edit_teacher_id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all">
                        <option value="">-- Belum Ditentukan --</option>
                        @foreach ($availableTeachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('edit_teacher_id')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="updateClassroom">Update Kelas</span>
                        <span wire:loading wire:target="updateClassroom">Updating...</span>
                    </button>
                    <button type="button" wire:click="cancelEdit"
                        class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl cursor-pointer transition-all">
                        Batal
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- KOLOM 2: Daftar Semua Kelas (dengan Pagination) -->
    <div
        class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden flex flex-col justify-between min-h-[350px]">
        <div>
            <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center gap-2">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Daftar Kelas</h2>

                <!-- TOMBOL NAVIGASI KE HALAMAN PENETAPAN KELAS MASSAL -->
                <a href="/admin/penetapan-kelas"
                    class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold transition-all flex items-center gap-1 shrink-0">
                    <span>📌 Penetapan Massal →</span>
                </a>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse ($classrooms as $class)
                    <div class="p-4 flex justify-between items-center transition-colors cursor-pointer select-none
                                {{ $selectedClassroomId == $class->id ? 'bg-indigo-50/40 border-l-4 border-indigo-600' : 'hover:bg-gray-50/40' }}"
                        wire:click="showStudents({{ $class->id }})">

                        <div class="pr-2">
                            <div class="font-bold text-gray-900 text-sm uppercase flex items-center gap-2">
                                {{ $class->name }}
                                <span wire:loading wire:target="showStudents({{ $class->id }})"
                                    class="inline-block animate-spin text-xs text-indigo-600">🌀</span>
                            </div>
                            <div class="text-xs text-indigo-600 font-medium mt-0.5">
                                👨‍🏫 {{ $class->waliKelas->name ?? 'Belum ada Wali Kelas' }}
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $class->students_count }} Murid Terdaftar
                            </p>
                        </div>

                        <div class="flex items-center gap-1 shrink-0">
                            <button wire:click.stop="editClassroom({{ $class->id }})"
                                class="p-2 hover:bg-gray-100 text-indigo-600 rounded-lg text-xs font-bold transition-colors cursor-pointer"
                                title="Edit Wali Kelas">
                                ✏️
                            </button>
                            <button wire:click.stop="delete({{ $class->id }})"
                                wire:confirm="Menghapus kelas ini akan memutuskan status penempatan kelas murid di dalamnya. Yakin?"
                                class="p-2 hover:bg-rose-50 text-gray-300 hover:text-rose-600 rounded-lg text-xs font-bold transition-colors cursor-pointer"
                                title="Hapus Kelas">
                                🗑️
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-xs">
                        Belum ada kelas yang dibuat.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Link Pagination Kelas -->
        <div class="p-3 bg-gray-50/30 border-t border-gray-100 text-xs">
            {{ $classrooms->links() }}
        </div>
    </div>

    <!-- KOLOM 3: Detail Murid (dengan Pagination) -->
    <div
        class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden flex flex-col justify-between min-h-[350px]">
        @if ($selectedClassroom)
            <div>
                <div class="p-4 bg-gray-900 text-white flex justify-between items-center gap-2">
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold uppercase tracking-wider truncate">Kelas:
                            {{ $selectedClassroom->name }}</h2>
                        <p class="text-[11px] text-gray-300 mt-0.5 truncate">
                            Wali Kelas: <strong
                                class="text-indigo-300">{{ $selectedClassroom->waliKelas->name ?? 'Belum Ditugaskan' }}</strong>
                        </p>
                    </div>
                    <span class="px-2.5 py-1 bg-white/10 rounded-full text-xs font-bold font-mono shrink-0">
                        {{ $students->total() }} Murid
                    </span>
                </div>

                <div class="divide-y divide-gray-50">
                    @if ($students->isEmpty())
                        <div class="p-8 text-center text-gray-400 text-xs">
                            Belum ada murid yang dimasukkan ke kelas ini.<br>
                            <a href="/admin/siswa"
                                class="text-indigo-600 font-semibold hover:underline mt-2 inline-block">Input Siswa Baru
                                →</a>
                        </div>
                    @else
                        @foreach ($students as $index => $student)
                            <div
                                class="p-3.5 flex justify-between items-center hover:bg-gray-50/30 transition-colors gap-2">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span
                                        class="w-5 h-5 bg-gray-100 rounded-full text-[10px] font-bold text-gray-500 flex items-center justify-center font-mono shrink-0">
                                        {{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-gray-900 truncate">{{ $student->name }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">NISN:
                                            {{ $student->nisn }}</div>
                                    </div>
                                </div>
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-xs shrink-0 {{ $student->gender == 'L' ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Link Pagination Murid -->
            @if ($students->isNotEmpty())
                <div class="p-3 bg-gray-50/30 border-t border-gray-100 text-xs">
                    {{ $students->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center text-gray-400 flex flex-col items-center justify-center h-full min-h-[300px]">
                <span class="text-3xl mb-2">🏫</span>
                <p class="text-xs font-medium">Silakan klik salah satu kelas di samping untuk melihat daftar murid &
                    wali kelasnya.</p>
            </div>
        @endif
    </div>

</div>
