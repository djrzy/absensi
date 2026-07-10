<div class="p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- KOLOM 1: Form Tambah Kelas -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Buat Kelas Baru</h2>

        @if (session()->has('success') && !$selectedClassroomId)
            <div
                class="mb-4 p-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="store" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Kelas</label>
                <input type="text" wire:model="name" placeholder="Misal: X-IPA-1, XI-IPS-2"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all uppercase">
                @error('name')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer transition-all">
                Simpan Kelas
            </button>
        </form>
    </div>

    <!-- KOLOM 2: Daftar Semua Kelas -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden h-fit">
        <div class="p-4 bg-gray-50/50 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Daftar Kelas</h2>
        </div>
        <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto">
            @foreach ($classrooms as $class)
                <div class="p-4 flex justify-between items-center transition-colors cursor-pointer
                            {{ $selectedClassroomId == $class->id ? 'bg-indigo-50/40 border-l-4 border-indigo-600' : 'hover:bg-gray-50/40' }}"
                    wire:click="showStudents({{ $class->id }})">
                    <div>
                        <div class="font-bold text-gray-900 text-sm uppercase">{{ $class->name }}</div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $class->students_count }} Murid Terdaftar</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs text-indigo-600 font-semibold group-hover:underline">Lihat Murid →</span>
                        <button wire:click.stop="delete({{ $class->id }})"
                            wire:confirm="Menghapus kelas ini akan memutuskan status penempatan kelas murid di dalamnya. Yakin?"
                            class="text-xs text-gray-300 hover:text-rose-600 font-bold transition-colors cursor-pointer">
                            🗑️
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- KOLOM 3: Daftar Murid Terdaftar (Dinamis) -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        @if ($selectedClassroom)
            <div class="p-4 bg-gray-900 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider">Kelas: {{ $selectedClassroom->name }}</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Daftar Anggota Kelas Aktif</p>
                </div>
                <span class="px-2.5 py-1 bg-white/10 rounded-full text-xs font-bold font-mono">
                    {{ $selectedClassroom->students->count() }} Anak
                </span>
            </div>

            <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto">
                @if ($selectedClassroom->students->isEmpty())
                    <div class="p-8 text-center text-gray-400 text-xs">
                        Belum ada murid yang dimasukkan ke kelas ini.<br>
                        <a href="/admin/siswa"
                            class="text-indigo-600 font-semibold hover:underline mt-2 inline-block">Input Siswa Baru
                            →</a>
                    </div>
                @else
                    @foreach ($selectedClassroom->students as $index => $student)
                        <div class="p-3.5 flex justify-between items-center hover:bg-gray-50/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-5 h-5 bg-gray-100 rounded-full text-[10px] font-bold text-gray-500 flex items-center justify-center font-mono">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <div class="text-xs font-bold text-gray-900">{{ $student->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono mt-0.5">NISN: {{ $student->nisn }}
                                    </div>
                                </div>
                            </div>
                            <span
                                class="text-[10px] font-bold px-2 py-0.5 rounded-sm {{ $student->gender == 'L' ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        @else
            <!-- State awal ketika belum ada kelas yang diklik -->
            <div class="p-12 text-center text-gray-400 flex flex-col items-center justify-center h-full min-h-[300px]">
                <span class="text-3xl mb-2">🏫</span>
                <p class="text-xs font-medium">Silakan klik salah satu kelas di samping untuk melihat daftar murid yang
                    terdaftar.</p>
            </div>
        @endif
    </div>

</div>
