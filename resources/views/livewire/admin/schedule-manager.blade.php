<div class="p-4 sm:p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Kolom Kiri: Form Input Jadwal -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs h-fit">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Buat Jadwal Pelajaran</h2>

        @if (session()->has('success'))
            <div
                class="mb-4 p-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="store" class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelas</label>
                <select wire:model="classroom_id"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-medium">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($classrooms as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('classroom_id')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mata Pelajaran</label>
                <select wire:model="subject_id"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-medium">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach ($subjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                    @endforeach
                </select>
                @error('subject_id')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Guru Pengajar</label>
                <select wire:model="teacher_id"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-medium">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hari</label>
                    <select wire:model="day"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-medium">
                        <option value="">-- Pilih Hari --</option>
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                    @error('day')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Ke</label>
                        <input type="number" wire:model="period_start" placeholder="1"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-2 py-2 text-xs text-center focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">S/D</label>
                        <input type="number" wire:model="period_end" placeholder="2"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-2 py-2 text-xs text-center focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-mono">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Mulai</label>
                    <input type="time" wire:model="time_start"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-mono">
                    @error('time_start')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Selesai</label>
                    <input type="time" wire:model="time_end"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-mono">
                    @error('time_end')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="w-full mt-2 py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="store">Simpan Jadwal</span>
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

    <!-- Kolom Kanan: Tabel Penampil Jadwal -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden h-fit">

        <!-- Header & 3-Column Filter Bar -->
        <div class="p-4 bg-gray-50/60 border-b border-gray-100 space-y-3">
            <div class="flex justify-between items-center">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Master Jadwal Pelajaran</h2>
                <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-200 text-gray-600 rounded-md font-mono">
                    Total: {{ $schedules->count() }} Jadwal
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <!-- 1. Filter Kelas -->
                <div>
                    <select wire:model.live="filter_classroom_id"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-blue-100 font-medium">
                        <option value="">🏫 Semua Kelas</option>
                        @foreach ($classrooms as $class)
                            <option value="{{ $class->id }}">Kelas: {{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Filter Hari -->
                <div>
                    <select wire:model.live="filter_day"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-blue-100 font-medium">
                        <option value="">📅 Semua Hari</option>
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $d)
                            <option value="{{ $d }}">Hari {{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Filter Guru -->
                <div>
                    <select wire:model.live="filter_teacher_id"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-blue-100 font-medium">
                        <option value="">👨‍🏫 Semua Guru</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Wrapper Responsif Tabel -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[640px]">
                <thead>
                    <tr
                        class="bg-gray-50/30 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Hari / Jam</th>
                        <th class="p-4">Kelas</th>
                        <th class="p-4">Mata Pelajaran</th>
                        <th class="p-4">Guru</th>
                        <th class="p-4 w-16 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-700">
                    @forelse ($schedules as $sched)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-4">
                                <span class="font-bold text-gray-900 block">{{ $sched->day }}</span>
                                <span class="text-gray-400 font-mono">
                                    Jam {{ $sched->period_start }}-{{ $sched->period_end }}
                                    ({{ substr($sched->time_start, 0, 5) }}-{{ substr($sched->time_end, 0, 5) }})
                                </span>
                            </td>
                            <td class="p-4 font-semibold text-gray-800">
                                <span
                                    class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md font-bold uppercase text-[11px]">
                                    {{ $sched->classroom->name }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-gray-900 block">{{ $sched->subject->name }}</span>
                                <span
                                    class="text-gray-400 text-[10px] bg-gray-100 px-1.5 py-0.5 rounded font-mono">{{ $sched->subject->code }}</span>
                            </td>
                            <td class="p-4 text-gray-600 font-medium">{{ $sched->teacher->name }}</td>
                            <td class="p-4 text-center">
                                <button wire:click="delete({{ $sched->id }})" wire:loading.attr="disabled"
                                    wire:confirm="Hapus jadwal ini? Data absensi yang terikat pada jadwal ini juga berpotensi ikut terpengaruh."
                                    class="text-rose-600 hover:text-rose-900 disabled:opacity-50 font-bold cursor-pointer transition-colors">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">
                                Tidak ditemukan jadwal dengan kombinasi filter tersebut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
