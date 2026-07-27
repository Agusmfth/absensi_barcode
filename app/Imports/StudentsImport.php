<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\StudentBarcodeService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentsImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use SkipsFailures;

    public int $imported = 0;

    public function __construct(private readonly StudentBarcodeService $barcode) {}

    public function model(array $row): Student
    {
        $this->imported++;

        return new Student([
            'nis' => trim((string) $row['nis']),
            'nisn' => $this->nullable($row['nisn'] ?? null),
            'name' => trim((string) $row['nama']),
            'gender' => $row['jenis_kelamin'],
            'class_id' => $row['class_id'],
            'birth_place' => $this->nullable($row['tempat_lahir'] ?? null),
            'birth_date' => $this->nullable($row['tanggal_lahir'] ?? null),
            'address' => $this->nullable($row['alamat'] ?? null),
            'parent_name' => $this->nullable($row['nama_orang_tua'] ?? null),
            'parent_phone' => $this->nullable($row['no_hp_orang_tua'] ?? null),
            'barcode_token' => $this->barcode->generateToken(),
            'is_active' => true,
        ]);
    }

    public function prepareForValidation($data, $index): array
    {
        $gender = strtolower(trim((string) ($data['jenis_kelamin'] ?? '')));
        $data['jenis_kelamin'] = match ($gender) {
            'l', 'laki-laki', 'laki laki', 'pria' => 'L',
            'p', 'perempuan', 'wanita' => 'P',
            default => strtoupper($gender),
        };
        $className = strtolower(trim((string) ($data['kelas'] ?? '')));
        $data['class_id'] = SchoolClass::whereRaw('LOWER(class_name) = ?', [$className])->value('id');
        $data['tanggal_lahir'] = $this->date($data['tanggal_lahir'] ?? null);

        return $data;
    }

    public function rules(): array
    {
        return [
            'nis' => ['required', 'max:30', 'distinct', Rule::unique('students', 'nis')],
            'nisn' => ['nullable', 'max:30', 'distinct', Rule::unique('students', 'nisn')],
            'nama' => ['required', 'max:255'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'kelas' => ['required'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'tempat_lahir' => ['nullable', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'nama_orang_tua' => ['nullable', 'max:255'],
            'no_hp_orang_tua' => ['nullable', 'max:30'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nis.unique' => 'NIS sudah terdaftar.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'class_id.required' => 'Nama kelas tidak ditemukan di sistem.',
        ];
    }

    private function nullable(mixed $value): mixed
    {
        return $value === null || trim((string) $value) === '' ? null : trim((string) $value);
    }

    private function date(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, trim((string) $value));
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable) {
            }
        }

        return $value;
    }
}
