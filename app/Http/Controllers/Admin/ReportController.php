<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman rekapitulasi nilai dan progres belajar siswa.
     */
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        $selectedClassId = $request->get('class_id', $classes->first()?->id);
        $selectedSubjectId = $request->get('subject_id');

        $studentsQuery = User::whereHas('role', function ($q) {
            $q->where('name', 'siswa');
        });

        if ($selectedClassId) {
            $studentsQuery->where('class_id', $selectedClassId);
        }

        $students = $studentsQuery->with(['schoolClass'])->orderBy('name')->get();

        // Hitung total materi berdasarkan filter kelas dan mapel
        $totalMaterials = Material::when($selectedClassId, fn($q) => $q->where('class_id', $selectedClassId))
            ->when($selectedSubjectId, fn($q) => $q->where('subject_id', $selectedSubjectId))
            ->count();

        foreach ($students as $student) {
            $completedMaterials = MaterialProgress::where('user_id', $student->id)
                ->where('is_completed', true)
                ->when($selectedSubjectId, function ($q) use ($selectedSubjectId) {
                    $q->whereHas('material', fn($m) => $m->where('subject_id', $selectedSubjectId));
                })
                ->count();

            $student->materials_percentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100, 1) : 0;
            $student->materials_completed_count = $completedMaterials;

            $avgAssignment = AssignmentSubmission::where('student_id', $student->id)
                ->whereNotNull('grade')
                ->when($selectedSubjectId, function ($q) use ($selectedSubjectId) {
                    $q->whereHas('assignment', fn($a) => $a->where('subject_id', $selectedSubjectId));
                })
                ->avg('grade');

            $student->avg_assignment_grade = $avgAssignment ? round($avgAssignment, 1) : null;

            $avgQuiz = QuizAttempt::where('student_id', $student->id)
                ->whereNotNull('score')
                ->when($selectedSubjectId, function ($q) use ($selectedSubjectId) {
                    $q->whereHas('quiz', fn($qz) => $qz->where('subject_id', $selectedSubjectId));
                })
                ->avg('score');

            $student->avg_quiz_score = $avgQuiz ? round($avgQuiz, 1) : null;

            // Hitung nilai akhir gabungan
            $evalCount = ($avgAssignment !== null ? 1 : 0) + ($avgQuiz !== null ? 1 : 0);
            $evalSum = ($avgAssignment ?? 0) + ($avgQuiz ?? 0);
            $student->overall_score = $evalCount > 0 ? round($evalSum / $evalCount, 1) : null;
        }

        return view('admin.reports.index', compact(
            'students',
            'classes',
            'subjects',
            'selectedClassId',
            'selectedSubjectId',
            'totalMaterials'
        ));
    }

    /**
     * Export rekapitulasi nilai siswa dalam format Microsoft Excel (.xlsx) yang rapi, profesional, dan berdesain modern.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');

        $classModel = $selectedClassId ? SchoolClass::find($selectedClassId) : null;
        $subjectModel = $selectedSubjectId ? Subject::find($selectedSubjectId) : null;

        $studentsQuery = User::whereHas('role', fn($q) => $q->where('name', 'siswa'));
        if ($selectedClassId) {
            $studentsQuery->where('class_id', $selectedClassId);
        }
        $students = $studentsQuery->with('schoolClass')->orderBy('name')->get();

        $totalMaterials = Material::when($selectedClassId, fn($q) => $q->where('class_id', $selectedClassId))
            ->when($selectedSubjectId, fn($q) => $q->where('subject_id', $selectedSubjectId))
            ->count();

        $className = $classModel ? $classModel->name : 'Semua Kelas';
        $subjectName = $subjectModel ? $subjectModel->name . ' (' . $subjectModel->code . ')' : 'Semua Mata Pelajaran';
        $exportDate = now()->translatedFormat('d F Y H:i');
        $fileName = 'Rekap_Nilai_' . Str::slug($className) . '_' . now()->format('Ymd_His') . '.xlsx';

        // 1. Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator(config('app.name', 'LMS'))
            ->setLastModifiedBy(config('app.name', 'LMS'))
            ->setTitle('Rekapitulasi Nilai & Progres Siswa - ' . $className)
            ->setSubject('Laporan Nilai Siswa')
            ->setDescription('Rekapitulasi Hasil Belajar, Progres Materi, Tugas, dan Kuis Siswa.');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai');
        $sheet->setShowGridLines(true);

        // 2. Banner Judul Utama (Row 1 - 2)
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', 'LAPORAN & REKAPITULASI HASIL BELAJAR SISWA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A'); // Deep Navy Blue
        $sheet->getRowDimension(1)->setRowHeight(32);

        $sheet->mergeCells('A2:M2');
        $sheet->setCellValue('A2', config('app.name', 'LMS Learning Management System') . ' • Waktu Unduh: ' . $exportDate . ' WIB');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9.5)->getColor()->setRGB('DBEAFE');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
        $sheet->getRowDimension(2)->setRowHeight(20);

        // 3. Metadata Box Informasi Laporan (Row 4 - 5)
        $sheet->setCellValue('B4', 'Filter Kelas');
        $sheet->setCellValue('C4', ': ' . $className);
        $sheet->setCellValue('F4', 'Filter Mata Pelajaran');
        $sheet->setCellValue('G4', ': ' . $subjectName);

        $sheet->setCellValue('B5', 'Total Siswa');
        $sheet->setCellValue('C5', ': ' . $students->count() . ' Siswa Terdaftar');
        $sheet->setCellValue('F5', 'Total Materi Kelas');
        $sheet->setCellValue('G5', ': ' . $totalMaterials . ' Modul Materi');

        // Style Metadata
        $sheet->getStyle('B4:B5')->getFont()->setBold(true)->setSize(9)->getColor()->setRGB('475569');
        $sheet->getStyle('F4:F5')->getFont()->setBold(true)->setSize(9)->getColor()->setRGB('475569');
        $sheet->getStyle('C4:C5')->getFont()->setSize(9)->getColor()->setRGB('0F172A');
        $sheet->getStyle('G4:G5')->getFont()->setSize(9)->getColor()->setRGB('0F172A');

        $metadataBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8FAFC'],
            ],
        ];
        $sheet->getStyle('A4:M5')->applyFromArray($metadataBorder);

        // 4. Header Kolom Tabel (Row 7)
        $headers = [
            'A7' => 'No',
            'B7' => 'ID Siswa',
            'C7' => 'Nama Lengkap Siswa',
            'D7' => 'Email Siswa',
            'E7' => 'Kelas',
            'F7' => 'Materi Selesai',
            'G7' => 'Total Materi',
            'H7' => 'Progres Materi',
            'I7' => 'Rata-rata Tugas',
            'J7' => 'Rata-rata Kuis',
            'K7' => 'Nilai Akhir',
            'L7' => 'Predikat',
            'M7' => 'Status Evaluasi',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 9.5,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Royal Blue
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1D4ED8'],
                ],
            ],
        ];
        $sheet->getStyle('A7:M7')->applyFromArray($headerStyle);
        $sheet->getRowDimension(7)->setRowHeight(28);

        // 5. Data Rows (Row 8 dst)
        $currentRow = 8;
        $totalMaterialPercentages = [];
        $totalAssignmentGrades = [];
        $totalQuizScores = [];
        $totalOverallScores = [];

        foreach ($students as $index => $student) {
            $completedMaterials = MaterialProgress::where('user_id', $student->id)
                ->where('is_completed', true)
                ->when($selectedSubjectId, function ($q) use ($selectedSubjectId) {
                    $q->whereHas('material', fn($m) => $m->where('subject_id', $selectedSubjectId));
                })->count();

            $matPct = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100, 1) : 0;
            $totalMaterialPercentages[] = $matPct;

            $avgAss = AssignmentSubmission::where('student_id', $student->id)
                ->whereNotNull('grade')
                ->when($selectedSubjectId, fn($q) => $q->whereHas('assignment', fn($a) => $a->where('subject_id', $selectedSubjectId)))
                ->avg('grade');

            $avgQz = QuizAttempt::where('student_id', $student->id)
                ->whereNotNull('score')
                ->when($selectedSubjectId, fn($q) => $q->whereHas('quiz', fn($qz) => $qz->where('subject_id', $selectedSubjectId)))
                ->avg('score');

            $evalCount = ($avgAss !== null ? 1 : 0) + ($avgQz !== null ? 1 : 0);
            $evalSum = ($avgAss ?? 0) + ($avgQz ?? 0);
            $overallScore = $evalCount > 0 ? round($evalSum / $evalCount, 1) : null;

            if ($avgAss !== null) {
                $totalAssignmentGrades[] = $avgAss;
            }
            if ($avgQz !== null) {
                $totalQuizScores[] = $avgQz;
            }
            if ($overallScore !== null) {
                $totalOverallScores[] = $overallScore;
            }

            // Tentukan Predikat Nilai & Warna
            $predicateColor = '64748B';
            if ($overallScore !== null) {
                if ($overallScore >= 85) {
                    $predicate = 'A (Sangat Baik)';
                    $predicateColor = '047857'; // Emerald Green
                } elseif ($overallScore >= 75) {
                    $predicate = 'B (Baik)';
                    $predicateColor = '1D4ED8'; // Blue
                } elseif ($overallScore >= 65) {
                    $predicate = 'C (Cukup)';
                    $predicateColor = 'D97706'; // Amber
                } else {
                    $predicate = 'D (Perlu Bimbingan)';
                    $predicateColor = 'DC2626'; // Red
                }
            } else {
                $predicate = '-';
            }

            // Tentukan Status Evaluasi
            if ($avgAss !== null && $avgQz !== null) {
                $status = 'Lengkap (Tugas & Kuis)';
            } elseif ($avgAss !== null) {
                $status = 'Hanya Tugas';
            } elseif ($avgQz !== null) {
                $status = 'Hanya Kuis';
            } else {
                $status = 'Belum Ada Evaluasi';
            }

            // Set Values
            $sheet->setCellValue('A' . $currentRow, $index + 1);
            $sheet->setCellValue('B' . $currentRow, $student->id);
            $sheet->setCellValue('C' . $currentRow, $student->name);
            $sheet->setCellValue('D' . $currentRow, $student->email);
            $sheet->setCellValue('E' . $currentRow, $student->schoolClass->name ?? '-');
            $sheet->setCellValue('F' . $currentRow, $completedMaterials);
            $sheet->setCellValue('G' . $currentRow, $totalMaterials);
            $sheet->setCellValue('H' . $currentRow, $matPct . '%');
            $sheet->setCellValue('I' . $currentRow, $avgAss !== null ? number_format($avgAss, 1) : '-');
            $sheet->setCellValue('J' . $currentRow, $avgQz !== null ? number_format($avgQz, 1) : '-');
            $sheet->setCellValue('K' . $currentRow, $overallScore !== null ? number_format($overallScore, 1) : '-');
            $sheet->setCellValue('L' . $currentRow, $predicate);
            $sheet->setCellValue('M' . $currentRow, $status);

            // Styling Baris
            $bgColor = ($index % 2 === 0) ? 'FFFFFF' : 'F8FAFC';
            $rowStyle = [
                'font' => ['size' => 9, 'color' => ['rgb' => '1E293B']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ];
            $sheet->getStyle('A' . $currentRow . ':M' . $currentRow)->applyFromArray($rowStyle);

            // Alignments spesifik
            $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $currentRow . ':D' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('E' . $currentRow . ':M' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Highlight Nilai Akhir & Predikat
            if ($overallScore !== null) {
                $sheet->getStyle('K' . $currentRow)->getFont()->setBold(true)->getColor()->setRGB($predicateColor);
                $sheet->getStyle('L' . $currentRow)->getFont()->setBold(true)->getColor()->setRGB($predicateColor);
            }

            $sheet->getRowDimension($currentRow)->setRowHeight(22);
            $currentRow++;
        }

        // 6. Ringkasan / Footer Statistik Kelas (Di bawah data)
        $summaryStartRow = $currentRow + 1;

        $avgMatClass = count($totalMaterialPercentages) > 0 ? round(array_sum($totalMaterialPercentages) / count($totalMaterialPercentages), 1) : 0;
        $avgAssClass = count($totalAssignmentGrades) > 0 ? round(array_sum($totalAssignmentGrades) / count($totalAssignmentGrades), 1) : null;
        $avgQzClass = count($totalQuizScores) > 0 ? round(array_sum($totalQuizScores) / count($totalQuizScores), 1) : null;
        $avgOverallClass = count($totalOverallScores) > 0 ? round(array_sum($totalOverallScores) / count($totalOverallScores), 1) : null;

        // Header Ringkasan
        $sheet->mergeCells('B' . $summaryStartRow . ':G' . $summaryStartRow);
        $sheet->setCellValue('B' . $summaryStartRow, 'RINGKASAN & STATISTIK KELAS');
        $sheet->getStyle('B' . $summaryStartRow)->getFont()->setBold(true)->setSize(9.5)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('B' . $summaryStartRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B' . $summaryStartRow . ':G' . $summaryStartRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
        $sheet->getRowDimension($summaryStartRow)->setRowHeight(24);

        $summaryData = [
            ['Rata-rata Progres Materi Kelas', $avgMatClass . '%'],
            ['Rata-rata Nilai Tugas Siswa', $avgAssClass !== null ? number_format($avgAssClass, 1) : '-'],
            ['Rata-rata Nilai Kuis Siswa', $avgQzClass !== null ? number_format($avgQzClass, 1) : '-'],
            ['Rata-rata Nilai Akhir Gabungan', $avgOverallClass !== null ? number_format($avgOverallClass, 1) : '-'],
        ];

        $sRow = $summaryStartRow + 1;
        foreach ($summaryData as $item) {
            $sheet->mergeCells('B' . $sRow . ':E' . $sRow);
            $sheet->setCellValue('B' . $sRow, $item[0]);
            $sheet->mergeCells('F' . $sRow . ':G' . $sRow);
            $sheet->setCellValue('F' . $sRow, $item[1]);

            $sheet->getStyle('B' . $sRow)->getFont()->setSize(9)->setBold(true)->getColor()->setRGB('334155');
            $sheet->getStyle('F' . $sRow)->getFont()->setSize(9.5)->setBold(true)->getColor()->setRGB('1E40AF');
            $sheet->getStyle('F' . $sRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('B' . $sRow . ':G' . $sRow)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EFF6FF'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'BFDBFE'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($sRow)->setRowHeight(20);
            $sRow++;
        }

        // 7. Auto-fit Lebar Kolom
        $columns = range('A', 'M');
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze pane pada header tabel (baris 8)
        $sheet->freezePane('A8');

        // 8. Stream Response Xlsx
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->headers->set('Pragma', 'public');

        return $response;
    }

    /**
     * Alias untuk kompatibilitas route sebelumnya.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        return $this->exportExcel($request);
    }
}
