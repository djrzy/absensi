<?php

namespace App\Livewire\Admin;

use App\Models\AcademicYear;
use Livewire\Component;

class AcademicYearManager extends Component
{
    public $years;

    // Properti form baru sesuai kolom database
    public $year;       // Menampung input teks (misal: 2026/2027)
    public $semester;   // Menampung pilihan dropdown (Ganjil / Genap)

    public function mount()
    {
        $this->refreshYears();
    }

    public function refreshYears()
    {
        $this->years = AcademicYear::orderBy('id', 'desc')->get();
    }

    public function store()
    {
        // Validasi kombinasi unik tahun + semester agar tidak ada duplikasi data yang sama
        $this->validate([
            'year' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        // Cek duplikasi manual untuk kombinasi ganda
        $exists = AcademicYear::where('year', $this->year)
            ->where('semester', $this->semester)
            ->exists();

        if ($exists) {
            $this->addError('year', 'Kombinasi Tahun Ajaran & Semester ini sudah terdaftar.');
            return;
        }

        $isFirst = AcademicYear::count() === 0;

        AcademicYear::create([
            'year' => $this->year,
            'semester' => $this->semester,
            'is_active' => $isFirst,
        ]);

        $this->reset(['year', 'semester']);
        $this->refreshYears();
        session()->flash('success', 'Tahun Ajaran baru berhasil ditambahkan!');
    }

    public function activate($id)
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);

        $year = AcademicYear::findOrFail($id);
        $year->update(['is_active' => true]);

        $this->refreshYears();
        session()->flash('success', "Tahun Ajaran {$year->year} - {$year->semester} sekarang AKTIF!");
    }

    public function delete($id)
    {
        $year = AcademicYear::findOrFail($id);

        if ($year->is_active) {
            session()->flash('error', 'Gagal! Tahun ajaran yang sedang AKTIF tidak boleh dihapus.');
            return;
        }

        $year->delete();
        $this->refreshYears();
        session()->flash('success', 'Tahun Ajaran berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.admin.academic-year-manager');
    }
}
