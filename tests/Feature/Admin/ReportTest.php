<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;
    protected User $siswa;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();

        $roleGuru = Role::create(['name' => 'guru']);
        $roleSiswa = Role::create(['name' => 'siswa']);

        $this->class = SchoolClass::create(['name' => 'Kelas X IPA 1']);

        $this->guru = User::factory()->create([
            'role_id' => $roleGuru->id,
            'nip' => '198401012005011005',
        ]);

        $this->siswa = User::factory()->create([
            'role_id' => $roleSiswa->id,
            'class_id' => $this->class->id,
        ]);
    }

    public function test_guru_can_view_reports(): void
    {
        $response = $this->actingAs($this->guru)->get(route('admin.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan &amp; Rekap Nilai Siswa', false);
    }

    public function test_guru_can_export_csv(): void
    {
        $response = $this->actingAs($this->guru)->get(route('admin.reports.export-csv'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('Rekap_Nilai_', $response->headers->get('Content-Disposition'));
    }

    public function test_siswa_cannot_access_reports(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('admin.reports.index'));
        $response->assertRedirect(route('dashboard'));
    }
}
