<?php

namespace App\Livewire\Admin;

use App\Models\AcademicCalendar;
use App\Models\AcademicYear;
use Livewire\Component;

class CalendarManager extends Component
{
    public $holidays;
    public $date;
    public $description;

    public function mount()
    {
        $this->refreshHolidays();
    }

    public function refreshHolidays()
    {
        $this->holidays = AcademicCalendar::orderBy('date', 'asc')->get();
    }

    public function store()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) {
            session()->flash('error', 'Gagal! Tidak ada Tahun Ajaran aktif saat ini.');
            return;
        }

        $this->validate([
            'date' => 'required|date|unique:academic_calendars,date',
            'description' => 'required|string|max:100',
        ]);

        AcademicCalendar::create([
            'academic_year_id' => $activeYear->id,
            'date' => $this->date,
            'description' => $this->description,
        ]);

        $this->reset(['date', 'description']);
        $this->refreshHolidays();
        session()->flash('success', 'Hari libur resmi berhasil ditambahkan!');
    }

    public function delete($id)
    {
        AcademicCalendar::findOrFail($id)->delete();
        $this->refreshHolidays();
        session()->flash('success', 'Hari libur berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.admin.calendar-manager');
    }
}
