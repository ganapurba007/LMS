<?php

namespace Tests\Feature\Student;

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswa;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();

        $roleSiswa = Role::create(['name' => 'siswa']);
        $this->class = SchoolClass::create(['name' => 'Kelas X IPA 1']);

        $this->siswa = User::factory()->create([
            'role_id' => $roleSiswa->id,
            'class_id' => $this->class->id,
        ]);
    }

    public function test_siswa_can_view_student_dashboard(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Siswa');
        $response->assertSee('Kelas X IPA 1');
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_guru_can_view_frontend_dashboard(): void
    {
        $roleGuru = Role::create(['name' => 'guru']);
        $guru = User::factory()->create([
            'role_id' => $roleGuru->id,
            'class_id' => null,
        ]);

        $response = $this->actingAs($guru)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Admin Guru');
    }
}
