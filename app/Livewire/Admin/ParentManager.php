<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Student;
use App\Models\Classroom;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ParentManager extends Component
{
    use WithPagination;

    // Properti Form Input Akun Wali Utama
    public $name;
    public $email;
    public $password;
    public $student_id;

    // Properti Mode Edit
    public $selectedParentId = null;
    public $isEditMode = false;

    // Properti Filter, Search, & Dynamic Limit Pagination
    public $search = '';
    public $filterClassroom = '';
    public $perPage = 10;

    // Properti Bulk Delete State
    public $selectedParents = [];
    public $selectAll = false;
    public $selectAllMatches = false;

    // Properti Pencarian Combobox Anak Pertama & Modal
    public $searchStudent = '';
    public $searchModalStudent = '';

    // Properti State Modal "+ Tautkan Anak"
    public $showLinkModal = false;
    public $targetParent = null;
    public $additional_student_id = null;

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

    public function updatingPerPage()
    {
        $this->resetPage();
        $this->resetBulkSelection();
    }

    public function updatedPage()
    {
        $this->resetBulkSelection();
    }

    // Toggle Checkbox Master (Halaman Aktif)
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedParents = $this->getCurrentPageParentIds();
        } else {
            $this->resetBulkSelection();
        }
    }

    // Toggle Pilih Seluruh Data Wali Terfilter di Semua Halaman
    public function selectAllFilteredData()
    {
        $this->selectAllMatches = true;
        $this->selectedParents = $this->getParentsQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
    }

    public function resetBulkSelection()
    {
        $this->selectedParents = [];
        $this->selectAll = false;
        $this->selectAllMatches = false;
    }

    private function getCurrentPageParentIds()
    {
        return $this->getParentsQuery()->paginate($this->perPage)->pluck('id')->map(fn($id) => (string)$id)->toArray();
    }

    public function selectStudent($id)
    {
        $this->student_id = $id;
        $this->searchStudent = '';
    }

    public function clearSelectedStudent()
    {
        $this->student_id = null;
    }

    // Helper penanganan input login agar aman sesuai migrasi (email / username / NISN)
    private function resolveCredentials($input)
    {
        $clean = trim($input);
        if (filter_var($clean, FILTER_VALIDATE_EMAIL)) {
            return [$clean, explode('@', $clean)[0]];
        }
        return [$clean . '@sekolah.id', $clean];
    }

    public function store()
    {
        $this->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|string|max:100',
            'password'   => 'required|min:6',
            'student_id' => 'nullable|exists:students,id',
        ], [
            'name.required'     => 'Nama lengkap wali murid wajib diisi.',
            'email.required'    => 'Email / Username / NISN wajib diisi.',
            'password.required' => 'Kata sandi minimal 6 karakter.',
        ]);

        [$resolvedEmail, $resolvedUsername] = $this->resolveCredentials($this->email);

        if (User::where('email', $resolvedEmail)->orWhere('username', $resolvedUsername)->exists()) {
            $this->addError('email', 'Email / Username / NISN ini sudah terdaftar di sistem.');
            return;
        }

        DB::transaction(function () use ($resolvedEmail, $resolvedUsername) {
            $user = User::create([
                'name'     => $this->name,
                'username' => $resolvedUsername,
                'email'    => $resolvedEmail,
                'password' => Hash::make($this->password),
                'role'     => 'WaliMurid',
            ]);

            if ($this->student_id) {
                DB::table('student_parents')->insert([
                    'user_id'    => $user->id,
                    'student_id' => $this->student_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $this->resetForm();
        session()->flash('success', 'Akun Wali Murid berhasil dibuat!');
    }

    public function edit($id)
    {
        $parent = User::findOrFail($id);
        $this->selectedParentId = $parent->id;
        $this->name             = $parent->name;
        $this->email            = $parent->username ?: $parent->email;
        $this->password         = '';
        $this->isEditMode       = true;
    }

    public function update()
    {
        $this->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|string|max:100',
            'password' => 'nullable|min:6',
        ], [
            'name.required'  => 'Nama lengkap wali murid wajib diisi.',
            'email.required' => 'Email / Username / NISN wajib diisi.',
            'password.min'   => 'Kata sandi baru minimal 6 karakter.',
        ]);

        [$resolvedEmail, $resolvedUsername] = $this->resolveCredentials($this->email);

        $exists = User::where('id', '!=', $this->selectedParentId)
            ->where(function ($q) use ($resolvedEmail, $resolvedUsername) {
                $q->where('email', $resolvedEmail)->orWhere('username', $resolvedUsername);
            })->exists();

        if ($exists) {
            $this->addError('email', 'Email / Username / NISN ini sudah digunakan akun lain.');
            return;
        }

        $parent = User::findOrFail($this->selectedParentId);

        $updateData = [
            'name'     => $this->name,
            'username' => $resolvedUsername,
            'email'    => $resolvedEmail,
        ];

        if (!empty($this->password)) {
            $updateData['password'] = Hash::make($this->password);
        }

        $parent->update($updateData);

        $this->resetForm();
        session()->flash('success', 'Data Akun Wali Murid berhasil diperbarui!');
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'name',
            'email',
            'password',
            'student_id',
            'searchStudent',
            'selectedParentId',
            'isEditMode'
        ]);
    }

    public function openLinkModal($parentId)
    {
        $this->targetParent = User::findOrFail($parentId);
        $this->additional_student_id = null;
        $this->searchModalStudent = '';
        $this->showLinkModal = true;
    }

    public function closeLinkModal()
    {
        $this->showLinkModal = false;
        $this->targetParent = null;
        $this->additional_student_id = null;
        $this->searchModalStudent = '';
    }

    public function linkStudentById($studentId)
    {
        $this->additional_student_id = $studentId;
        $this->linkStudent();
    }

    public function linkStudent()
    {
        $this->validate([
            'additional_student_id' => 'required|exists:students,id',
        ], [
            'additional_student_id.required' => 'Silakan pilih siswa yang ingin ditautkan.',
        ]);

        $exists = DB::table('student_parents')
            ->where('user_id', $this->targetParent->id)
            ->where('student_id', $this->additional_student_id)
            ->exists();

        if ($exists) {
            session()->flash('modal_error', 'Siswa ini sudah terikat dengan akun wali tersebut.');
            return;
        }

        DB::table('student_parents')->insert([
            'user_id'    => $this->targetParent->id,
            'student_id' => $this->additional_student_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('success', 'Berhasil menautkan anak tambahan ke akun ' . $this->targetParent->name);
        $this->closeLinkModal();
    }

    public function unlinkStudent($parentId, $studentId)
    {
        DB::table('student_parents')
            ->where('user_id', $parentId)
            ->where('student_id', $studentId)
            ->delete();

        session()->flash('success', 'Tautan anak berhasil dilepaskan dari akun wali tersebut.');
    }

    public function delete($userId)
    {
        // Memanfaatkan cascade delete dari Foreign Key student_parents
        User::where('id', $userId)->where('role', 'WaliMurid')->delete();

        if ($this->selectedParentId === $userId) {
            $this->resetForm();
        }

        session()->flash('success', 'Akun Wali Murid berhasil dihapus.');
    }

    // ACTION: Hapus Massal Wali Murid Terpilih
    public function deleteSelected()
    {
        if (empty($this->selectedParents)) {
            return;
        }

        $count = count($this->selectedParents);

        // Memanfaatkan cascade delete dari Foreign Key student_parents
        User::whereIn('id', $this->selectedParents)->where('role', 'WaliMurid')->delete();

        $this->resetBulkSelection();
        $this->resetForm();
        session()->flash('success', "Berhasil menghapus {$count} akun wali murid secara permanen!");
    }

    private function getParentsQuery()
    {
        return User::where('role', 'WaliMurid')
            ->with(['students.classroom'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%')
                        ->orWhereHas('students', function ($studentQuery) {
                            $studentQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('nisn', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterClassroom, function ($query) {
                $query->whereHas('students', function ($studentQuery) {
                    $studentQuery->where('classroom_id', $this->filterClassroom);
                });
            })
            ->orderBy('id', 'desc');
    }

    public function render()
    {
        $parentsQuery = $this->getParentsQuery();
        $totalParents = $parentsQuery->count();
        $parents      = $parentsQuery->paginate($this->perPage);

        $searchedStudents = Student::with('classroom:id,name')
            ->when($this->searchStudent, function ($q) {
                $q->where('name', 'like', '%' . $this->searchStudent . '%')
                    ->orWhere('nisn', 'like', '%' . $this->searchStudent . '%');
            })
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();

        $modalSearchedStudents = Student::with('classroom:id,name')
            ->when($this->searchModalStudent, function ($q) {
                $q->where('name', 'like', '%' . $this->searchModalStudent . '%')
                    ->orWhere('nisn', 'like', '%' . $this->searchModalStudent . '%');
            })
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();

        $selectedStudent = $this->student_id ? Student::with('classroom')->find($this->student_id) : null;

        return view('livewire.admin.parent-manager', [
            'parents'               => $parents,
            'totalParents'          => $totalParents,
            'searchedStudents'      => $searchedStudents,
            'modalSearchedStudents' => $modalSearchedStudents,
            'selectedStudent'       => $selectedStudent,
            'classrooms'            => Classroom::orderBy('name', 'asc')->get()
        ]);
    }
}
