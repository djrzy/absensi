<div class="p-6 max-w-7xl mx-auto space-y-6">

    <!-- Header & Tombol Kembali -->
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-indigo-50 text-indigo-700 rounded-2xl flex items-center justify-center font-bold text-lg font-mono">
                {{ substr($student->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $student->name }}</h1>
                <p class="text-xs text-gray-500 font-mono mt-0.5">
                    NISN: {{ $student->nisn }} • Kelas: {{ $student->classroom->name ?? 'Belum Ada Kelas' }}
                </p>
            </div>
        </div>

        <a href="{{ route('admin.students') }}"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all flex items-center gap-2">
            ⬅️ Kembali ke Daftar Siswa
        </a>
    </div>

    @php
        $bio = $student->bio_details ?? [];
        $alamat = $bio['alamat'] ?? [];
        $ayah = $bio['data_ayah'] ?? [];
        $ibu = $bio['data_ibu'] ?? [];
        $wali = $bio['data_wali'] ?? [];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Informasi Pribadi & Alamat -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. Biodata Utama Siswa -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-4">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                    📋 Biodata Pribadi Siswa
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-gray-400 block mb-0.5">Nomor Induk Lokal</span>
                        <strong class="text-gray-800 font-mono">{{ $bio['nomor_induk'] ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">NIK (Nomor Induk Kependudukan)</span>
                        <strong class="text-gray-800 font-mono">{{ $bio['nik'] ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Tempat, Tanggal Lahir</span>
                        <strong class="text-gray-800">
                            {{ $bio['tempat_lahir'] ?? '-' }}, {{ $bio['tanggal_lahir'] ?? '-' }}
                        </strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Jenis Kelamin / Agama</span>
                        <strong class="text-gray-800">
                            {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }} • {{ $bio['agama'] ?? '-' }}
                        </strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">No. Kartu Keluarga (KK)</span>
                        <strong class="text-gray-800 font-mono">{{ $bio['no_kk'] ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">No. Akta Kelahiran</span>
                        <strong class="text-gray-800 font-mono">{{ $bio['no_akta'] ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Anak Ke / Jumlah Saudara</span>
                        <strong class="text-gray-800">
                            Anak ke-{{ $bio['anak_ke'] ?? '-' }} dari {{ $bio['jumlah_saudara'] ?? '-' }} bersaudara
                        </strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Sekolah Asal</span>
                        <strong class="text-gray-800">{{ $bio['sekolah_asal'] ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 2. Alamat Domisili Siswa -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-4">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                    🏠 Alamat & Domisili Tempat Tinggal
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="sm:col-span-2">
                        <span class="text-gray-400 block mb-0.5">Alamat Jalan</span>
                        <strong class="text-gray-800">{{ $alamat['jalan'] ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Kelurahan / Kecamatan</span>
                        <strong class="text-gray-800">
                            {{ $alamat['kelurahan'] ?? '-' }} / {{ $alamat['kecamatan'] ?? '-' }}
                        </strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Kota / Provinsi</span>
                        <strong class="text-gray-800">
                            {{ $alamat['kota'] ?? '-' }}, {{ $alamat['provinsi'] ?? '-' }}
                        </strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">No. Telepon / HP Siswa</span>
                        <strong class="text-gray-800 font-mono">{{ $alamat['telepon'] ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Email Siswa</span>
                        <strong class="text-gray-800">{{ $alamat['email'] ?? '-' }}</strong>
                    </div>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Data Orang Tua / Wali -->
        <div class="space-y-6">

            <!-- Data Ayah -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-3 text-xs">
                <h2
                    class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-1.5">
                    👨‍👦 Data Ayah Kandung
                </h2>
                <div>
                    <span class="text-gray-400 block">Nama Ayah</span>
                    <strong class="text-gray-800 text-sm">{{ $ayah['nama'] ?? '-' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block">NIK Ayah</span>
                    <strong class="text-gray-800 font-mono">{{ $ayah['nik'] ?? '-' }}</strong>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-gray-400 block">Pekerjaan</span>
                        <strong class="text-gray-800">{{ $ayah['pekerjaan'] ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Telepon / WA</span>
                        <strong class="text-gray-800 font-mono">{{ $ayah['telepon'] ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Data Ibu -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-3 text-xs">
                <h2
                    class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-1.5">
                    👩‍👦 Data Ibu Kandung
                </h2>
                <div>
                    <span class="text-gray-400 block">Nama Ibu</span>
                    <strong class="text-gray-800 text-sm">{{ $ibu['nama'] ?? '-' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block">NIK Ibu</span>
                    <strong class="text-gray-800 font-mono">{{ $ibu['nik'] ?? '-' }}</strong>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-gray-400 block">Pekerjaan</span>
                        <strong class="text-gray-800">{{ $ibu['pekerjaan'] ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Telepon / WA</span>
                        <strong class="text-gray-800 font-mono">{{ $ibu['telepon'] ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Data Fisik / Kesehatan -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-3 text-xs">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                    🩺 Catatan Fisik & Kesehatan
                </h2>
                <div class="grid grid-cols-3 gap-2 text-center font-bold">
                    <div class="p-2 bg-gray-50 rounded-xl">
                        <span class="block text-[10px] text-gray-400">Gol. Darah</span>
                        <span class="text-gray-800 font-mono">{{ $bio['gol_darah'] ?? '-' }}</span>
                    </div>
                    <div class="p-2 bg-gray-50 rounded-xl">
                        <span class="block text-[10px] text-gray-400">Tinggi</span>
                        <span class="text-gray-800 font-mono">{{ $bio['tinggi_badan'] ?? '-' }} cm</span>
                    </div>
                    <div class="p-2 bg-gray-50 rounded-xl">
                        <span class="block text-[10px] text-gray-400">Berat</span>
                        <span class="text-gray-800 font-mono">{{ $bio['berat_badan'] ?? '-' }} kg</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
