<?php

namespace Tests\Feature\Admin;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk Panel Guru');
        $response->assertSee('RuangTerra');
        // Tidak ada register di halaman login backend
        $response->assertDontSee('Daftar Siswa Baru');
        $response->assertDontSee('Daftar Sekarang');
    }

    public function test_guru_can_authenticate_via_admin_login(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'guru@lms.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_siswa_cannot_authenticate_via_admin_login(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'siswa1@lms.com',
            'password' => 'password',
        ]);

        // Siswa tidak boleh berhasil login di backend
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_invalid_credentials_fail_via_admin_login(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'guru@lms.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi?');
        $response->assertSee('Kirim Tautan Reset');
    }

    public function test_admin_forgot_password_rejects_siswa_email(): void
    {
        $response = $this->post('/admin/forgot-password', [
            'email' => 'siswa1@lms.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_frontend_login_allows_both_guru_and_siswa(): void
    {
        // 1. Frontend Login screen contains Register and Forgot Password
        $loginScreen = $this->get('/login');
        $loginScreen->assertStatus(200);
        $loginScreen->assertSee('Daftar Siswa Baru');
        $loginScreen->assertSee('Lupa Kata Sandi?');

        // 2. Siswa can login via frontend /login -> redirects to student /dashboard
        $siswaResponse = $this->post('/login', [
            'email' => 'siswa1@lms.com',
            'password' => 'password',
        ]);
        $siswaResponse->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->post('/logout');
        $this->assertGuest();

        // 3. Guru can login via frontend /login -> redirects to /dashboard
        $guruResponse = $this->post('/login', [
            'email' => 'guru@lms.com',
            'password' => 'password',
        ]);
        $guruResponse->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_guru_can_view_admin_dashboard_with_all_metrics_and_discussions(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $response = $this->actingAs($guru)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');
        $response->assertSee('Komentar Terbaru dari Siswa');
        $response->assertSee('Akses Cepat Pengelolaan');
        $response->assertSee('Lihat Portal Frontend');
        $response->assertSee('TUGAS PERLU NILAI');
        $response->assertSee('KUIS &amp; UJIAN AKTIF', false);
    }
}
