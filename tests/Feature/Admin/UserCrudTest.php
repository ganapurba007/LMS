<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guru_can_view_user_list_and_search(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $response = $this->actingAs($guru)->get('/admin/users?search=Budi');

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
    }

    public function test_guru_can_filter_users_by_role(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();
        $siswaRole = Role::where('name', 'siswa')->first();

        $response = $this->actingAs($guru)->get('/admin/users?role_id='.$siswaRole->id);

        $response->assertStatus(200);
        $response->assertViewHas('users', function ($users) {
            return $users->pluck('email')->contains('siswa1@lms.com')
                && !$users->pluck('email')->contains('guru@lms.com');
        });
    }

    public function test_guru_can_update_user_role_class_and_nip(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();
        $siswa = User::where('email', 'siswa1@lms.com')->first();
        $guruRole = Role::where('name', 'guru')->first();

        $response = $this->actingAs($guru)->put("/admin/users/{$siswa->id}", [
            'name' => 'Budi Promote Guru',
            'email' => 'siswa1@lms.com',
            'nip' => '199002022020022002',
            'role_id' => $guruRole->id,
            'class_id' => null,
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'id' => $siswa->id,
            'name' => 'Budi Promote Guru',
            'nip' => '199002022020022002',
            'role_id' => $guruRole->id,
        ]);
    }

    public function test_duplicate_nip_on_update_is_rejected(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();
        $siswa = User::where('email', 'siswa1@lms.com')->first();

        $response = $this->actingAs($guru)->put("/admin/users/{$siswa->id}", [
            'name' => 'Siswa Test',
            'email' => 'siswa1@lms.com',
            'nip' => '198501012010011001', // NIP milik Guru Dani
            'role_id' => $siswa->role_id,
        ]);

        $response->assertSessionHasErrors('nip');
    }

    public function test_siswa_cannot_access_user_management(): void
    {
        $siswa = User::where('email', 'siswa1@lms.com')->first();

        $response = $this->actingAs($siswa)->get('/admin/users');

        $response->assertRedirect('/dashboard');
    }

    public function test_guru_can_reset_user_password_with_generated_password(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();
        $siswa = User::where('email', 'siswa1@lms.com')->first();
        $oldPasswordHash = $siswa->password;

        $response = $this->actingAs($guru)->post("/admin/users/{$siswa->id}/reset-password");

        $response->assertRedirect();
        $response->assertSessionHas('reset_new_password');
        $newPassword = session('reset_new_password');
        $this->assertNotEmpty($newPassword);

        $siswa->refresh();
        $this->assertNotEquals($oldPasswordHash, $siswa->password);

        // Verify that the user can actually authenticate with the new password
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check($newPassword, $siswa->password));
        $this->assertTrue(\Illuminate\Support\Facades\Auth::attempt([
            'email' => $siswa->email,
            'password' => $newPassword,
        ]));
    }

    public function test_guru_can_reset_user_password_via_json_request(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();
        $siswa = User::where('email', 'siswa2@lms.com')->first();

        $response = $this->actingAs($guru)->postJson("/admin/users/{$siswa->id}/reset-password");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'user_name',
            'new_password',
        ]);

        $newPassword = $response->json('new_password');
        $this->assertNotEmpty($newPassword);

        $siswa->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check($newPassword, $siswa->password));
    }

    public function test_guru_can_update_user_password_manually(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();
        $siswa = User::where('email', 'siswa1@lms.com')->first();

        $response = $this->actingAs($guru)->put("/admin/users/{$siswa->id}", [
            'name' => 'Budi Santoso',
            'email' => 'siswa1@lms.com',
            'nip' => '',
            'role_id' => $siswa->role_id,
            'class_id' => $siswa->class_id,
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ]);

        $response->assertRedirect('/admin/users');
        $siswa->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('PasswordBaru123', $siswa->password));
        $this->assertNull($siswa->nip);
    }

    public function test_guru_cannot_update_user_with_mismatched_password_confirmation(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();
        $siswa = User::where('email', 'siswa1@lms.com')->first();

        $response = $this->actingAs($guru)->put("/admin/users/{$siswa->id}", [
            'name' => 'Budi Santoso',
            'email' => 'siswa1@lms.com',
            'role_id' => $siswa->role_id,
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'BedaPassword456',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
