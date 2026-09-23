<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\QuestionBankOption;
use App\Services\DocumentQuestionParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QuestionBankController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $type = $request->query('type');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $questionBanks = QuestionBank::with('options')
            ->withCount('options')
            ->where('instructor_id', Auth::id())
            ->when($search, function ($query, $search) {
                $query->where('question_text', 'like', "%{$search}%");
            })
            ->when($type, function ($query, $type) {
                $query->where('question_type', $type);
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.question-banks.index', compact('questionBanks', 'search', 'type', 'perPage'));
    }

    public function create(): View
    {
        return view('admin.question-banks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Batch / Bulk Questions Processing
        if ($request->has('questions') && is_array($request->input('questions')) && count($request->input('questions')) > 0) {
            $createdCount = 0;

            DB::transaction(function () use ($request, &$createdCount) {
                foreach ($request->input('questions') as $qData) {
                    $qType = $qData['question_type'] ?? 'multiple_choice';
                    $qText = trim($qData['question_text'] ?? '');
                    if ($qText === '') {
                        if ($qType === 'matching') {
                            $qText = 'Jodohkanlah item berikut dengan pasangannya yang benar:';
                        } else {
                            continue;
                        }
                    }

                    if ($qType === 'true_false') {
                        $correctTf = $qData['correct_tf'] ?? ($qData['tf_correct_answer'] ?? 'Benar');
                        $qb = QuestionBank::create([
                            'instructor_id' => Auth::id(),
                            'question_text' => $qText,
                            'question_type' => 'true_false',
                        ]);

                        QuestionBankOption::create([
                            'question_bank_id' => $qb->id,
                            'option_text' => 'Benar',
                            'is_correct' => ($correctTf === 'Benar'),
                        ]);
                        QuestionBankOption::create([
                            'question_bank_id' => $qb->id,
                            'option_text' => 'Salah',
                            'is_correct' => ($correctTf === 'Salah'),
                        ]);
                        $createdCount++;
                    } elseif ($qType === 'matching') {
                        $pairs = $qData['pairs'] ?? ($qData['matching_pairs'] ?? []);
                        if (is_array($pairs) && count($pairs) >= 2) {
                            $qb = QuestionBank::create([
                                'instructor_id' => Auth::id(),
                                'question_text' => $qText,
                                'question_type' => 'matching',
                            ]);

                            foreach ($pairs as $pair) {
                                if (!empty($pair['premise']) && !empty($pair['match'])) {
                                    QuestionBankOption::create([
                                        'question_bank_id' => $qb->id,
                                        'option_text' => trim($pair['premise']),
                                        'match_text' => trim($pair['match']),
                                        'is_correct' => true,
                                    ]);
                                }
                            }
                            $createdCount++;
                        }
                    } else {
                        // Multiple Choice
                        $options = $qData['options'] ?? [];
                        $filteredOptions = [];
                        $rawCorrectOpt = isset($qData['correct_option']) ? (int)$qData['correct_option'] : 0;
                        $correctOptText = $options[$rawCorrectOpt] ?? null;

                        foreach ($options as $idx => $optText) {
                            $t = trim($optText);
                            if ($t !== '') {
                                $filteredOptions[] = [
                                    'text' => $t,
                                    'is_correct' => ($idx === $rawCorrectOpt || ($correctOptText !== null && $t === trim($correctOptText)))
                                ];
                            }
                        }

                        if (count($filteredOptions) >= 2) {
                            $qb = QuestionBank::create([
                                'instructor_id' => Auth::id(),
                                'question_text' => $qText,
                                'question_type' => 'multiple_choice',
                            ]);

                            $hasCorrect = false;
                            foreach ($filteredOptions as $fOpt) {
                                if ($fOpt['is_correct']) {
                                    $hasCorrect = true;
                                    break;
                                }
                            }
                            if (!$hasCorrect) {
                                $filteredOptions[0]['is_correct'] = true;
                            }

                            foreach ($filteredOptions as $fOpt) {
                                QuestionBankOption::create([
                                    'question_bank_id' => $qb->id,
                                    'option_text' => $fOpt['text'],
                                    'is_correct' => $fOpt['is_correct'],
                                ]);
                            }
                            $createdCount++;
                        }
                    }
                }
            });

            if ($createdCount > 0) {
                return redirect()->route('admin.question-banks.index')->with('success', $createdCount . ' butir soal berhasil ditambahkan ke Bank Soal.');
            }

            return back()->with('error', 'Tidak ada butir soal yang valid untuk disimpan.');
        }

        // 2. Single Question Fallback
        $questionType = $request->input('question_type', 'multiple_choice');

        if ($questionType === 'true_false') {
            $request->validate([
                'question_text' => ['required', 'string'],
                'correct_tf' => ['required', 'in:Benar,Salah'],
            ]);

            DB::transaction(function () use ($request) {
                $questionBank = QuestionBank::create([
                    'instructor_id' => Auth::id(),
                    'question_text' => trim($request->question_text),
                    'question_type' => 'true_false',
                ]);

                QuestionBankOption::create([
                    'question_bank_id' => $questionBank->id,
                    'option_text' => 'Benar',
                    'is_correct' => ($request->correct_tf === 'Benar'),
                ]);

                QuestionBankOption::create([
                    'question_bank_id' => $questionBank->id,
                    'option_text' => 'Salah',
                    'is_correct' => ($request->correct_tf === 'Salah'),
                ]);
            });
        } elseif ($questionType === 'matching') {
            $qText = trim($request->input('question_text', ''));
            if ($qText === '') {
                $qText = 'Jodohkanlah item berikut dengan pasangannya yang benar:';
            }
            $request->validate([
                'pairs' => ['required', 'array', 'min:2'],
                'pairs.*.premise' => ['required', 'string'],
                'pairs.*.match' => ['required', 'string'],
            ]);

            DB::transaction(function () use ($request, $qText) {
                $questionBank = QuestionBank::create([
                    'instructor_id' => Auth::id(),
                    'question_text' => $qText,
                    'question_type' => 'matching',
                ]);

                foreach ($request->pairs as $pair) {
                    QuestionBankOption::create([
                        'question_bank_id' => $questionBank->id,
                        'option_text' => trim($pair['premise']),
                        'match_text' => trim($pair['match']),
                        'is_correct' => true,
                    ]);
                }
            });
        } else {
            $request->validate([
                'question_text' => ['required', 'string'],
                'options' => ['required', 'array', 'min:2'],
                'options.*' => ['required', 'string'],
                'correct_option' => ['required', 'integer', 'min:0'],
            ]);

            DB::transaction(function () use ($request) {
                $questionBank = QuestionBank::create([
                    'instructor_id' => Auth::id(),
                    'question_text' => trim($request->question_text),
                    'question_type' => 'multiple_choice',
                ]);

                foreach ($request->options as $index => $optionText) {
                    QuestionBankOption::create([
                        'question_bank_id' => $questionBank->id,
                        'option_text' => trim($optionText),
                        'is_correct' => (int) $index === (int) $request->correct_option,
                    ]);
                }
            });
        }

        return redirect()->route('admin.question-banks.index')->with('success', 'Soal berhasil ditambahkan ke Bank Soal.');
    }

    public function edit(QuestionBank $questionBank): View
    {
        $questionBank->load('options');

        return view('admin.question-banks.edit', compact('questionBank'));
    }

    public function update(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $questionType = $request->input('question_type', $questionBank->question_type ?? 'multiple_choice');

        if ($questionType === 'true_false') {
            $request->validate([
                'question_text' => ['required', 'string'],
                'correct_tf' => ['required', 'in:Benar,Salah'],
            ]);

            DB::transaction(function () use ($request, $questionBank) {
                $questionBank->update([
                    'question_text' => trim($request->question_text),
                    'question_type' => 'true_false',
                ]);

                $questionBank->options()->delete();

                QuestionBankOption::create([
                    'question_bank_id' => $questionBank->id,
                    'option_text' => 'Benar',
                    'is_correct' => ($request->correct_tf === 'Benar'),
                ]);

                QuestionBankOption::create([
                    'question_bank_id' => $questionBank->id,
                    'option_text' => 'Salah',
                    'is_correct' => ($request->correct_tf === 'Salah'),
                ]);
            });
        } elseif ($questionType === 'matching') {
            $qText = trim($request->input('question_text', ''));
            if ($qText === '') {
                $qText = 'Jodohkanlah item berikut dengan pasangannya yang benar:';
            }
            $request->validate([
                'pairs' => ['required', 'array', 'min:2'],
                'pairs.*.premise' => ['required', 'string'],
                'pairs.*.match' => ['required', 'string'],
            ]);

            DB::transaction(function () use ($request, $questionBank, $qText) {
                $questionBank->update([
                    'question_text' => $qText,
                    'question_type' => 'matching',
                ]);

                $questionBank->options()->delete();

                foreach ($request->pairs as $pair) {
                    QuestionBankOption::create([
                        'question_bank_id' => $questionBank->id,
                        'option_text' => trim($pair['premise']),
                        'match_text' => trim($pair['match']),
                        'is_correct' => true,
                    ]);
                }
            });
        } else {
            $request->validate([
                'question_text' => ['required', 'string'],
                'options' => ['required', 'array', 'min:2'],
                'options.*' => ['required', 'string'],
                'correct_option' => ['required', 'integer', 'min:0'],
            ]);

            DB::transaction(function () use ($request, $questionBank) {
                $questionBank->update([
                    'question_text' => trim($request->question_text),
                    'question_type' => 'multiple_choice',
                ]);

                $questionBank->options()->delete();

                foreach ($request->options as $index => $optionText) {
                    QuestionBankOption::create([
                        'question_bank_id' => $questionBank->id,
                        'option_text' => trim($optionText),
                        'is_correct' => (int) $index === (int) $request->correct_option,
                    ]);
                }
            });
        }

        return redirect()->route('admin.question-banks.index')->with('success', 'Soal di Bank Soal berhasil diperbarui.');
    }

    public function destroy(QuestionBank $questionBank): RedirectResponse
    {
        $questionBank->delete();

        return redirect()->route('admin.question-banks.index')->with('success', 'Soal di Bank Soal berhasil dihapus.');
    }

    /**
     * Download contoh template format naskah soal (.docx atau .txt).
     */
    public function downloadTemplate(Request $request, DocumentQuestionParserService $parser)
    {
        $format = strtolower($request->query('format', 'docx'));

        if ($format === 'txt') {
            $content = $parser->getTextTemplateContent();
            return response($content, 200, [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="Template_Format_Soal_LMS.txt"',
            ]);
        }

        // Default: docx
        $filePath = $parser->generateDocxTemplate();
        return response()->download($filePath, 'Template_Format_Soal_LMS.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Upload dan simpan langsung butir-butir soal dari file dokumen Word (.docx) atau PDF (.pdf).
     */
    public function importDocument(Request $request, DocumentQuestionParserService $parser): RedirectResponse
    {
        $request->validate([
            'document_file' => ['nullable', 'file', 'mimes:docx,pdf,txt,doc', 'max:20480'],
            'document_chunk_path' => ['nullable', 'string'],
            'original_filename' => ['nullable', 'string'],
        ]);

        if (!$request->hasFile('document_file') && !$request->filled('document_chunk_path')) {
            return back()->with('error', 'Silakan pilih file dokumen naskah soal terlebih dahulu.');
        }

        try {
            if ($request->filled('document_chunk_path') && Storage::disk('public')->exists($request->document_chunk_path)) {
                $path = Storage::disk('public')->path($request->document_chunk_path);
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $displayName = $request->input('original_filename', basename($path));
                $isChunk = true;
            } else {
                $file = $request->file('document_file');
                $extension = $file->getClientOriginalExtension();
                $path = $file->getRealPath();
                $displayName = $file->getClientOriginalName();
                $isChunk = false;
            }

            $extractedText = $parser->extractTextFromFile($path, $extension);
            if (empty(trim($extractedText))) {
                return back()->with('error', 'Tidak dapat mengekstrak teks dari file dokumen tersebut. Pastikan dokumen bukan hasil scan gambar murni tanpa teks.');
            }

            $questions = $parser->parseQuestionsFromText($extractedText);
            if (empty($questions)) {
                return back()->with('error', 'Format butir soal dalam dokumen tidak terdeteksi. Pastikan naskah soal memiliki nomor urut (contoh: 1. Pertanyaan) dan opsi (A., B., Kunci).');
            }

            $createdCount = 0;
            $instructorId = Auth::id();

            DB::transaction(function () use ($questions, $instructorId, &$createdCount) {
                foreach ($questions as $qData) {
                    $qType = $qData['question_type'] ?? 'multiple_choice';
                    $qText = trim($qData['question_text'] ?? '');
                    if ($qText === '') {
                        if ($qType === 'matching') {
                            $qText = 'Jodohkanlah item berikut dengan pasangannya yang benar:';
                        } else {
                            continue;
                        }
                    }

                    if ($qType === 'true_false') {
                        $correctTf = $qData['correct_tf'] ?? 'Benar';
                        $qb = QuestionBank::create([
                            'instructor_id' => $instructorId,
                            'question_text' => $qText,
                            'question_type' => 'true_false',
                        ]);

                        QuestionBankOption::create([
                            'question_bank_id' => $qb->id,
                            'option_text' => 'Benar',
                            'is_correct' => ($correctTf === 'Benar'),
                        ]);
                        QuestionBankOption::create([
                            'question_bank_id' => $qb->id,
                            'option_text' => 'Salah',
                            'is_correct' => ($correctTf === 'Salah'),
                        ]);
                        $createdCount++;
                    } elseif ($qType === 'matching') {
                        $pairs = $qData['pairs'] ?? [];
                        if (is_array($pairs) && count($pairs) >= 2) {
                            $qb = QuestionBank::create([
                                'instructor_id' => $instructorId,
                                'question_text' => $qText,
                                'question_type' => 'matching',
                            ]);

                            foreach ($pairs as $pair) {
                                if (!empty($pair['premise']) && !empty($pair['match'])) {
                                    QuestionBankOption::create([
                                        'question_bank_id' => $qb->id,
                                        'option_text' => trim($pair['premise']),
                                        'match_text' => trim($pair['match']),
                                        'is_correct' => true,
                                    ]);
                                }
                            }
                            $createdCount++;
                        }
                    } else {
                        // Multiple choice
                        $options = $qData['options'] ?? [];
                        $filteredOptions = [];
                        $rawCorrectOpt = isset($qData['correct_option']) ? (int)$qData['correct_option'] : 0;

                        foreach ($options as $idx => $optText) {
                            $t = trim($optText);
                            if ($t !== '') {
                                $filteredOptions[] = [
                                    'text' => $t,
                                    'is_correct' => ($idx === $rawCorrectOpt),
                                ];
                            }
                        }

                        if (count($filteredOptions) >= 2) {
                            $qb = QuestionBank::create([
                                'instructor_id' => $instructorId,
                                'question_text' => $qText,
                                'question_type' => 'multiple_choice',
                            ]);

                            $hasCorrect = false;
                            foreach ($filteredOptions as $fOpt) {
                                if ($fOpt['is_correct']) {
                                    $hasCorrect = true;
                                    break;
                                }
                            }
                            if (!$hasCorrect) {
                                $filteredOptions[0]['is_correct'] = true;
                            }

                            foreach ($filteredOptions as $fOpt) {
                                QuestionBankOption::create([
                                    'question_bank_id' => $qb->id,
                                    'option_text' => $fOpt['text'],
                                    'is_correct' => $fOpt['is_correct'],
                                ]);
                            }
                            $createdCount++;
                        }
                    }
                }
            });

            // Hapus file temporary chunk jika berasal dari chunked upload
            if ($isChunk && !empty($request->document_chunk_path)) {
                Storage::disk('public')->delete($request->document_chunk_path);
            }

            return redirect()->route('admin.question-banks.index')
                ->with('success', "Berhasil! {$createdCount} butir soal dari file {$displayName} langsung tersimpan di Bank Soal.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Parse file dokumen Word (.docx) atau PDF (.pdf) menjadi butir-butir soal.
     */
    public function parseDocument(Request $request, DocumentQuestionParserService $parser): JsonResponse
    {
        $request->validate([
            'document_file' => ['nullable', 'file', 'mimes:docx,pdf,txt,doc', 'max:20480'],
            'document_chunk_path' => ['nullable', 'string'],
        ]);

        if (!$request->hasFile('document_file') && !$request->filled('document_chunk_path')) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan pilih file dokumen naskah soal terlebih dahulu.',
            ], 422);
        }

        try {
            $isChunk = false;
            if ($request->filled('document_chunk_path') && Storage::disk('public')->exists($request->document_chunk_path)) {
                $path = Storage::disk('public')->path($request->document_chunk_path);
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $isChunk = true;
            } else {
                $file = $request->file('document_file');
                $extension = $file->getClientOriginalExtension();
                $path = $file->getRealPath();
            }

            $extractedText = $parser->extractTextFromFile($path, $extension);
            if (empty(trim($extractedText))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengekstrak teks dari file dokumen tersebut. Pastikan dokumen bukan hasil scan gambar murni tanpa teks.',
                ], 422);
            }

            $questions = $parser->parseQuestionsFromText($extractedText);

            // Bersihkan file temporary chunk setelah selesai diparse
            if ($isChunk && !empty($request->document_chunk_path)) {
                Storage::disk('public')->delete($request->document_chunk_path);
            }

            return response()->json([
                'success' => true,
                'raw_text' => $extractedText,
                'count' => count($questions),
                'questions' => $questions,
                'message' => count($questions) . ' butir soal berhasil dideteksi dari dokumen.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membaca dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload gambar dari editor soal (TinyMCE atau form gambar custom).
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:10240'],
        ]);

        try {
            $file = $request->file('file');
            $filename = 'qb_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('question-images', $filename, 'public');

            $url = asset('storage/' . $path);

            return response()->json([
                'location' => $url,
                'url' => $url,
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Gagal mengunggah gambar: ' . $e->getMessage(),
            ], 500);
        }
    }
}

