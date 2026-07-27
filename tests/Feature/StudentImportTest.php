<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_students_from_csv_with_birth_data(): void
    {
        SchoolClass::create(['class_name' => '1A', 'grade_level' => 1, 'academic_year' => '2026/2027']);
        $admin = User::factory()->create();
        $csv = "nis,nisn,nama,jenis_kelamin,kelas,tempat_lahir,tanggal_lahir,alamat,nama_orang_tua,no_hp_orang_tua\n".
            "20260099,0012345678,Budi Santoso,L,1A,Jakarta,15/05/2015,Jalan Melati,Sutrisno,081234567890\n";

        $response = $this->actingAs($admin)->post(route('students.import'), [
            'student_file' => UploadedFile::fake()->createWithContent('students.csv', $csv),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', '1 siswa berhasil diimpor.');
        $this->assertDatabaseHas('students', [
            'nis' => '20260099',
            'name' => 'Budi Santoso',
            'birth_place' => 'Jakarta',
            'birth_date' => '2015-05-15 00:00:00',
        ]);
    }

    public function test_import_template_can_be_downloaded(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('students.import.template'));

        $response->assertOk();
        $response->assertDownload('template-import-siswa.xlsx');
    }
}
