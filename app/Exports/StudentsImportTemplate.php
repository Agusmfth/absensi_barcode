<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsImportTemplate implements FromArray, ShouldAutoSize, WithHeadings
{
    public function headings(): array
    {
        return ['nis', 'nisn', 'nama', 'jenis_kelamin', 'kelas', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'nama_orang_tua', 'no_hp_orang_tua'];
    }

    public function array(): array
    {
        return [['20260001', '0012345678', 'Budi Santoso', 'L', '1A', 'Jakarta', '15/05/2015', 'Jl. Pendidikan No. 1', 'Sutrisno', '081234567890']];
    }
}
