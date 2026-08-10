<!-- Wrapper Container dengan Alpine.js state isDirty -->
<div class="p-4 sm:p-6 max-w-5xl mx-auto space-y-4" x-data="{ isDirty: false }" x-on:change.window="isDirty = true"
    x-on:beforeunload.window="if (isDirty) $event.returnValue = 'Ada perubahan absensi yang belum disimpan! Yakin ingin keluar?'">

    <!-- HEADER TOP NAVIGATION & ACTION LOCK -->
    <div class="flex justify-between items-center gap-2">
        <a href="/dashboard" wire:navigate
            @click="if (isDirty && !confirm('Ada perubahan absensi yang belum disimpan. Yakin ingin meninggalkan halaman ini?')) $event.preventDefault()"
            class="text-xs font-semibold text-gray-500 hover:text-gray-900 transition-colors flex items-center gap-1">
            <span>←</span> Kembali ke Dashboard
        </a>

        @if (!$isLocked)
            <!-- SOFT LOCK GUARD: Tombol Kunci Permanen -->
            <button wire:click="lockAttendance" wire:loading.attr="disabled"
                wire:confirm="⚠️ KONFIRMASI KUNCI PERMANEN:\n\nSetelah dikunci, data absensi dan bukti foto hari ini TIDAK BISA DIBUAT/DIUBAH LAGI oleh Guru.\n\nApakah Anda yakin seluruh data sudah benar?"
                class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs inline-flex items-center justify-center gap-1.5 min-h-[34px] touch-manipulation">
                <span wire:loading.remove wire:target="lockAttendance" class="inline-flex items-center gap-1">🔒 Kunci
                    Absensi Hari Ini</span>
                <span wire:loading.flex wire:target="lockAttendance" class="items-center gap-1">
                    <svg class="animate-spin h-3.5 w-3.5 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span>Mengunci...</span>
                </span>
            </button>
        @else
            <span
                class="px-3 py-1 bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-lg shadow-xs flex items-center gap-1">
                🛑 Status: Terkunci Permanen
            </span>
        @endif
    </div>

    <!-- FLASH NOTIFICATION -->
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

    <!-- PERINGATAN KETERLAMBATAN GURU -->
    @if ($isLateForAttendance && !$isLocked)
        <div class="p-3 bg-amber-50 border border-amber-200 text-amber-950 rounded-xl text-xs flex items-center gap-2">
            <span class="shrink-0">⚠️</span>
            <p><strong>Perhatian Guru:</strong> Waktu pengisian telah melewati 15 menit dari jam mulai jadwal. Silakan
                gunakan opsi status <strong>T (Terlambat)</strong> jika diperlukan.</p>
        </div>
    @endif

    <!-- MAIN CARD CONTAINER -->
    <div
        class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden {{ $isLocked ? 'opacity-75 pointer-events-none' : '' }}">

        <!-- BILAH PINTAS ABSENSI MASSAL & INDIKATOR PROGRESS -->
        @if (!$isLocked)
            <div
                class="p-4 bg-indigo-50/40 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="setAllHadir" @click="isDirty = true" wire:loading.attr="disabled"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-2xs transition-all inline-flex items-center justify-center gap-1.5 cursor-pointer min-h-[32px] touch-manipulation">
                        <span wire:loading.remove wire:target="setAllHadir" class="inline-flex items-center gap-1">⚡
                            Tandai Semua Hadir</span>
                        <span wire:loading.flex wire:target="setAllHadir" class="items-center gap-1">
                            <svg class="animate-spin h-3.5 w-3.5 text-white shrink-0" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>

                    <button type="button" wire:click="resetAllStatus" @click="isDirty = true"
                        wire:loading.attr="disabled"
                        class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 disabled:opacity-50 text-gray-700 text-xs font-bold rounded-xl transition-all inline-flex items-center justify-center gap-1.5 cursor-pointer min-h-[32px] touch-manipulation">
                        <span wire:loading.remove wire:target="resetAllStatus" class="inline-flex items-center gap-1">🔄
                            Reset Pilihan</span>
                        <span wire:loading.flex wire:target="resetAllStatus" class="items-center gap-1">
                            <svg class="animate-spin h-3.5 w-3.5 text-gray-700 shrink-0" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Mereset...</span>
                        </span>
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

        <!-- TABEL PRESENSI SISWA -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[560px]">
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
                        <tr x-data="{ status: $wire.entangle('attendanceData.{{ $student->id }}') }" class="transition-colors"
                            :class="{
                                'bg-rose-50/50 hover:bg-rose-50/80': status === 'Alpa',
                                'bg-amber-50/40 hover:bg-amber-50/60': '{{ isset($inheritedStatuses[$student->id]) && $inheritedStatuses[$student->id] ? 'true' : 'false' }}'
                                === 'true' && status !== 'Alpa',
                                'hover:bg-gray-50/30': status !== 'Alpa'
                            }">
                            <td class="p-4 text-center text-gray-400 font-medium font-mono">{{ $index + 1 }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-1.5">
                                    <div class="font-semibold text-gray-900">{{ $student->name }}</div>
                                    <template x-if="status === 'Alpa'">
                                        <span
                                            class="text-[9px] bg-rose-600 text-white font-bold px-1.5 py-0.5 rounded animate-pulse">
                                            ⚠️ ALPA
                                        </span>
                                    </template>
                                    @if (isset($inheritedStatuses[$student->id]) && $inheritedStatuses[$student->id])
                                        <span
                                            class="text-[10px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded-md font-medium">
                                            🔄 Auto
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 font-mono">NISN: {{ $student->nisn }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-center gap-1 select-none">
                                    @foreach ([
        'Hadir' => ['letter' => 'H', 'active' => 'bg-emerald-600 text-white border-emerald-600'],
        'Terlambat' => ['letter' => 'T', 'active' => 'bg-indigo-600 text-white border-indigo-600'],
        'Sakit' => ['letter' => 'S', 'active' => 'bg-amber-500 text-white border-amber-500'],
        'Izin' => ['letter' => 'I', 'active' => 'bg-blue-600 text-white border-blue-600'],
        'Alpa' => ['letter' => 'A', 'active' => 'bg-rose-600 text-white border-rose-600'],
    ] as $stKey => $stStyle)
                                        <button type="button"
                                            @click="status = '{{ $stKey }}'; $dispatch('change')"
                                            {{ $isLocked ? 'disabled' : '' }}
                                            class="px-2.5 py-1.5 rounded-lg border text-xs font-bold transition-all cursor-pointer touch-manipulation active:scale-95"
                                            :class="status === '{{ $stKey }}' ? '{{ $stStyle['active'] }} shadow-xs' :
                                                'bg-white border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-gray-600'">
                                            {{ $stStyle['letter'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4">
                                <input type="text" wire:model.defer="studentNotes.{{ $student->id }}"
                                    {{ $isLocked ? 'disabled' : '' }} placeholder="Catatan..."
                                    class="w-full bg-gray-50/50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- JURNAL MENGAJAR & WIDGET KAMERA BUKTI FOTO -->
        <div class="p-5 sm:p-6 bg-gray-50/50 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Jurnal Mengajar</label>
                <textarea wire:model.defer="notes" {{ $isLocked ? 'disabled' : '' }} placeholder="Materi yang diajarkan hari ini..."
                    rows="4"
                    class="w-full bg-white border border-gray-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all shadow-2xs"></textarea>
            </div>

            <!-- KAMERA & UPLOAD FOTO MULTI-DEVICE (LAPTOP: WEBCAM + FILE UPLOAD | MOBILE: CAMERA CAPTURE) -->
            <!-- KAMERA & UPLOAD FOTO MULTI-DEVICE WITH RE-CAPTURE CAPABILITY -->
            <div class="space-y-2" x-data="{
                isMobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 640,
                showWebcam: false,
                isUploading: false,
                uploadProgress: 0,
                stream: null,
            
                // 1. Buka Kamera / Webcam
                async startWebcam() {
                    try {
                        this.showWebcam = true;
                        this.$nextTick(async () => {
                            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                            this.$refs.video.srcObject = this.stream;
                        });
                    } catch (err) {
                        alert('Tidak dapat mengakses webcam laptop. Silakan periksa izin kamera browser atau gunakan opsi Pilih File.');
                        this.showWebcam = false;
                    }
                },
            
                // 2. Tangkap Gambar dari Webcam
                captureWebcam() {
                    const video = this.$refs.video;
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth || 1280;
                    canvas.height = video.videoHeight || 720;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
                    this.stopWebcam();
            
                    canvas.toBlob((blob) => {
                        if (!blob) return;
                        const file = new File([blob], 'webcam-proof.jpg', { type: 'image/jpeg', lastModified: Date.now() });
                        this.uploadFileToLivewire(file);
                    }, 'image/jpeg', 0.75);
                },
            
                // 3. Hentikan Stream Webcam
                stopWebcam() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                    this.showWebcam = false;
                },
            
                // 4. Handle Upload & Compress File Gambar
                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;
            
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = (e) => {
                        const img = new Image();
                        img.src = e.target.result;
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const MAX_WIDTH = 1280;
                            const MAX_HEIGHT = 1280;
                            let width = img.width;
                            let height = img.height;
            
                            if (width > height) {
                                if (width > MAX_WIDTH) {
                                    height *= MAX_WIDTH / width;
                                    width = MAX_WIDTH;
                                }
                            } else {
                                if (height > MAX_HEIGHT) {
                                    width *= MAX_HEIGHT / height;
                                    height = MAX_HEIGHT;
                                }
                            }
            
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);
            
                            canvas.toBlob((blob) => {
                                if (!blob) return;
                                const compressedFile = new File([blob], file.name || 'bukti-mengajar.jpg', { type: 'image/jpeg', lastModified: Date.now() });
                                this.uploadFileToLivewire(compressedFile);
                            }, 'image/jpeg', 0.75);
                        };
                    };
                },
            
                // 5. Unggah File Terkompresi ke Livewire
                uploadFileToLivewire(file) {
                    this.isUploading = true;
                    this.uploadProgress = 10;
            
                    @this.upload('photoProof', file,
                        (uploadedFilename) => {
                            this.isUploading = false;
                            this.uploadProgress = 0;
                        },
                        () => {
                            this.isUploading = false;
                            this.uploadProgress = 0;
                            alert('Gagal mengunggah foto. Silakan coba lagi.');
                        },
                        (event) => {
                            this.uploadProgress = event.detail.progress;
                        }
                    );
                }
            }">

                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">
                    📷 Foto Bukti Mengajar Di Kelas <span class="text-rose-600">* (Wajib)</span>
                </label>

                <div class="bg-white p-3.5 border border-gray-200 rounded-2xl space-y-3 shadow-2xs">

                    <!-- MODAL / STREAM WEBCAM (MUNCUL SAAT MENGAMBIL FOTO/GANTI FOTO VIA WEBCAM) -->
                    <div x-show="showWebcam" x-cloak class="space-y-2">
                        <div class="relative w-full h-52 bg-black rounded-xl overflow-hidden border border-gray-300">
                            <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" @click="captureWebcam()"
                                class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer touch-manipulation">
                                <span>📸 Tangkap Foto</span>
                            </button>
                            <button type="button" @click="stopWebcam()"
                                class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>

                    <!-- JIKA BELUM ADA FOTO SAMA SEKALI DAN WEBCAM TIDAK AKTIF -->
                    @if (!$photoProof && !$existingPhoto)
                        <div x-show="!showWebcam">
                            <!-- TAMPILAN HP -->
                            <template x-if="isMobile">
                                <label
                                    class="flex items-center justify-center gap-2 w-full py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 rounded-xl text-xs font-bold cursor-pointer transition-all select-none touch-manipulation active:scale-[0.99] {{ $isLocked ? 'opacity-50 pointer-events-none' : '' }}">
                                    <span class="text-base">📸</span>
                                    <span>Ambil Foto via Kamera HP</span>
                                    <input type="file" accept="image/*" capture="environment"
                                        @change="handleFileSelect($event)" {{ $isLocked ? 'disabled' : '' }}
                                        class="hidden">
                                </label>
                            </template>

                            <!-- TAMPILAN LAPTOP / DESKTOP (2 PILIHAN UTAMA) -->
                            <template x-if="!isMobile">
                                <div class="grid grid-cols-1 gap-2.5">
                                    <button type="button" @click="startWebcam()" {{ $isLocked ? 'disabled' : '' }}
                                        class="flex items-center justify-center gap-2 py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 rounded-xl text-xs font-bold cursor-pointer transition-all active:scale-[0.99] disabled:opacity-50">
                                        <span class="text-base">📹</span>
                                        <span>Gunakan Webcam</span>
                                    </button>

                                    {{-- <label
                                        class="flex items-center justify-center gap-2 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200/80 rounded-xl text-xs font-bold cursor-pointer transition-all active:scale-[0.99] {{ $isLocked ? 'opacity-50 pointer-events-none' : '' }}">
                                        <span class="text-base">📁</span>
                                        <span>Pilih File Gambar</span>
                                        <input type="file" accept="image/*" @change="handleFileSelect($event)"
                                            {{ $isLocked ? 'disabled' : '' }} class="hidden">
                                    </label> --}}
                                </div>
                            </template>
                        </div>
                    @endif

                    <!-- INDIKATOR PROGRESS UPLOAD -->
                    <div x-show="isUploading" x-cloak
                        class="space-y-1.5 p-2 bg-indigo-50/50 rounded-xl border border-indigo-100">
                        <div class="flex justify-between items-center text-[10px] font-bold text-indigo-700">
                            <span class="flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-indigo-600 shrink-0" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>Mengompres & Mengunggah Foto...</span>
                            </span>
                            <span class="font-mono" x-text="uploadProgress + '%'"></span>
                        </div>
                        <div class="w-full bg-indigo-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-600 h-full transition-all duration-200 rounded-full"
                                :style="'width: ' + uploadProgress + '%'"></div>
                        </div>
                    </div>

                    <!-- ERROR VALIDASI -->
                    @error('photoProof')
                        <span class="text-xs text-rose-600 block font-semibold">⚠️ {{ $message }}</span>
                    @enderror

                    <!-- PREVIEW FOTO BARU SIAP DISIMPAN -->
                    @if ($photoProof)
                        <div x-show="!showWebcam" class="space-y-2">
                            <div class="flex flex-wrap justify-between items-center gap-2">
                                <span
                                    class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider flex items-center gap-1">
                                    <span>✓</span> Foto Baru Siap Disimpan
                                </span>

                                @if (!$isLocked)
                                    <!-- OPSI GANTI FOTO DI HP -->
                                    <template x-if="isMobile">
                                        <label
                                            class="text-[11px] text-indigo-600 hover:text-indigo-800 font-bold cursor-pointer underline flex items-center gap-1 touch-manipulation">
                                            <span>🔄 Ganti Foto</span>
                                            <input type="file" accept="image/*" capture="environment"
                                                @change="handleFileSelect($event)" class="hidden">
                                        </label>
                                    </template>

                                    <!-- OPSI GANTI FOTO DI LAPTOP (WEBCAM / FILE) -->
                                    <template x-if="!isMobile">
                                        <div class="flex items-center gap-2 text-[11px]">
                                            <button type="button" @click="startWebcam()"
                                                class="text-indigo-600 hover:text-indigo-800 font-bold underline cursor-pointer">
                                                📹 Ambil Ulang Webcam
                                            </button>
                                            <span class="text-gray-300">|</span>
                                            {{-- <label
                                                class="text-indigo-600 hover:text-indigo-800 font-bold cursor-pointer underline">
                                                <span>📁 Ganti File</span>
                                                <input type="file" accept="image/*"
                                                    @change="handleFileSelect($event)" class="hidden">
                                            </label> --}}
                                        </div>
                                    </template>
                                @endif
                            </div>

                            <div
                                class="relative w-full h-44 rounded-xl overflow-hidden border border-emerald-200 shadow-2xs">
                                <img src="{{ $photoProof->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                        </div>

                        <!-- PREVIEW FOTO TERGANTI / TERSIMPAN DI DATABASE -->
                    @elseif ($existingPhoto)
                        <div x-show="!showWebcam" class="space-y-2">
                            <div class="flex flex-wrap justify-between items-center gap-2">
                                <span
                                    class="text-[10px] text-gray-500 font-bold uppercase tracking-wider flex items-center gap-1">
                                    <span>📷</span> Foto Bukti Tersimpan
                                </span>

                                @if (!$isLocked)
                                    <!-- OPSI GANTI FOTO DI HP -->
                                    <template x-if="isMobile">
                                        <label
                                            class="text-[11px] text-indigo-600 hover:text-indigo-800 font-bold cursor-pointer underline flex items-center gap-1 touch-manipulation">
                                            <span>🔄 Ambil Ulang Foto</span>
                                            <input type="file" accept="image/*" capture="environment"
                                                @change="handleFileSelect($event)" class="hidden">
                                        </label>
                                    </template>

                                    <!-- OPSI GANTI FOTO DI LAPTOP (WEBCAM / FILE) -->
                                    <template x-if="!isMobile">
                                        <div class="flex items-center gap-2 text-[11px]">
                                            <button type="button" @click="startWebcam()"
                                                class="text-indigo-600 hover:text-indigo-800 font-bold underline cursor-pointer">
                                                📹 Ambil Ulang Webcam
                                            </button>
                                            <span class="text-gray-300">|</span>
                                            <label
                                                class="text-indigo-600 hover:text-indigo-800 font-bold cursor-pointer underline">
                                                <span>📁 Pilih File Baru</span>
                                                <input type="file" accept="image/*"
                                                    @change="handleFileSelect($event)" class="hidden">
                                            </label>
                                        </div>
                                    </template>
                                @endif
                            </div>

                            <div
                                class="relative w-full h-44 rounded-xl overflow-hidden border border-gray-200 shadow-2xs">
                                <img src="{{ asset('storage/' . $existingPhoto) }}"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="md:col-span-2 pt-2 flex justify-end">
                @if (!$isLocked)
                    <button wire:click="save" @click="isDirty = false" wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-8 py-3 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs transition-all inline-flex items-center justify-center gap-2 cursor-pointer min-h-[42px] touch-manipulation">
                        <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-1.5">💾
                            Simpan Absensi & Foto Bukti</span>
                        <span wire:loading.flex wire:target="save" class="items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Menyimpan Data...</span>
                        </span>
                    </button>
                @endif
            </div>

        </div>
    </div>
</div>
