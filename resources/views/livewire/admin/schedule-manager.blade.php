<div class="p-4 sm:p-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ openEdit: @entangle('showEditModal') }">

    <!-- Kolom Kiri: Form Input & Import Jadwal -->
    <div class="space-y-6">

        <!-- 1. FORM TAMBAH JADWAL MANUAL -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
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
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-medium cursor-pointer">
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
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-medium cursor-pointer">
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
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-medium cursor-pointer">
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
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all font-medium cursor-pointer">
                            <option value="">-- Pilih Hari --</option>
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $d)
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

                <!-- INPUT TIME 24-JAM TAMBAH DATA -->
                <div class="grid grid-cols-2 gap-3">

                    <!-- JAM MULAI -->
                    <div class="space-y-1" x-data="{
                        fullTime: @entangle('time_start').live,
                        hh: '',
                        mm: '',
                        init() {
                            this.splitTime(this.fullTime);
                            this.$watch('fullTime', (val) => {
                                if (!val) {
                                    this.hh = '';
                                    this.mm = '';
                                }
                            });
                        },
                        splitTime(val) {
                            if (val && val.includes(':')) {
                                const parts = val.split(':');
                                this.hh = parts[0] || '';
                                this.mm = parts[1] || '';
                            } else {
                                this.hh = '';
                                this.mm = '';
                            }
                        },
                        syncTime() {
                            if (this.hh !== '' || this.mm !== '') {
                                let h = this.hh ? this.hh.padStart(2, '0') : '00';
                                let m = this.mm ? this.mm.padStart(2, '0') : '00';
                                this.fullTime = h + ':' + m;
                            } else {
                                this.fullTime = '';
                            }
                        },
                        handleHH(e) {
                            let val = e.target.value.replace(/\D/g, '').substring(0, 2);
                            if (parseInt(val) > 23) val = '23';
                            this.hh = val;
                            if (val.length === 2) {
                                this.$nextTick(() => {
                                    this.$refs.mmStart.focus();
                                    this.$refs.mmStart.select();
                                });
                                this.syncTime();
                            }
                        },
                        handleMM(e) {
                            let val = e.target.value.replace(/\D/g, '').substring(0, 2);
                            if (parseInt(val) > 59) val = '59';
                            this.mm = val;
                            if (val.length === 2 || val.length === 0) {
                                this.syncTime();
                            }
                        },
                        handleMMKeydown(e) {
                            if (e.key === 'Backspace' && this.mm === '') {
                                this.$nextTick(() => {
                                    this.$refs.hhStart.focus();
                                    this.$refs.hhStart.select();
                                });
                            }
                        },
                        formatOnBlur() {
                            if (this.hh !== '') this.hh = this.hh.padStart(2, '0');
                            if (this.mm !== '') this.mm = this.mm.padStart(2, '0');
                            this.syncTime();
                        }
                    }">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Mulai</label>
                        <div
                            class="flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-100 focus-within:border-blue-400 transition-all">
                            <input type="text" x-ref="hhStart" x-model="hh" @input="handleHH($event)"
                                @blur="formatOnBlur()" placeholder="--" maxlength="2"
                                class="w-8 text-center bg-transparent text-xs font-mono font-bold text-gray-800 outline-none">
                            <span class="font-bold text-gray-400 font-mono px-0.5 select-none">:</span>
                            <input type="text" x-ref="mmStart" x-model="mm" @input="handleMM($event)"
                                @keydown="handleMMKeydown($event)" @blur="formatOnBlur()" placeholder="--"
                                maxlength="2"
                                class="w-8 text-center bg-transparent text-xs font-mono font-bold text-gray-800 outline-none">
                        </div>
                        @error('time_start')
                            <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- JAM SELESAI -->
                    <div class="space-y-1" x-data="{
                        fullTime: @entangle('time_end').live,
                        hh: '',
                        mm: '',
                        init() {
                            this.splitTime(this.fullTime);
                            this.$watch('fullTime', (val) => {
                                if (!val) {
                                    this.hh = '';
                                    this.mm = '';
                                }
                            });
                        },
                        splitTime(val) {
                            if (val && val.includes(':')) {
                                const parts = val.split(':');
                                this.hh = parts[0] || '';
                                this.mm = parts[1] || '';
                            } else {
                                this.hh = '';
                                this.mm = '';
                            }
                        },
                        syncTime() {
                            if (this.hh !== '' || this.mm !== '') {
                                let h = this.hh ? this.hh.padStart(2, '0') : '00';
                                let m = this.mm ? this.mm.padStart(2, '0') : '00';
                                this.fullTime = h + ':' + m;
                            } else {
                                this.fullTime = '';
                            }
                        },
                        handleHH(e) {
                            let val = e.target.value.replace(/\D/g, '').substring(0, 2);
                            if (parseInt(val) > 23) val = '23';
                            this.hh = val;
                            if (val.length === 2) {
                                this.$nextTick(() => {
                                    this.$refs.mmEnd.focus();
                                    this.$refs.mmEnd.select();
                                });
                                this.syncTime();
                            }
                        },
                        handleMM(e) {
                            let val = e.target.value.replace(/\D/g, '').substring(0, 2);
                            if (parseInt(val) > 59) val = '59';
                            this.mm = val;
                            if (val.length === 2 || val.length === 0) {
                                this.syncTime();
                            }
                        },
                        handleMMKeydown(e) {
                            if (e.key === 'Backspace' && this.mm === '') {
                                this.$nextTick(() => {
                                    this.$refs.hhEnd.focus();
                                    this.$refs.hhEnd.select();
                                });
                            }
                        },
                        formatOnBlur() {
                            if (this.hh !== '') this.hh = this.hh.padStart(2, '0');
                            if (this.mm !== '') this.mm = this.mm.padStart(2, '0');
                            this.syncTime();
                        }
                    }">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Selesai</label>
                        <div
                            class="flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-100 focus-within:border-blue-400 transition-all">
                            <input type="text" x-ref="hhEnd" x-model="hh" @input="handleHH($event)"
                                @blur="formatOnBlur()" placeholder="--" maxlength="2"
                                class="w-8 text-center bg-transparent text-xs font-mono font-bold text-gray-800 outline-none">
                            <span class="font-bold text-gray-400 font-mono px-0.5 select-none">:</span>
                            <input type="text" x-ref="mmEnd" x-model="mm" @input="handleMM($event)"
                                @keydown="handleMMKeydown($event)" @blur="formatOnBlur()" placeholder="--"
                                maxlength="2"
                                class="w-8 text-center bg-transparent text-xs font-mono font-bold text-gray-800 outline-none">
                        </div>
                        @error('time_end')
                            <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="w-full mt-2 py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="store">Simpan Jadwal</span>
                    <span wire:loading.flex wire:target="store" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Memproses...</span>
                    </span>
                </button>
            </form>
        </div>

        <!-- 2. WIDGET IMPORT EXCEL JADWAL -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-3">
            <div class="flex justify-between items-center">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Import Jadwal via Excel</h2>
            </div>

            @if (session()->has('success_import'))
                <div
                    class="p-3 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-medium">
                    {{ session('success_import') }}
                </div>
            @endif

            <form wire:submit.prevent="importExcel" class="space-y-3">
                <div>
                    <input type="file" wire:model="excelFile" accept=".xlsx, .xls"
                        class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    @error('excelFile')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="importExcel">Unggah & Proses Excel</span>
                    <span wire:loading.flex wire:target="importExcel" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Mengimpor File...</span>
                    </span>
                </button>
                <button type="button" wire:click="downloadTemplate"
                    class="w-full py-2.5 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs flex items-center justify-center gap-2">
                    Unduh Template
                </button>
            </form>

            @if (!empty($importSummary['details']))
                <div
                    class="mt-3 p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs space-y-1 max-h-48 overflow-y-auto">
                    <p class="font-bold text-rose-800">⚠️ Laporan Gagal Baris (Total: {{ $importSummary['failed'] }}):
                    </p>
                    @foreach ($importSummary['details'] as $fail)
                        <p class="text-rose-700 font-mono text-[11px]">Baris {{ $fail['row'] }}:
                            {{ $fail['reason'] }}</p>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Kolom Kanan: Tabel Penampil Jadwal -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden h-fit">

        <!-- Header & Filter Bar -->
        <div class="p-4 bg-gray-50/60 border-b border-gray-100 space-y-3">
            <div class="flex flex-wrap justify-between items-center gap-2">
                <div class="flex items-center gap-3">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Master Jadwal Pelajaran</h2>

                    @if (count($selectedSchedules) > 0)
                        <button type="button"
                            onclick="confirm('Hapus {{ count($selectedSchedules) }} jadwal yang dipilih?') || event.stopImmediatePropagation()"
                            wire:click="deleteSelected"
                            class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <span>🗑️ Hapus Terpilih</span>
                            <span class="bg-rose-800 text-rose-100 text-[10px] px-1.5 py-0.2 rounded-full font-mono">
                                {{ count($selectedSchedules) }}
                            </span>
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold px-2 py-1 bg-gray-200 text-gray-600 rounded-lg font-mono">
                        Total: {{ $schedules->total() }}
                    </span>

                    <div
                        class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-lg px-2 py-1 text-xs">
                        <span class="text-gray-500 font-medium text-[11px]">Tampilkan:</span>
                        <select wire:model.live="perPage"
                            class="font-bold text-gray-800 bg-transparent outline-none cursor-pointer">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <!-- 1. Filter Kelas -->
                <div>
                    <select wire:model.live="filter_classroom_id"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-blue-100 font-medium cursor-pointer">
                        <option value="">🏫 Semua Kelas</option>
                        @foreach ($classrooms as $class)
                            <option value="{{ $class->id }}">Kelas: {{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Filter Hari -->
                <div>
                    <select wire:model.live="filter_day"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-blue-100 font-medium cursor-pointer">
                        <option value="">📅 Semua Hari</option>
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $d)
                            <option value="{{ $d }}">Hari {{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Filter Guru -->
                <div>
                    <select wire:model.live="filter_teacher_id"
                        class="w-full bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-blue-100 font-medium cursor-pointer">
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
                        <th class="p-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th class="p-4">Jam Pelajaran</th>
                        <th class="p-4">Mata Pelajaran</th>
                        <th class="p-4">Guru Pengajar</th>
                        <th class="p-4 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-700">
                    @php $currentGroupKey = null; @endphp

                    @forelse ($schedules as $sched)
                        @php
                            $groupKey = $sched->day . ' - ' . $sched->classroom->name;
                        @endphp

                        @if ($currentGroupKey !== $groupKey)
                            @php $currentGroupKey = $groupKey; @endphp
                            <tr wire:key="group-{{ $loop->index }}"
                                class="bg-gradient-to-r from-slate-100 via-indigo-50/50 to-transparent border-y border-gray-200/80">
                                <td colspan="5" class="px-4 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-2 py-0.5 bg-indigo-600 text-white rounded-md text-[10px] font-bold uppercase tracking-wider">
                                            📅 {{ $sched->day }}
                                        </span>
                                        <span class="text-xs font-bold text-gray-800 uppercase tracking-wide">
                                            🏫 Kelas: {{ $sched->classroom->name }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        <!-- TAMBAHKAN wire:key DI SINI -->
                        <tr wire:key="schedule-row-{{ $sched->id }}"
                            class="hover:bg-gray-50/30 transition-colors {{ in_array((string) $sched->id, $selectedSchedules) ? 'bg-indigo-50/30' : '' }}">

                            <td class="p-4 text-center">
                                <!-- PASTIKAN ID STRING & MEMILIKI wire:key UNTUK CHECKBOX -->
                                <input type="checkbox" wire:key="check-{{ $sched->id }}"
                                    wire:model.live="selectedSchedules" value="{{ $sched->id }}"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </td>

                            <td class="p-4">
                                <span class="font-bold text-gray-900 block">Jam Ke-{{ $sched->period_start }} s/d
                                    {{ $sched->period_end }}</span>
                                <span class="text-gray-400 font-mono text-[11px]">
                                    ({{ \Carbon\Carbon::parse($sched->time_start)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($sched->time_end)->format('H:i') }})
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-gray-900 block">{{ $sched->subject->name }}</span>
                                <span
                                    class="text-gray-400 text-[10px] bg-gray-100 px-1.5 py-0.5 rounded font-mono">{{ $sched->subject->code }}</span>
                            </td>
                            <td class="p-4 text-gray-600 font-medium">{{ $sched->teacher->name }}</td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit({{ $sched->id }})"
                                        class="px-2 py-1 bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-600 rounded-lg font-bold transition-all cursor-pointer">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $sched->id }})" wire:loading.attr="disabled"
                                        wire:confirm="Hapus jadwal ini? Data absensi yang terikat pada jadwal ini juga berpotensi ikut terpengaruh."
                                        class="px-2 py-1 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 rounded-lg font-bold transition-all cursor-pointer">
                                        Hapus
                                    </button>
                                </div>
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

        <!-- LINK PAGINATION -->
        @if ($schedules->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/30">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL EDIT JADWAL PELAJARAN -->
    <div x-show="openEdit" x-cloak
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4"
        @keydown.escape.window="openEdit = false">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl w-full max-w-lg overflow-hidden space-y-4 p-5"
            @click.away="$wire.closeEditModal()">

            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">✏️ Edit Jadwal Pelajaran</h3>
                <button type="button" wire:click="closeEditModal"
                    class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>

            <form wire:submit.prevent="update" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelas</label>
                    <select wire:model="edit_classroom_id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all font-medium cursor-pointer">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($classrooms as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('edit_classroom_id')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mata Pelajaran</label>
                    <select wire:model="edit_subject_id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all font-medium cursor-pointer">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach ($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                        @endforeach
                    </select>
                    @error('edit_subject_id')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Guru Pengajar</label>
                    <select wire:model="edit_teacher_id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all font-medium cursor-pointer">
                        <option value="">-- Pilih Guru --</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('edit_teacher_id')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hari</label>
                        <select wire:model="edit_day"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all font-medium cursor-pointer">
                            <option value="">-- Pilih Hari --</option>
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                        @error('edit_day')
                            <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Ke</label>
                            <input type="number" wire:model="edit_period_start" placeholder="1"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-2 py-2 text-xs text-center focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">S/D</label>
                            <input type="number" wire:model="edit_period_end" placeholder="2"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-2 py-2 text-xs text-center focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all font-mono">
                        </div>
                    </div>
                </div>

                <!-- MASKED TIME INPUT 24-JAM DILENGKAPI INIT DARI PROPERTI EDIT -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- JAM MULAI EDIT -->
                    <div class="space-y-1" x-data="{
                        fullTime: @entangle('edit_time_start').live,
                        hh: '',
                        mm: '',
                        init() {
                            this.splitTime(this.fullTime);
                            this.$watch('fullTime', (val) => this.splitTime(val));
                        },
                        splitTime(val) {
                            if (val && val.includes(':')) {
                                const parts = val.split(':');
                                this.hh = parts[0] || '';
                                this.mm = parts[1] || '';
                            } else {
                                this.hh = '';
                                this.mm = '';
                            }
                        },
                        syncTime() {
                            if (this.hh !== '' || this.mm !== '') {
                                let h = this.hh ? this.hh.padStart(2, '0') : '00';
                                let m = this.mm ? this.mm.padStart(2, '0') : '00';
                                this.fullTime = h + ':' + m;
                            } else {
                                this.fullTime = '';
                            }
                        },
                        handleHH(e) {
                            let val = e.target.value.replace(/\D/g, '').substring(0, 2);
                            if (parseInt(val) > 23) val = '23';
                            this.hh = val;
                            if (val.length === 2) {
                                this.$nextTick(() => {
                                    this.$refs.editMmStart.focus();
                                    this.$refs.editMmStart.select();
                                });
                                this.syncTime();
                            }
                        },
                        handleMM(e) {
                            let val = e.target.value.replace(/\D/g, '').substring(0, 2);
                            if (parseInt(val) > 59) val = '59';
                            this.mm = val;
                            if (val.length === 2 || val.length === 0) {
                                this.syncTime();
                            }
                        },
                        handleMMKeydown(e) {
                            if (e.key === 'Backspace' && this.mm === '') {
                                this.$nextTick(() => {
                                    this.$refs.editHhStart.focus();
                                    this.$refs.editHhStart.select();
                                });
                            }
                        },
                        formatOnBlur() {
                            if (this.hh !== '') this.hh = this.hh.padStart(2, '0');
                            if (this.mm !== '') this.mm = this.mm.padStart(2, '0');
                            this.syncTime();
                        }
                    }">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Mulai</label>
                        <div
                            class="flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-400 transition-all">
                            <input type="text" x-ref="editHhStart" x-model="hh" @input="handleHH($event)"
                                @blur="formatOnBlur()" placeholder="--" maxlength="2"
                                class="w-8 text-center bg-transparent text-xs font-mono font-bold text-gray-800 outline-none">
                            <span class="font-bold text-gray-400 font-mono px-0.5 select-none">:</span>
                            <input type="text" x-ref="editMmStart" x-model="mm" @input="handleMM($event)"
                                @keydown="handleMMKeydown($event)" @blur="formatOnBlur()" placeholder="--"
                                maxlength="2"
                                class="w-8 text-center bg-transparent text-xs font-mono font-bold text-gray-800 outline-none">
                        </div>
                        @error('edit_time_start')
                            <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- JAM SELESAI EDIT -->
                    <div class="space-y-1" x-data="{
                        fullTime: @entangle('edit_time_end').live,
                        hh: '',
                        mm: '',
                        init() {
                            this.splitTime(this.fullTime);
                            this.$watch('fullTime', (val) => this.splitTime(val));
                        },
                        splitTime(val) {
                            if (val && val.includes(':')) {
                                const parts = val.split(':');
                                this.hh = parts[0] || '';
                                this.mm = parts[1] || '';
                            } else {
                                this.hh = '';
                                this.mm = '';
                            }
                        },
                        syncTime() {
                            if (this.hh !== '' || this.mm !== '') {
                                let h = this.hh ? this.hh.padStart(2, '0') : '00';
                                let m = this.mm ? this.mm.padStart(2, '0') : '00';
                                this.fullTime = h + ':' + m;
                            } else {
                                this.fullTime = '';
                            }
                        },
                        handleHH(e) {
                            let val = e.target.value.replace(/\D/g, '').substring(0, 2);
                            if (parseInt(val) > 23) val = '23';
                            this.hh = val;
                            if (val.length === 2) {
                                this.$nextTick(() => {
                                    this.$refs.editMmEnd.focus();
                                    this.$refs.editMmEnd.select();
                                });
                                this.syncTime();
                            }
                        },
                        handleMM(e) {
                            let val = e.target.value.replace(/\D/g, '').substring(0, 2);
                            if (parseInt(val) > 59) val = '59';
                            this.mm = val;
                            if (val.length === 2 || val.length === 0) {
                                this.syncTime();
                            }
                        },
                        handleMMKeydown(e) {
                            if (e.key === 'Backspace' && this.mm === '') {
                                this.$nextTick(() => {
                                    this.$refs.editHhEnd.focus();
                                    this.$refs.editHhEnd.select();
                                });
                            }
                        },
                        formatOnBlur() {
                            if (this.hh !== '') this.hh = this.hh.padStart(2, '0');
                            if (this.mm !== '') this.mm = this.mm.padStart(2, '0');
                            this.syncTime();
                        }
                    }">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Selesai</label>
                        <div
                            class="flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-400 transition-all">
                            <input type="text" x-ref="editHhEnd" x-model="hh" @input="handleHH($event)"
                                @blur="formatOnBlur()" placeholder="--" maxlength="2"
                                class="w-8 text-center bg-transparent text-xs font-mono font-bold text-gray-800 outline-none">
                            <span class="font-bold text-gray-400 font-mono px-0.5 select-none">:</span>
                            <input type="text" x-ref="editMmEnd" x-model="mm" @input="handleMM($event)"
                                @keydown="handleMMKeydown($event)" @blur="formatOnBlur()" placeholder="--"
                                maxlength="2"
                                class="w-8 text-center bg-transparent text-xs font-mono font-bold text-gray-800 outline-none">
                        </div>
                        @error('edit_time_end')
                            <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
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
