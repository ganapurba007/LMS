<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_siswa_can_register_with_class_selection(): void
    {
        $class = SchoolClass::first();

        $response = $this->post('/register', [
            'name' => 'Siswa Baru',
            'email' => 'siswabaru@lms.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'class_id' => $class->id,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'siswabaru@lms.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isSiswa());
        $this->assertEquals($class->id, $user->class_id);
    }

    public function test_siswa_cannot_register_without_class_selection(): void
    {
        $response = $this->post('/register', [
            'name' => 'Siswa No Class',
            'email' => 'siswanoclass@lms.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('class_id');
        $this->assertGuest();
    }

    public function test_guru_login_redirects_to_student_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'guru@lms.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_siswa_login_redirects_to_student_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'siswa1@lms.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_siswa_accessing_admin_route_is_redirected_to_student_dashboard(): void
    {
        $siswa = User::where('email', 'siswa1@lms.com')->first();

        $response = $this->actingAs($siswa)->get('/admin/dashboard');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_guest_accessing_admin_route_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_user_can_logout(): void
    {
        $siswa = User::where('email', 'siswa1@lms.com')->first();

        $response = $this->actingAs($siswa)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_admin_shortcut_redirects_to_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_login_can_be_accessed(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk Panel Guru');
    }

    public function test_logged_in_siswa_accessing_admin_login_is_blocked_and_redirected_to_dashboard(): void
    {
        $siswa = User::where('email', 'siswa1@lms.com')->first();

        $response = $this->actingAs($siswa)->get('/admin/login');

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }
}
