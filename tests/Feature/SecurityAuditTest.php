<?php

namespace Tests\Feature;

use App\Models\Discussion;
use App\Models\Material;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private User $admin;
    private SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();

        $studentRole = Role::create(['name' => 'siswa', 'display_name' => 'Siswa']);
        $guruRole = Role::create(['name' => 'guru', 'display_name' => 'Guru']);

        $this->class = SchoolClass::create([
            'name' => 'Kelas X Security',
            'code' => 'SEC-101',
        ]);

        $this->student = User::factory()->create([
            'role_id' => $studentRole->id,
            'class_id' => $this->class->id,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $guruRole->id,
        ]);
    }

    public function test_sqli_payload_in_query_params_does_not_cause_sql_injection(): void
    {
        $sqliPayload = "' OR '1'='1' --";

        // Querying users with SQLi payload
        $response = $this->actingAs($this->admin)->get(route('admin.users.index', [
            'search' => $sqliPayload,
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('SQLSTATE');
    }

    public function test_xss_payload_in_discussion_comment_is_escaped_on_render(): void
    {
        $subject = Subject::create(['code' => 'SEC1', 'name' => 'Keamanan']);
        $material = new Material([
            'title' => 'Materi Keamanan Web',
            'content_type' => 'text',
            'content' => 'Safe Content',
        ]);
        $material->class_id = $this->class->id;
        $material->subject_id = $subject->id;
        $material->instructor_id = $this->admin->id;
        $material->save();

        $xssPayload = "<script>alert('XSS-TEST')</script>";

        // Post discussion comment with XSS payload
        $postResponse = $this->actingAs($this->student)->post(route('student.materials.discussions', $material), [
            'comment' => $xssPayload,
        ]);
        $postResponse->assertRedirect();

        // View material page
        $viewResponse = $this->actingAs($this->student)->get(route('student.materials.show', $material));
        $viewResponse->assertStatus(200);
        $viewResponse->assertDontSee("<script>alert('XSS-TEST')</script>", false);
        $viewResponse->assertSee("&lt;script&gt;alert(&#039;XSS-TEST&#039;)&lt;/script&gt;", false);
    }

    public function test_student_role_cannot_access_admin_crud_routes(): void
    {
        $adminRoutes = [
            route('admin.roles.index'),
            route('admin.users.index'),
            route('admin.classes.index'),
            route('admin.subjects.index'),
            route('admin.question-banks.index'),
            route('admin.materials.index'),
            route('admin.assignments.index'),
            route('admin.quizzes.index'),
            route('admin.submissions.index'),
            route('admin.reports.index'),
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($this->student)->get($route);
            $response->assertRedirect(route('dashboard'));
        }
    }

    public function test_guest_is_redirected_to_login_on_protected_routes(): void
    {
        $protectedRoutes = [
            route('dashboard'),
            route('student.materials.index'),
            route('student.assignments.index'),
            route('student.quizzes.index'),
            route('student.report.index'),
            route('admin.dashboard'),
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect(route('login'));
        }
    }

    public function test_storage_streaming_serves_existing_files_and_prevents_directory_traversal(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        \Illuminate\Support\Facades\Storage::disk('public')->put('question-images/test_img.png', 'fake_image_content');

        // Test normal access to storage file via streaming route
        $response = $this->get('/storage-file/question-images/test_img.png');
        $response->assertStatus(200);

        // Test directory traversal protection
        $badResponse = $this->get('/storage-file/../../../.env');
        $this->assertTrue(in_array($badResponse->status(), [404, 302]));
    }
}
