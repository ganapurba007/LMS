<?php

namespace Tests\Feature\Admin;

use App\Models\MaterialBank;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChunkUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $roleGuru = Role::create(['name' => 'guru']);
        $this->guru = User::factory()->create(['role_id' => $roleGuru->id]);

        Storage::fake('public');
    }

    public function test_guest_cannot_upload_chunk(): void
    {
        $response = $this->postJson(route('admin.upload.chunk'), [
            'file' => UploadedFile::fake()->create('chunk.bin', 100),
            'chunk_index' => 0,
            'total_chunks' => 2,
            'file_uuid' => 'test_uuid_123',
            'original_filename' => 'sample.pdf',
        ]);

        $response->assertStatus(401);
    }

    public function test_disallowed_extension_is_rejected(): void
    {
        $response = $this->actingAs($this->guru)->postJson(route('admin.upload.chunk'), [
            'file' => UploadedFile::fake()->create('chunk.bin', 100),
            'chunk_index' => 0,
            'total_chunks' => 1,
            'file_uuid' => 'test_uuid_dangerous',
            'original_filename' => 'malicious.php',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);

        // Uji penolakan arsip ZIP dan RAR
        $resZip = $this->actingAs($this->guru)->postJson(route('admin.upload.chunk'), [
            'file' => UploadedFile::fake()->create('archive.zip', 100),
            'chunk_index' => 0,
            'total_chunks' => 1,
            'file_uuid' => 'test_uuid_zip',
            'original_filename' => 'archive.zip',
        ]);
        $resZip->assertStatus(422)->assertJson(['success' => false]);

        $resRar = $this->actingAs($this->guru)->postJson(route('admin.upload.chunk'), [
            'file' => UploadedFile::fake()->create('archive.rar', 100),
            'chunk_index' => 0,
            'total_chunks' => 1,
            'file_uuid' => 'test_uuid_rar',
            'original_filename' => 'archive.rar',
        ]);
        $resRar->assertStatus(422)->assertJson(['success' => false]);
    }

    public function test_chunked_upload_merges_successfully(): void
    {
        $uuid = 'test_uuid_' . uniqid();
        $chunk1Content = 'Hello ';
        $chunk2Content = 'World! This is a chunked upload test.';

        $chunk1 = UploadedFile::fake()->createWithContent('chunk_0', $chunk1Content);
        $chunk2 = UploadedFile::fake()->createWithContent('chunk_1', $chunk2Content);

        // Upload chunk 0
        $res1 = $this->actingAs($this->guru)->postJson(route('admin.upload.chunk'), [
            'file' => $chunk1,
            'chunk_index' => 0,
            'total_chunks' => 2,
            'file_uuid' => $uuid,
            'original_filename' => 'document.pdf',
            'target_folder' => 'material-banks',
        ]);

        $res1->assertOk()
            ->assertJson([
                'success' => true,
                'completed' => false,
                'chunk_index' => 0,
            ]);

        // Upload chunk 1 (final)
        $res2 = $this->actingAs($this->guru)->postJson(route('admin.upload.chunk'), [
            'file' => $chunk2,
            'chunk_index' => 1,
            'total_chunks' => 2,
            'file_uuid' => $uuid,
            'original_filename' => 'document.pdf',
            'target_folder' => 'material-banks',
        ]);

        $res2->assertOk()
            ->assertJson([
                'success' => true,
                'completed' => true,
                'original_filename' => 'document.pdf',
            ]);

        $filePath = $res2->json('file_path');
        $this->assertNotEmpty($filePath);
        Storage::disk('public')->assertExists($filePath);

        $mergedContent = Storage::disk('public')->get($filePath);
        $this->assertEquals($chunk1Content . $chunk2Content, $mergedContent);
    }

    public function test_material_bank_store_with_chunked_path(): void
    {
        // 1. Upload chunk
        $uuid = 'test_mb_' . uniqid();
        $content = 'LMS Material content test';
        $chunk = UploadedFile::fake()->createWithContent('chunk_0', $content);

        $resChunk = $this->actingAs($this->guru)->postJson(route('admin.upload.chunk'), [
            'file' => $chunk,
            'chunk_index' => 0,
            'total_chunks' => 1,
            'file_uuid' => $uuid,
            'original_filename' => 'materi-modul.pdf',
            'target_folder' => 'material-banks',
        ]);

        $resChunk->assertOk();
        $chunkPath = $resChunk->json('file_path');

        // 2. Submit MaterialBank form
        $response = $this->actingAs($this->guru)->post(route('admin.material-banks.store'), [
            'title' => 'Modul Chunked Upload',
            'document_chunk_path' => $chunkPath,
        ]);

        $response->assertRedirect(route('admin.material-banks.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('material_banks', [
            'instructor_id' => $this->guru->id,
            'title' => 'Modul Chunked Upload',
            'document_path' => $chunkPath,
            'content_type' => 'document',
        ]);
    }

    public function test_cancel_chunk_cleans_up_directory(): void
    {
        $uuid = 'test_cancel_' . uniqid();
        $chunk = UploadedFile::fake()->createWithContent('chunk_0', 'Draft');

        $this->actingAs($this->guru)->postJson(route('admin.upload.chunk'), [
            'file' => $chunk,
            'chunk_index' => 0,
            'total_chunks' => 3,
            'file_uuid' => $uuid,
            'original_filename' => 'draft.pdf',
        ]);

        $chunkDir = storage_path("app/chunks/{$uuid}");
        $this->assertTrue(File::exists($chunkDir));

        $resCancel = $this->actingAs($this->guru)->postJson(route('admin.upload.chunk.cancel'), [
            'file_uuid' => $uuid,
        ]);

        $resCancel->assertOk()->assertJson(['success' => true]);
        clearstatcache(true, $chunkDir);
        $this->assertFalse(File::exists($chunkDir));
    }
}
