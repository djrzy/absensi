<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentManager extends Component
{
    use WithPagination, WithFileUploads;

    // Form Input Manual (Tambah/Edit)
    public $name, $nisn, $gender, $classroom_id;
    public $selectedStudentId = null;
    public $isEditMode = false;

    // Filter & Search State
    public $search = '';
    public $filterClassroom = '';
    public $filterGender = '';

    // Bulk Action State (Hapus Massal)
    public $selectedStudents = [];
    public $selectAll = false;

    // Impor Excel & Laporan State
    public $excelFile;
    public $importSummary = null;

    public function updatingSearch()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }
    public function updatingFilterClassroom()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }
    public function updatingFilterGender()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedStudents = $this->getCurrentPageStudentIds();
        } else {
            $this->selectedStudents = [];
        }
    }

    public function updatedPage()
    {
        $this->resetBulkSelection();
    }

    private function resetBulkSelection()
    {
        $this->selectedStudents = [];
        $this->selectAll = false;
    }

    private function getCurrentPageStudentIds()
    {
        return $this->getStudentsQuery()->paginate(10)->pluck('id')->map(fn($id) => (string)$id)->toArray();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $this->selectedStudentId,
            'gender' => 'required|in:L,P',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        if ($this->isEditMode) {
            $student = Student::findOrFail($this->selectedStudentId);
            $student->update([
                'name' => $this->name,
                'nisn' => $this->nisn,
                'gender' => $this->gender,
                'classroom_id' => $this->classroom_id ?: null,
            ]);
            session()->flash('success', 'Data murid berhasil diperbarui!');
        } else {
            Student::create([
                'name' => $this->name,
                'nisn' => $this->nisn,
                'gender' => $this->gender,
                'classroom_id' => $this->classroom_id ?: null,
            ]);
            session()->flash('success', 'Murid baru berhasil didaftarkan!');
        }

        $this->resetInputFields();
    }

    public function importExcel()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls|max:10240',
        ], [
            'excelFile.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'excelFile.mimes'    => 'Format file harus .xlsx atau .xls',
        ]);

        $filePath = $this->excelFile->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // 1. PRE-FETCH CACHE IN MEMORY (Super Fast Lookup)
        // Ambil daftar kelas yang ADA di DB: [ '26/27-VIIR' => id_kelas ]
        $classroomsMap = Classroom::pluck('id', 'name')->toArray();

        // Ambil seluruh NISN siswa yang sudah terdaftar untuk deteksi duplikasi instant
        $existingNisns = Student::pluck('nisn', 'nisn')->toArray();

        // Ambil seluruh Username/Email User yang sudah ada
        $existingUsers = User::pluck('id', 'username')->toArray();

        $successCount = 0;
        $failedCount = 0;
        $failedDetails = [];

        $studentsToInsert = [];
        $usersToInsert = [];
        $parentsToInsert = [];

        $now = now()->toDateTimeString();
        $defaultPasswordHash = Hash::make('password'); // Pre-hash sekali saja untuk hemat CPU

        foreach ($rows as $index => $row) {
            if ($index < 4) continue; // Skip 4 baris header template

            // SESUAI TEMPLATE EXCEL:
            $nisn         = trim((string)($row[28] ?? $row[2] ?? '')); // NISN (Col AC / Col C)
            $name         = trim((string)($row[3] ?? ''));             // Nama Siswa (Col D)
            $classNameRaw = strtoupper(trim((string)($row[4] ?? ''))); // KODE KELAS (Col E - Indeks 4)
            $genderRaw    = strtoupper(trim((string)($row[8] ?? ''))); // Gender (Col I - Indeks 8)

            if (empty($name) && empty($nisn)) continue;

            $gender = in_array($genderRaw, ['P', 'PEREMPUAN']) ? 'P' : 'L';

            // VALIDASI 1: NISN KOSONG
            if (empty($nisn)) {
                $failedCount++;
                $failedDetails[] = ['row' => $index + 1, 'name' => $name ?: 'Tanpa Nama', 'nisn' => '-', 'reason' => 'NISN Kosong'];
                continue;
            }

            // VALIDASI 2: DUPLIKASI NISN
            if (isset($existingNisns[$nisn])) {
                $failedCount++;
                $failedDetails[] = ['row' => $index + 1, 'name' => $name, 'nisn' => $nisn, 'reason' => 'NISN Sudah Terdaftar (Duplikat)'];
                continue;
            }

            // VALIDASI 3: CEK KELAS DI DATABASE (STRICT REQUIREMENT)
            if (empty($classNameRaw) || !isset($classroomsMap[$classNameRaw])) {
                $failedCount++;
                $failedDetails[] = [
                    'row'    => $index + 1,
                    'name'   => $name,
                    'nisn'   => $nisn,
                    'reason' => "Kelas '{$classNameRaw}' Tidak Ditemukan di Database"
                ];
                continue; // LEWATKAN JIKA KELAS TIDAK ADA DI DATABASE
            }

            $classroomId = $classroomsMap[$classNameRaw];

            // Format bio_details
            $bioDetails = [
                'nomor_induk'     => trim((string)($row[2] ?? '')),
                'status'          => trim((string)($row[5] ?? '')),
                'tahun_masuk'     => trim((string)($row[6] ?? '')),
                'tahun_lulus'     => trim((string)($row[7] ?? '')),
                'tempat_lahir'    => trim((string)($row[9] ?? '')),
                'tanggal_lahir'   => trim((string)($row[10] ?? '')),
                'agama'           => trim((string)($row[11] ?? '')),
                'kewarganegaraan' => trim((string)($row[12] ?? '')),
                'nik'             => trim((string)($row[13] ?? '')),
                'no_kk'           => trim((string)($row[14] ?? '')),
                'no_akta'         => trim((string)($row[15] ?? '')),
                'anak_ke'         => trim((string)($row[16] ?? '')),
                'jumlah_saudara'  => trim((string)($row[17] ?? '')),
                'berat_badan'     => trim((string)($row[18] ?? '')),
                'tinggi_badan'    => trim((string)($row[19] ?? '')),
                'gol_darah'       => trim((string)($row[20] ?? '')),
                'riwayat_penyakit' => trim((string)($row[21] ?? '')),
                'status_yatim'    => trim((string)($row[22] ?? '')),
                'status_tinggal'  => trim((string)($row[23] ?? '')),
                'jarak_tinggal'   => trim((string)($row[24] ?? '')),
                'bahasa'          => trim((string)($row[25] ?? '')),
                'hobi'            => trim((string)($row[26] ?? '')),
                'cita_cita'       => trim((string)($row[27] ?? '')),
                'no_ijazah_asal'  => trim((string)($row[29] ?? '')),
                'sekolah_asal'    => trim((string)($row[30] ?? '')),

                'alamat' => [
                    'jalan'     => trim((string)($row[33] ?? '')),
                    'kelurahan' => trim((string)($row[34] ?? '')),
                    'kecamatan' => trim((string)($row[35] ?? '')),
                    'kota'      => trim((string)($row[36] ?? '')),
                    'provinsi'  => trim((string)($row[37] ?? '')),
                    'telepon'   => trim((string)($row[38] ?? '')),
                ],

                'data_ayah' => [
                    'nama'        => trim((string)($row[40] ?? '')),
                    'tempat_lahir' => trim((string)($row[41] ?? '')),
                    'tanggal_lahir' => trim((string)($row[42] ?? '')),
                    'agama'       => trim((string)($row[43] ?? '')),
                    'nik'         => trim((string)($row[45] ?? '')),
                    'pekerjaan'   => trim((string)($row[46] ?? '')),
                    'penghasilan' => trim((string)($row[47] ?? '')),
                    'pendidikan'  => trim((string)($row[48] ?? '')),
                    'status_hidup' => trim((string)($row[49] ?? '')),
                    'telepon'     => trim((string)($row[51] ?? '')),
                ],

                'data_ibu' => [
                    'nama'        => trim((string)($row[53] ?? '')),
                    'tempat_lahir' => trim((string)($row[54] ?? '')),
                    'tanggal_lahir' => trim((string)($row[55] ?? '')),
                    'agama'       => trim((string)($row[56] ?? '')),
                    'nik'         => trim((string)($row[58] ?? '')),
                    'pekerjaan'   => trim((string)($row[59] ?? '')),
                    'penghasilan' => trim((string)($row[60] ?? '')),
                    'pendidikan'  => trim((string)($row[61] ?? '')),
                    'status_hidup' => trim((string)($row[62] ?? '')),
                    'telepon'     => trim((string)($row[64] ?? '')),
                ],

                'data_wali' => [
                    'nama'            => trim((string)($row[66] ?? '')),
                    'tempat_lahir'    => trim((string)($row[67] ?? '')),
                    'tanggal_lahir'   => trim((string)($row[68] ?? '')),
                    'nik'             => trim((string)($row[71] ?? '')),
                    'pekerjaan'       => trim((string)($row[72] ?? '')),
                    'status_hubungan'  => trim((string)($row[75] ?? '')),
                    'telepon'         => trim((string)($row[77] ?? '')),
                ]
            ];

            // Buat nama akun wali
            $parentName = $bioDetails['data_ayah']['nama']
                ?: ($bioDetails['data_ibu']['nama']
                    ?: 'Wali dari ' . $name);

            // MASUKKAN KE PREPARED ARRAY UNTUK BATCH INSERT TRANSACTION
            try {
                DB::transaction(function () use ($name, $nisn, $gender, $classroomId, $bioDetails, $parentName, $defaultPasswordHash, $now) {
                    // 1. Simpan Siswa
                    $student = Student::create([
                        'name'         => $name,
                        'nisn'         => $nisn,
                        'gender'       => $gender,
                        'classroom_id' => $classroomId,
                        'bio_details'  => $bioDetails,
                    ]);

                    // 2. Buat Akun Wali (Username & Password = NISN)
                    $parentUser = User::create([
                        'name'     => $parentName,
                        'username' => $nisn, // LOGIN DENGAN NISN SEBAGAI USERNAME
                        'email'    => $nisn . '@sekolah.id',
                        'password' => $defaultPasswordHash, // Hash pre-calculated
                        'role'     => 'WaliMurid',
                    ]);

                    // 3. Tautkan Akun Wali ke Siswa
                    DB::table('student_parents')->insert([
                        'user_id'    => $parentUser->id,
                        'student_id' => $student->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                });

                // Tandai NISN sudah terpakai
                $existingNisns[$nisn] = $nisn;
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $failedDetails[] = [
                    'row'    => $index + 1,
                    'name'   => $name,
                    'nisn'   => $nisn,
                    'reason' => 'Error DB: ' . $e->getMessage()
                ];
            }
        }

        $this->importSummary = [
            'success' => $successCount,
            'failed'  => $failedCount,
            'details' => $failedDetails
        ];

        $this->reset('excelFile');
        session()->flash('success_import', 'Proses impor berhasil diselesaikan dengan cepat!');
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $this->selectedStudentId = $student->id;
        $this->name = $student->name;
        $this->nisn = $student->nisn;
        $this->gender = $student->gender;
        $this->classroom_id = $student->classroom_id;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        Student::findOrFail($id)->delete();
        session()->flash('success', 'Data murid berhasil dihapus!');
    }

    public function deleteSelected()
    {
        if (empty($this->selectedStudents)) {
            return;
        }

        $count = count($this->selectedStudents);
        Student::whereIn('id', $this->selectedStudents)->delete();

        $this->resetBulkSelection();
        session()->flash('success', "Berhasil menghapus {$count} data murid sekaligus!");
    }

    public function resetInputFields()
    {
        $this->reset(['name', 'nisn', 'gender', 'classroom_id', 'selectedStudentId', 'isEditMode']);
    }

    private function getStudentsQuery()
    {
        return Student::with('classroom')
            ->when($this->search, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterClassroom, function ($q) {
                if ($this->filterClassroom === 'unassigned') {
                    $q->whereNull('classroom_id');
                } else {
                    $q->where('classroom_id', $this->filterClassroom);
                }
            })
            ->when($this->filterGender, function ($q) {
                $q->where('gender', $this->filterGender);
            })
            ->orderBy('name', 'asc');
    }

    public function render()
    {
        $students = $this->getStudentsQuery()->paginate(10);

        return view('livewire.admin.student-manager', [
            'students'   => $students,
            'classrooms' => Classroom::orderBy('name')->get()
        ]);
    }
}
