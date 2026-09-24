<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChunkUploadController extends Controller
{
    /**
     * Menerima chunk file, menyimpannya di temporary chunk folder,
     * dan menggabungkan semua chunk saat chunk terakhir terkirim.
     */
    public function uploadChunk(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file'],
            'chunk_index' => ['required', 'integer', 'min:0'],
            'total_chunks' => ['required', 'integer', 'min:1'],
            'file_uuid' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9_\-]+$/'],
            'original_filename' => ['required', 'string', 'max:255'],
            'target_folder' => ['nullable', 'string', 'in:material-banks,question-banks-temp,materials,documents'],
        ]);

        $file = $request->file('file');
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');
        $fileUuid = $request->input('file_uuid');
        $originalFilename = $request->input('original_filename');
        $targetFolder = $request->input('target_folder', 'material-banks');

        // Validasi ekstensi yang diizinkan (arsip .zip/.rar/.txt tidak diizinkan)
        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg', 'mp4', 'webm'];
        if (!in_array($extension, $allowedExtensions)) {
            return response()->json([
                'success' => false,
                'message' => "Format file (.{$extension}) tidak diizinkan. Silakan gunakan dokumen PDF, Word, PowerPoint, Excel, atau Gambar.",
            ], 422);
        }

        $chunkDir = storage_path("app/chunks/{$fileUuid}");
        if (!File::exists($chunkDir)) {
            File::makeDirectory($chunkDir, 0755, true);
        }

        // Simpan chunk saat ini
        $chunkFileName = sprintf('chunk_%05d', $chunkIndex);
        $chunkFilePath = $chunkDir . DIRECTORY_SEPARATOR . $chunkFileName;
        file_put_contents($chunkFilePath, file_get_contents($file->getRealPath()));

        // Periksa apakah seluruh chunk sudah terunggah
        $uploadedChunks = File::files($chunkDir);
        if (count($uploadedChunks) < $totalChunks) {
            return response()->json([
                'success' => true,
                'completed' => false,
                'chunk_index' => $chunkIndex,
                'uploaded_count' => count($uploadedChunks),
                'total_chunks' => $totalChunks,
            ]);
        }

        // Seluruh chunk lengkap -> Gabungkan (Merge)
        $uniqueName = Str::random(40) . '.' . $extension;
        $relativeDestPath = "{$targetFolder}/{$uniqueName}";
        $finalDisk = 'public';
        $finalFullPath = Storage::disk($finalDisk)->path($relativeDestPath);

        // Buat folder tujuan jika belum ada
        $finalDir = dirname($finalFullPath);
        if (!File::exists($finalDir)) {
            File::makeDirectory($finalDir, 0755, true);
        }

        $outHandle = fopen($finalFullPath, 'wb');
        if (!$outHandle) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat file tujuan di server.',
            ], 500);
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            $currentChunkFile = $chunkDir . '/' . sprintf('chunk_%05d', $i);
            if (!File::exists($currentChunkFile)) {
                fclose($outHandle);
                return response()->json([
                    'success' => false,
                    'message' => "Potongan file indeks ke-{$i} hilang. Silakan ulangi unggah.",
                ], 422);
            }

            $inHandle = fopen($currentChunkFile, 'rb');
            while (!feof($inHandle)) {
                fwrite($outHandle, fread($inHandle, 1048576)); // 1MB buffer read
            }
            fclose($inHandle);
        }
        fclose($outHandle);

        // Hapus temporary chunk folder
        File::deleteDirectory($chunkDir);

        return response()->json([
            'success' => true,
            'completed' => true,
            'file_path' => $relativeDestPath,
            'file_url' => asset("storage/{$relativeDestPath}"),
            'original_filename' => $originalFilename,
            'file_size' => File::size($finalFullPath),
            'message' => 'File berhasil diunggah dan dirangkai sempurna.',
        ]);
    }

    /**
     * Membatalkan / membersihkan pecahan chunk sementara jika upload dibatalkan.
     */
    public function cancelChunk(Request $request): JsonResponse
    {
        $request->validate([
            'file_uuid' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9_\-]+$/'],
        ]);

        $uuid = $request->input('file_uuid');
        $chunkDir = storage_path("app/chunks/{$uuid}");
        
        if (File::isDirectory($chunkDir)) {
            File::deleteDirectory($chunkDir);
            if (File::isDirectory($chunkDir)) {
                @rmdir($chunkDir);
            }
            clearstatcache();
        }

        return response()->json([
            'success' => true,
            'message' => 'Pecahan file sementara berhasil dibersihkan.',
        ]);
    }
}
