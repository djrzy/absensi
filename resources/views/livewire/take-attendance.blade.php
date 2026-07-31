<!-- Bungkus container paling luar dengan Alpine.js state isDirty -->
<div class="p-4 sm:p-6 max-w-5xl mx-auto space-y-4" x-data="{ isDirty: false }" x-on:change.window="isDirty = true"
    x-on:beforeunload.window="if (isDirty) $event.returnValue = 'Ada perubahan absensi yang belum disimpan! Yakin ingin keluar?'">

    <div class="flex justify-between items-center">
        <a href="/dashboard"
            @click="if (isDirty && !confirm('Ada perubahan absensi yang belum disimpan. Yakin ingin meninggalkan halaman ini?')) $event.preventDefault()"
            class="text-xs font-semibold text-gray-500 hover:text-gray-900 transition-colors">
            ← Kembali ke Dashboard
        </a>

        @if (!$isLocked)
            <!-- SOFT LOCK GUARD: Tombol Kunci dengan Modal Konfirmasi Berwarna Tegas -->
            <button wire:click="lockAttendance"
                wire:confirm="⚠️ KONFIRMASI KUNCI PERMANEN:\n\nSetelah dikunci, data absensi dan bukti foto hari ini TIDAK BISA DIBUAT/DIUBAH LAGI oleh Guru.\n\nApakah Anda yakin seluruh data sudah benar?"
                class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs flex items-center gap-1.5">
                🔒 Kunci Absensi Hari Ini
            </button>
        @else
            <span
                class="px-3 py-1 bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-lg shadow-xs flex items-center gap-1">
                🛑 Status: Terkunci Permanen
            </span>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if ($isLateForAttendance && !$isLocked)
        <div class="p-3 bg-amber-50 border border-amber-200 text-amber-950 rounded-xl text-xs flex items-center gap-2">
            <span>⚠️</span>
            <p><strong>Perhatian Guru:</strong> Waktu pengisian telah melewati 15 menit dari jam mulai jadwal. Silakan
                gunakan opsi status <strong>T (Terlambat)</strong> jika diperlukan.</p>
        </div>
    @endif

    <div
        class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden {{ $isLocked ? 'opacity-70 pointer-events-none' : '' }}">

        <!-- BILAH PINTAS ABSENSI MASSAL & INDIKATOR PROGRESS -->
        @if (!$isLocked)
            <div
                class="p-4 bg-indigo-50/40 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="setAllHadir" @click="isDirty = true"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer">
                        <span>⚡</span> Tandai Semua Hadir
                    </button>

                    <button type="button" wire:click="resetAllStatus" @click="isDirty = true"
                        class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                        🔄 Reset Pilihan
                    </button>
                </div>

                @php
                    $totalStudents = count($students);
                    $filledCount = count(array_filter($attendanceData, fn($val) => !is_null($val)));
                    $unfilledCount = $totalStudents - $filledCount;
                @endphp

                <div class="text-xs font-bold font-mono">
                    @if ($unfilledCount > 0)
                        <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                            ⚠️ Belum Diisi: {{ $unfilledCount }} dari {{ $totalStudents }} Siswa
                        </span>
                    @else
                        <span class="text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            ✅ Seluruh {{ $totalStudents }} Siswa Telah Diabsen
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-max sm:min-w-0">
                <thead>
                    <tr
                        class="bg-gray-50/70 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4 w-12 text-center">No</th>
                        <th class="p-4">Nama Siswa</th>
                        <th class="p-4 w-64 text-center">Status Kehadiran</th>
                        <th class="p-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                    @foreach ($students as $index => $student)
                        @php
                            $currentStatus = $attendanceData[$student->id] ?? null;
                            $isAlpa = $currentStatus === 'Alpa';
                        @endphp
                        <tr
                            class="transition-colors {{ $isAlpa ? 'bg-rose-50/50 hover:bg-rose-50/80' : (isset($inheritedStatuses[$student->id]) && $inheritedStatuses[$student->id] ? 'bg-amber-50/40 hover:bg-amber-50/60' : 'hover:bg-gray-50/30') }}">
                            <td class="p-4 text-center text-gray-400 font-medium">{{ $index + 1 }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-1.5">
                                    <div class="font-semibold text-gray-900">{{ $student->name }}</div>
                                    @if ($isAlpa)
                                        <!-- SOFT GUARD BADGE ALPA -->
                                        <span
                                            class="text-[9px] bg-rose-600 text-white font-bold px-1.5 py-0.5 rounded animate-pulse">
                                            ⚠️ ALPA
                                        </span>
                                    @endif
                                    @if (isset($inheritedStatuses[$student->id]) && $inheritedStatuses[$student->id])
                                        <span
                                            class="text-[10px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded-md font-medium">🔄
                                            Auto</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 font-mono">NISN: {{ $student->nisn }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-center gap-1">
                                    @foreach ([
        'Hadir' => 'peer-checked:bg-emerald-600 peer-checked:text-white',
        'Terlambat' => 'peer-checked:bg-indigo-600 peer-checked:text-white',
        'Sakit' => 'peer-checked:bg-amber-500 peer-checked:text-white',
        'Izin' => 'peer-checked:bg-blue-600 peer-checked:text-white',
        'Alpa' => 'peer-checked:bg-rose-600 peer-checked:text-white',
    ] as $status => $activeClass)
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model.live="attendanceData.{{ $student->id }}"
                                                value="{{ $status }}" {{ $isLocked ? 'disabled' : '' }}
                                                class="sr-only peer">
                                            <span
                                                class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-400 inline-block transition-all hover:bg-gray-50 {{ $activeClass }}">
                                                {{ substr($status, 0, 1) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4">
                                <input type="text" wire:model="studentNotes.{{ $student->id }}"
                                    {{ $isLocked ? 'disabled' : '' }} placeholder="Catatan..."
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- JURNAL MENGAJAR & WIDGET KAMERA -->
        <div class="p-6 bg-gray-50/50 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Jurnal Mengajar</label>
                <textarea wire:model="notes" {{ $isLocked ? 'disabled' : '' }} placeholder="Materi yang diajarkan hari ini..."
                    rows="4"
                    class="w-full bg-white border border-gray-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all shadow-2xs"></textarea>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">
                    📷 Foto Bukti Mengajar Di Kelas <span class="text-rose-600">* (Wajib)</span>
                </label>

                <div class="bg-white p-3 border border-gray-200 rounded-xl space-y-3">
                    <label
                        class="flex items-center justify-center gap-2 w-full py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold cursor-pointer transition-all">
                        <span>📸 Ambil Foto via Kamera HP</span>
                        <input type="file" wire:model="photoProof" accept="image/*" capture="environment"
                            {{ $isLocked ? 'disabled' : '' }} class="hidden">
                    </label>

                    @error('photoProof')
                        <span class="text-xs text-rose-600 block font-semibold">{{ $message }}</span>
                    @enderror

                    <div wire:loading wire:target="photoProof"
                        class="text-xs text-indigo-600 font-bold flex items-center gap-2">
                        <span class="animate-spin">⏳</span> Memproses foto dari kamera...
                    </div>

                    @if ($photoProof)
                        <div class="space-y-1">
                            <span class="text-[10px] text-emerald-600 font-bold uppercase">✓ Foto Baru Siap
                                Disimpan:</span>
                            <div class="relative w-full h-36 rounded-xl overflow-hidden border border-emerald-200">
                                <img src="{{ $photoProof->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    @elseif ($existingPhoto)
                        <div class="space-y-1">
                            <span class="text-[10px] text-gray-400 font-bold uppercase">Foto Tersimpan:</span>
                            <div class="relative w-full h-36 rounded-xl overflow-hidden border border-gray-200">
                                <img src="{{ asset('storage/' . $existingPhoto) }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tombol Simpan (Reset isDirty saat diklik) -->
            <div class="md:col-span-2 pt-2 flex justify-end">
                @if (!$isLocked)
                    <button wire:click="save" @click="isDirty = false" wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-8 py-3 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove>💾 Simpan Absensi & Foto Bukti</span>
                        <span wire:loading>Menyimpan Data...</span>
                    </button>
                @endif
            </div>

        </div>
    </div>
</div>
