<x-app-layout :suppressGlobalAlerts="true">
    <!-- Include Bootstrap, Tabler Icons & Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        /* Modern Sticky Header Bar (Lega, Mewah & Berjarak Nyaman) */
        .quiz-attempt-hero {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 60%, #2b5788 100%);
            border-bottom: 3px solid #66A3BF;
            color: #ffffff;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            padding-top: 1.15rem !important;
            padding-bottom: 1.15rem !important;
        }
        
        /* Single Question Card Stepper */
        .question-step-card {
            display: none;
            animation: fadeInQuestion 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .question-step-card.active {
            display: block;
        }
        @keyframes fadeInQuestion {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Compact & Balanced Question Card */
        .question-card-modern {
            background: #ffffff;
            border-radius: 16px;
            border: 1.5px solid rgba(51, 104, 160, 0.16);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: border-color 0.2s ease;
        }
        .question-card-header {
            padding: 0.85rem 1.35rem;
            background: #F2EFE7;
            border-bottom: 1px solid rgba(51, 104, 160, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        /* Review Option Tiles (Sama Bentuk & Spacing Dengan Pilihan Saat Kuis) */
        .review-option-tile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.85rem 1.15rem;
            border-radius: 12px;
            border: 1.5px solid rgba(51, 104, 160, 0.15);
            background: #ffffff;
            margin-bottom: 0.75rem;
            transition: all 0.15s ease-in-out;
        }
        .review-option-tile.tile-correct-selected {
            background: #ecfdf5;
            border-color: #10B981;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.15);
        }
        .review-option-tile.tile-correct-unselected {
            background: #f0fdf4;
            border-color: #10B981;
            border-style: dashed;
        }
        .review-option-tile.tile-wrong-selected {
            background: #fef2f2;
            border-color: #ef4444;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.15);
        }

        .option-badge-letter {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.88rem;
            margin-right: 0.75rem;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        /* Duration & Stats Badges */
        .duration-badge-box {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 50rem;
            padding: 7px 18px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        /* Navigasi Nomor Soal (Palette) Sticky Sidebar */
        .palette-sticky-card {
            position: sticky;
            top: 5.5rem;
            background: #ffffff;
            border-radius: 18px;
            border: 1.5px solid rgba(51, 104, 160, 0.16);
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.05);
            padding: 1.35rem 1.45rem !important;
            overflow: hidden;
        }
        .palette-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(36px, 1fr));
            gap: 8px;
        }
        .nav-question-btn {
            height: 36px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
            border: none;
        }
        
        /* Status Warna Nomor Soal Hasil Kuis */
        /* 1. Benar: Hijau */
        .nav-btn-correct {
            background-color: #10B981 !important;
            color: #ffffff !important;
            border: 1.5px solid #059669 !important;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
        }
        .nav-btn-correct:hover {
            background-color: #059669 !important;
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        /* 2. Salah: Merah */
        .nav-btn-wrong {
            background-color: #EF4444 !important;
            color: #ffffff !important;
            border: 1.5px solid #DC2626 !important;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);
        }
        .nav-btn-wrong:hover {
            background-color: #DC2626 !important;
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        /* 3. Tidak Dijawab: Abu-abu netral */
        .nav-btn-unanswered {
            background-color: #F1F5F9 !important;
            color: #475569 !important;
            border: 1.5px solid #CBD5E1 !important;
        }
        .nav-btn-unanswered:hover {
            background-color: #E2E8F0 !important;
            transform: translateY(-2px);
        }

        /* 4. Sedang Dilihat / Aktif: Ring Biru */
        .nav-btn-current {
            box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.45), 0 4px 10px rgba(37, 99, 235, 0.25) !important;
            transform: scale(1.08);
            z-index: 2;
        }

        /* Keterangan Warna (Legend) */
        .legend-box-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 10px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            transition: background-color 0.15s ease;
        }
        .legend-indicator-dot {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            flex-shrink: 0;
        }
        .legend-indicator-dot.dot-current {
            background-color: #2563EB;
            border: 1.5px solid #1D4ED8;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
        }
        .legend-indicator-dot.dot-correct {
            background-color: #10B981;
            border: 1.5px solid #059669;
        }
        .legend-indicator-dot.dot-wrong {
            background-color: #EF4444;
            border: 1.5px solid #DC2626;
        }
        .legend-indicator-dot.dot-unanswered {
            background-color: #F1F5F9;
            border: 1.5px solid #CBD5E1;
        }

        /* Mobile & Responsive Screen Adjustments (< 768px) */
        @media (max-width: 767.98px) {
            .quiz-attempt-hero {
                position: static !important;
                padding-top: 0.85rem !important;
                padding-bottom: 0.85rem !important;
            }
            .quiz-attempt-hero h1 {
                font-size: 1rem !important;
                line-height: 1.35 !important;
            }
            .duration-badge-box {
                padding: 5px 12px !important;
                font-size: 0.8rem !important;
            }
            .question-card-modern {
                border-radius: 14px !important;
                margin-bottom: 1.25rem !important;
            }
            .question-card-header {
                padding: 0.75rem 1rem !important;
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }
            .review-option-tile {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.6rem !important;
                padding: 0.85rem 1rem !important;
                border-radius: 12px !important;
            }
            .palette-sticky-card {
                position: static !important;
                padding: 1.25rem 1rem !important;
                border-radius: 16px !important;
                margin-top: 1.25rem !important;
            }
            .palette-grid {
                grid-template-columns: repeat(auto-fill, minmax(38px, 1fr)) !important;
                gap: 8px !important;
            }
            .nav-question-btn {
                height: 38px !important;
                font-size: 0.85rem !important;
            }
        }
    </style>

    @php
        $totalQuestions = $quiz->questions->count();
        $totalScorableItems = 0;
        $correctCount = 0;
        $wrongCount = 0;
        $unansweredCount = 0;
        $questionStatuses = [];

        foreach($quiz->questions as $index => $q) {
            $step = $index + 1;
            $userAnswer = $answersMap->get($q->id);

            if ($q->isMatching()) {
                $pairsAnswer = ($userAnswer && is_array($userAnswer->answer_data)) ? $userAnswer->answer_data : [];
                $totalPairs = $q->options->count();
                $totalScorableItems += $totalPairs;
                $matchedCount = 0;
                $answeredPairsCount = 0;

                if ($totalPairs > 0 && !empty($pairsAnswer)) {
                    foreach ($q->options as $opt) {
                        $hasChosen = isset($pairsAnswer[$opt->id]) && !empty($pairsAnswer[$opt->id]);
                        if ($hasChosen) {
                            $answeredPairsCount++;
                        }
                        if ($hasChosen && trim($pairsAnswer[$opt->id]) === trim($opt->match_text)) {
                            $matchedCount++;
                        }
                    }
                }

                $correctCount += $matchedCount;
                $wrongCount += ($answeredPairsCount - $matchedCount);
                $unansweredCount += ($totalPairs - $answeredPairsCount);

                if ($matchedCount === $totalPairs && $totalPairs > 0) {
                    $questionStatuses[$step] = 'correct';
                } elseif ($matchedCount > 0) {
                    $questionStatuses[$step] = 'correct';
                } elseif ($answeredPairsCount > 0) {
                    $questionStatuses[$step] = 'wrong';
                } else {
                    $questionStatuses[$step] = 'unanswered';
                }
            } else {
                $totalScorableItems += 1;
                $selectedOptionId = $userAnswer ? $userAnswer->selected_option_id : null;
                $correctOption = $q->options->firstWhere('is_correct', true);

                if ($selectedOptionId && $correctOption && $selectedOptionId === $correctOption->id) {
                    $correctCount++;
                    $questionStatuses[$step] = 'correct';
                } elseif ($selectedOptionId) {
                    $wrongCount++;
                    $questionStatuses[$step] = 'wrong';
                } else {
                    $unansweredCount++;
                    $questionStatuses[$step] = 'unanswered';
                }
            }
        }

        $accuracyPercent = $totalScorableItems > 0 ? round(($correctCount / $totalScorableItems) * 100) : 0;

        // Hitung Waktu Pengerjaan (Durasi Aktual Pengerjaan Jam - Menit - Detik)
        $durationFormatted = $attempt->duration_formatted;
    @endphp

    <!-- Sticky Top Bar: Info Kuis, Waktu Pengerjaan, & Skor Akhir -->
    <header class="quiz-attempt-hero">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 gap-md-4">
                
                <!-- Left: Quiz Info -->
                <div class="d-flex align-items-center gap-2.5 gap-sm-3">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0 shadow-sm hero-icon-box" style="background: rgba(255, 255, 255, 0.2); width: 44px; height: 44px;">
                        <i class="ti ti-award fs-3"></i>
                    </div>
                    <div>
                        <h1 class="fs-6 fs-md-5 fw-bold mb-1 text-white" style="font-family: 'Jost', sans-serif;">
                            Hasil &amp; Preview Kuis: {{ $quiz->title }}
                        </h1>
                        <div class="d-flex flex-wrap align-items-center gap-1.5 small" style="font-size: 0.78rem;">
                            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(255, 255, 255, 0.18);">
                                {{ $quiz->subject->name ?? 'Mata Pelajaran' }}
                            </span>
                            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(255, 255, 255, 0.18);">
                                Kelas {{ $quiz->schoolClass->name ?? 'Siswa' }}
                            </span>
                            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(255, 255, 255, 0.18);">
                                {{ $totalQuestions === $totalScorableItems ? $totalScorableItems . ' Butir Soal' : $totalQuestions . ' Nomor (' . $totalScorableItems . ' Butir)' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Waktu Pengerjaan, Skor Akhir, & Navigasi -->
                <div class="d-flex flex-wrap align-items-center gap-2 gap-sm-3 ms-auto ms-lg-0">
                    
                    <!-- Waktu Pengerjaan Badge -->
                    <div class="duration-badge-box d-flex align-items-center gap-2 text-white">
                        <i class="ti ti-clock-check fs-5 text-warning"></i>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="text-uppercase fw-semibold d-none d-sm-inline" style="font-size: 0.72rem; letter-spacing: 0.5px; opacity: 0.95;">WAKTU:</span>
                            <span class="fw-extrabold text-white" style="font-size: 0.9rem; line-height: 1;">{{ $durationFormatted }}</span>
                        </div>
                    </div>

                    <!-- Skor Akhir Pill -->
                    <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill text-white shadow-sm"
                         style="background: linear-gradient(135deg, #059669 0%, #10B981 100%); border: 1px solid rgba(255, 255, 255, 0.4);">
                        <i class="ti ti-trophy fs-5 text-warning"></i>
                        <span class="fw-extrabold" style="font-size: 0.9rem; line-height: 1;">Skor: {{ $attempt->score }} / 100</span>
                    </div>

                    <!-- Kembali ke Daftar Kuis -->
                    <a href="{{ route('student.quizzes.index') }}" 
                       class="btn btn-sm rounded-pill px-3.5 py-2 font-bold d-inline-flex align-items-center gap-1.5 text-white text-decoration-none shadow-sm" 
                       style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(6px); font-size: 0.82rem;">
                        <i class="ti ti-arrow-left"></i> <span class="d-none d-md-inline">Daftar Kuis</span>
                    </a>

                </div>

            </div>
        </div>
    </header>

    <!-- Main Content Area: 2 Kolom (Satu Halaman Satu Soal & Panel Navigasi Nomor Soal) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top: 2rem !important; padding-bottom: 2rem !important;">
        
        <!-- Flash Message Notification -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2 p-3 p-md-4" role="alert" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0 !important;">
                <i class="ti ti-circle-check fs-4"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2 p-3 p-md-4" role="alert" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca !important;">
                <i class="ti ti-alert-triangle fs-4"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2 p-3 p-md-4" role="alert" style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd !important;">
                <i class="ti ti-info-circle fs-4"></i>
                <div class="fw-semibold">{{ session('info') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4 align-items-start">
            
            <!-- 1. Left Column: Lembar Soal (Satu Halaman Satu Soal, Kompak & Nyaman) -->
            <div class="col-lg-7 col-xl-8">
                
                @forelse($quiz->questions as $index => $question)
                    @php
                        $stepNumber = $index + 1;
                        $userAnswer = $answersMap->get($question->id);
                        $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                        
                        $isMatching = $question->isMatching();
                        $isTrueFalse = $question->isTrueFalse();
                        
                        if ($isMatching) {
                            $pairsAnswer = ($userAnswer && is_array($userAnswer->answer_data)) ? $userAnswer->answer_data : [];
                            $totalPairs = $question->options->count();
                            $correctPairs = 0;
                            if ($totalPairs > 0 && !empty($pairsAnswer)) {
                                foreach ($question->options as $opt) {
                                    if (isset($pairsAnswer[$opt->id]) && trim($pairsAnswer[$opt->id]) === trim($opt->match_text)) {
                                        $correctPairs++;
                                    }
                                }
                            }
                            $isAnswered = !empty($pairsAnswer);
                            $isFullCorrect = ($totalPairs > 0 && $correctPairs === $totalPairs);
                            $isPartialCorrect = ($correctPairs > 0 && $correctPairs < $totalPairs);
                            $cardBorderColor = $isFullCorrect ? '#10B981' : ($isPartialCorrect ? '#F59E0B' : ($isAnswered ? '#EF4444' : '#94A3B8'));
                        } else {
                            $selectedOptionId = $userAnswer ? $userAnswer->selected_option_id : null;
                            $correctOption = $question->options->firstWhere('is_correct', true);
                            $isCorrect = $selectedOptionId && $correctOption && $selectedOptionId === $correctOption->id;
                            $isAnswered = !is_null($selectedOptionId);
                            $cardBorderColor = $isCorrect ? '#10B981' : ($isAnswered ? '#EF4444' : '#94A3B8');
                        }
                    @endphp

                    <!-- Single Question Card Container -->
                    <div class="question-step-card {{ $index === 0 ? 'active' : '' }}" 
                         id="question-step-{{ $stepNumber }}" 
                         data-step="{{ $stepNumber }}">
                        
                        <div class="question-card-modern" style="border-left: 5px solid {{ $cardBorderColor }} !important;">
                            
                            <!-- Question Card Header -->
                            <div class="question-card-header">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge px-2.5 py-1 rounded-pill font-bold d-inline-flex align-items-center gap-1" style="background: #2563EB; color: #ffffff; font-size: 0.8rem;">
                                        <i class="ti ti-file-text fs-6"></i> Soal No. {{ $stepNumber }}
                                    </span>
                                    <span class="text-muted small fw-semibold">dari {{ $totalQuestions }} Soal</span>
                                    
                                    @if($isMatching)
                                        <span class="badge bg-indigo-subtle text-indigo border border-indigo-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                            <i class="ti ti-arrows-exchange"></i> Menjodohkan
                                        </span>
                                    @elseif($isTrueFalse)
                                        <span class="badge bg-purple-subtle text-purple border border-purple-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                            <i class="ti ti-check-details"></i> Benar / Salah
                                        </span>
                                    @else
                                        <span class="badge bg-blue-subtle text-primary border border-blue-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                            <i class="ti ti-list-check"></i> Pilihan Ganda
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="d-flex align-items-center gap-2">
                                    @if($isMatching)
                                        @if($isFullCorrect)
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-2xs d-inline-flex align-items-center gap-1" style="background: #dcfce7; color: #15803d; font-size: 0.78rem;">
                                                <i class="ti ti-circle-check fs-6"></i> Benar Sempurna (+{{ $correctPairs }} Poin / {{ $totalPairs }} Soal)
                                            </span>
                                        @elseif($isPartialCorrect)
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-2xs d-inline-flex align-items-center gap-1" style="background: #fef3c7; color: #b45309; font-size: 0.78rem;">
                                                <i class="ti ti-alert-triangle fs-6"></i> Benar Sebagian (+{{ $correctPairs }} Poin dari {{ $totalPairs }} Soal)
                                            </span>
                                        @elseif($isAnswered)
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-2xs d-inline-flex align-items-center gap-1" style="background: #fee2e2; color: #b91c1c; font-size: 0.78rem;">
                                                <i class="ti ti-circle-x fs-6"></i> Salah (0 Poin dari {{ $totalPairs }} Soal)
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-2xs d-inline-flex align-items-center gap-1" style="background: #f1f5f9; color: #475569; font-size: 0.78rem;">
                                                <i class="ti ti-alert-circle fs-6"></i> Tidak Dijawab (0 Poin)
                                            </span>
                                        @endif
                                    @else
                                        @if($isCorrect)
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-2xs d-inline-flex align-items-center gap-1" style="background: #dcfce7; color: #15803d; font-size: 0.78rem;">
                                                <i class="ti ti-circle-check fs-6"></i> Benar (+1 Poin)
                                            </span>
                                        @elseif($isAnswered)
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-2xs d-inline-flex align-items-center gap-1" style="background: #fee2e2; color: #b91c1c; font-size: 0.78rem;">
                                                <i class="ti ti-circle-x fs-6"></i> Salah (0 Poin)
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-2xs d-inline-flex align-items-center gap-1" style="background: #f1f5f9; color: #475569; font-size: 0.78rem;">
                                                <i class="ti ti-alert-circle fs-6"></i> Tidak Dijawab (0 Poin)
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Question Card Body -->
                            <div class="p-4 p-md-4 bg-white">
                                
                                <!-- Question Text -->
                                <div class="text-dark fw-bold mb-4" style="font-size: 1.05rem; line-height: 1.6; font-family: 'Jost', sans-serif;">
                                    {!! nl2br(e($question->question_text)) !!}
                                </div>

                                @if($isMatching)
                                    <!-- Matching Format Review Table -->
                                    <div class="table-responsive rounded-3 border mb-3">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3" style="width: 32%; font-size: 0.85rem;">Premis / Soal</th>
                                                    <th style="width: 30%; font-size: 0.85rem;">Pasangan Jawaban Anda</th>
                                                    <th style="width: 24%; font-size: 0.85rem;">Kunci Pasangan Benar</th>
                                                    <th class="pe-3 text-end" style="width: 14%; font-size: 0.85rem;">Skor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($question->options as $opt)
                                                    @php
                                                        $studentMatch = $pairsAnswer[$opt->id] ?? null;
                                                        $isPairCorrect = ($studentMatch !== null && trim($studentMatch) === trim($opt->match_text));
                                                    @endphp
                                                    <tr>
                                                        <td class="ps-3 fw-semibold text-dark" style="font-size: 0.92rem;">
                                                            {{ $opt->option_text }}
                                                        </td>
                                                        <td>
                                                            @if($studentMatch)
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge {{ $isPairCorrect ? 'bg-success' : 'bg-danger' }} rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                                                        <i class="ti {{ $isPairCorrect ? 'ti-check' : 'ti-x' }}" style="font-size: 0.75rem;"></i>
                                                                    </span>
                                                                    <span class="{{ $isPairCorrect ? 'text-success fw-bold' : 'text-danger fw-medium' }}" style="font-size: 0.92rem;">
                                                                        {{ $studentMatch }}
                                                                    </span>
                                                                </div>
                                                            @else
                                                                <span class="badge bg-secondary-subtle text-muted" style="font-size: 0.78rem;">Tidak Dipilih</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle fw-semibold px-2.5 py-1" style="font-size: 0.88rem;">
                                                                <i class="ti ti-check me-1"></i> {{ $opt->match_text }}
                                                            </span>
                                                        </td>
                                                        <td class="pe-3 text-end">
                                                            @if($studentMatch && $isPairCorrect)
                                                                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">+1 Poin</span>
                                                            @elseif($studentMatch)
                                                                <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">0 Poin</span>
                                                            @else
                                                                <span class="badge bg-secondary text-white px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">0 Poin</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @elseif($isTrueFalse)
                                    <!-- True/False Format Review -->
                                    <div class="row g-3 mb-3">
                                        @foreach($question->options as $option)
                                            @php
                                                $isUserSelected = $selectedOptionId === $option->id;
                                                $isOptionCorrect = (bool)$option->is_correct;
                                                $isBenar = strtolower(trim($option->option_text)) === 'benar';

                                                $tileBorder = 'border: 1.5px solid rgba(51, 104, 160, 0.15);';
                                                $tileBg = 'background: #ffffff;';
                                                if ($isUserSelected && $isOptionCorrect) {
                                                    $tileBg = 'background: #ecfdf5;';
                                                    $tileBorder = 'border: 2px solid #10B981;';
                                                } elseif ($isUserSelected) {
                                                    $tileBg = 'background: #fef2f2;';
                                                    $tileBorder = 'border: 2px solid #ef4444;';
                                                } elseif ($isOptionCorrect) {
                                                    $tileBg = 'background: #f0fdf4;';
                                                    $tileBorder = 'border: 2px dashed #10B981;';
                                                }
                                            @endphp
                                            <div class="col-md-6">
                                                <div class="p-3 rounded-3 d-flex align-items-center justify-content-between" style="{{ $tileBg }} {{ $tileBorder }}">
                                                    <div class="d-flex align-items-center gap-2.5">
                                                        <span class="badge {{ $isBenar ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.88rem;">
                                                            <i class="ti {{ $isBenar ? 'ti-check' : 'ti-x' }} me-1"></i> {{ $option->option_text }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        @if($isUserSelected && $isOptionCorrect)
                                                            <span class="badge bg-success rounded-pill px-2.5 py-1 small fw-bold">
                                                                <i class="ti ti-check"></i> Jawaban Anda &amp; Benar
                                                            </span>
                                                        @elseif($isUserSelected)
                                                            <span class="badge bg-danger rounded-pill px-2.5 py-1 small fw-bold">
                                                                <i class="ti ti-x"></i> Jawaban Anda
                                                            </span>
                                                        @elseif($isOptionCorrect)
                                                            <span class="badge bg-success-subtle text-success border border-success rounded-pill px-2.5 py-1 small fw-bold">
                                                                <i class="ti ti-circle-check"></i> Kunci Benar
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Multiple Choice Review Options List -->
                                    <div class="d-flex flex-column mb-3">
                                        @foreach($question->options as $optIndex => $option)
                                            @php
                                                $letter = $letters[$optIndex % count($letters)];
                                                $isUserSelected = $selectedOptionId === $option->id;
                                                $isOptionCorrect = (bool)$option->is_correct;

                                                $tileClass = 'review-option-tile';
                                                $letterBg = 'background: #e2e8f0; color: #334155;';

                                                if ($isUserSelected && $isOptionCorrect) {
                                                    $tileClass .= ' tile-correct-selected';
                                                    $letterBg = 'background: #10B981; color: #ffffff;';
                                                } elseif ($isUserSelected) {
                                                    $tileClass .= ' tile-wrong-selected';
                                                    $letterBg = 'background: #EF4444; color: #ffffff;';
                                                } elseif ($isOptionCorrect) {
                                                    $tileClass .= ' tile-correct-unselected';
                                                    $letterBg = 'background: #10B981; color: #ffffff;';
                                                }
                                            @endphp

                                            <div class="{{ $tileClass }}">
                                                <div class="d-flex align-items-center">
                                                    <span class="option-badge-letter" style="{{ $letterBg }}">
                                                        {{ $letter }}
                                                    </span>
                                                    <div class="text-dark fw-medium" style="font-size: 0.95rem; line-height: 1.45;">
                                                        {{ $option->option_text }}
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-3">
                                                    @if($isUserSelected && $isOptionCorrect)
                                                        <span class="badge bg-success rounded-pill px-3 py-1 font-bold small d-inline-flex align-items-center gap-1">
                                                            <i class="ti ti-check"></i> Jawaban Anda &amp; Jawaban Benar
                                                        </span>
                                                    @elseif($isUserSelected)
                                                        <span class="badge bg-danger rounded-pill px-3 py-1 font-bold small d-inline-flex align-items-center gap-1">
                                                            <i class="ti ti-x"></i> Jawaban Anda
                                                        </span>
                                                    @elseif($isOptionCorrect)
                                                        <span class="badge bg-success-subtle text-success-emphasis border border-success rounded-pill px-3 py-1 font-bold small d-inline-flex align-items-center gap-1">
                                                            <i class="ti ti-circle-check"></i> Jawaban Benar
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Card Footer: Stepper Navigation Buttons (Sama Seperti Saat Kuis) -->
                                <div class="d-flex align-items-center justify-content-between pt-3 mt-4 border-top" style="border-color: rgba(51, 104, 160, 0.1) !important;">
                                    
                                    <!-- Tombol Sebelumnya -->
                                    <button type="button" 
                                            class="btn btn-outline-secondary rounded-pill px-3.5 py-2 font-bold d-inline-flex align-items-center gap-1.5 shadow-2xs"
                                            style="font-size: 0.85rem;"
                                            onclick="goToQuestion({{ $stepNumber - 1 }})"
                                            {{ $stepNumber === 1 ? 'disabled' : '' }}>
                                        <i class="ti ti-chevron-left"></i> Sebelumnya
                                    </button>

                                    <!-- Indikator Posisi Soal -->
                                    <span class="text-muted small fw-bold font-monospace d-none d-sm-inline">
                                        Soal {{ $stepNumber }} dari {{ $totalQuestions }}
                                    </span>

                                    <!-- Tombol Selanjutnya / Selesai Review -->
                                    @if($stepNumber < $totalQuestions)
                                        <button type="button" 
                                                class="btn text-white rounded-pill px-3.5 py-2 font-bold d-inline-flex align-items-center gap-1.5 shadow-2xs hover-lift"
                                                style="background: #2563EB; font-size: 0.85rem;"
                                                onclick="goToQuestion({{ $stepNumber + 1 }})">
                                            Selanjutnya <i class="ti ti-chevron-right"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('student.quizzes.index') }}" 
                                           class="btn btn-success rounded-pill px-4 py-2 font-bold d-inline-flex align-items-center gap-1.5 shadow-2xs hover-lift text-decoration-none"
                                           style="font-size: 0.85rem;">
                                            <i class="ti ti-check"></i> Selesai Review
                                        </a>
                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>
                @empty
                    <div class="card border-0 rounded-4 shadow-sm p-5 text-center">
                        <i class="ti ti-file-off text-muted fs-1 mb-2"></i>
                        <p class="text-muted mb-0">Tidak ada soal dalam kuis ini.</p>
                    </div>
                @endforelse

            </div>

            <!-- 2. Right Column: Sidebar Navigasi Soal & Ringkasan Hasil Pengerjaan -->
            <div class="col-lg-5 col-xl-4">
                <aside class="palette-sticky-card">
                    
                    <!-- Sidebar Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                            <i class="ti ti-layout-grid text-primary fs-5"></i> Navigasi Soal
                        </h6>
                        <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 font-bold small">
                            {{ $totalQuestions === $totalScorableItems ? $totalScorableItems . ' Soal' : $totalQuestions . ' Nomor (' . $totalScorableItems . ' Butir)' }}
                        </span>
                    </div>

                    <!-- Mini Summary Cards Grid: Skor & Waktu Pengerjaan -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border text-center" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                                <div class="text-success fw-extrabold fs-4 mb-0" style="line-height: 1.1;">{{ $attempt->score }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Skor Akhir</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border text-center" style="background: #eff6ff; border-color: #bfdbfe !important;">
                                <div class="text-primary fw-extrabold fs-6 mb-0" style="line-height: 1.35;">{{ $durationFormatted }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Waktu Pengerjaan</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar Tingkat Akurasi (Berjarak Lega ke Kotak Nomor Soal) -->
                    <div class="mb-4 pb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.78rem;">
                            <span class="text-muted fw-semibold">Akurasi Butir Soal</span>
                            <span class="fw-bold text-success">{{ $correctCount }} / {{ $totalScorableItems }} Butir ({{ $accuracyPercent }}%)</span>
                        </div>
                        <div class="progress" style="height: 7px; border-radius: 10px; background-color: #E2E8F0;">
                            <div class="progress-bar rounded-pill" 
                                 role="progressbar" 
                                 style="width: {{ $accuracyPercent }}%; background: linear-gradient(90deg, #10B981, #059669);" 
                                 aria-valuenow="{{ $accuracyPercent }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Kotak Nomor Soal (Palette Grid) -->
                    <div class="palette-grid mb-4" id="questionsPaletteGrid">
                        @foreach($quiz->questions as $index => $q)
                            @php
                                $step = $index + 1;
                                $status = $questionStatuses[$step] ?? 'unanswered';
                                
                                $btnClass = 'nav-question-btn ';
                                if ($status === 'correct') {
                                    $btnClass .= 'nav-btn-correct';
                                } elseif ($status === 'wrong') {
                                    $btnClass .= 'nav-btn-wrong';
                                } else {
                                    $btnClass .= 'nav-btn-unanswered';
                                }

                                if ($step === 1) {
                                    $btnClass .= ' nav-btn-current';
                                }
                            @endphp

                            <button type="button" 
                                    class="{{ $btnClass }}" 
                                    id="nav-num-{{ $step }}" 
                                    onclick="goToQuestion({{ $step }})"
                                    title="Soal No. {{ $step }} ({{ $status === 'correct' ? 'Benar' : ($status === 'wrong' ? 'Salah' : 'Tidak Dijawab') }})">
                                {{ $step }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Legenda Keterangan Warna (Legend) -->
                    <div class="d-flex flex-column gap-2 mb-4">
                        
                        <!-- Sedang Dilihat (BIRU) -->
                        <div class="legend-box-item">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="legend-indicator-dot dot-current"></div>
                                <div class="small fw-semibold text-dark">Sedang Dilihat</div>
                            </div>
                            <span id="legendCurrentNum" class="badge rounded-pill px-2.5 py-1 text-white font-bold" style="background-color: #2563EB; font-size: 0.75rem;">
                                No. 1
                            </span>
                        </div>

                        <!-- Jawaban Benar (HIJAU) -->
                        <div class="legend-box-item">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="legend-indicator-dot dot-correct"></div>
                                <div class="small fw-semibold text-dark">Jawaban Benar</div>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 text-white font-bold" style="background-color: #10B981; font-size: 0.75rem;">
                                {{ $correctCount }} Butir
                            </span>
                        </div>

                        <!-- Jawaban Salah (MERAH) -->
                        <div class="legend-box-item">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="legend-indicator-dot dot-wrong"></div>
                                <div class="small fw-semibold text-dark">Jawaban Salah</div>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 text-white font-bold" style="background-color: #EF4444; font-size: 0.75rem;">
                                {{ $wrongCount }} Butir
                            </span>
                        </div>

                        @if($unansweredCount > 0)
                            <!-- Tidak Dijawab (ABU-ABU) -->
                            <div class="legend-box-item">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="legend-indicator-dot dot-unanswered"></div>
                                    <div class="small fw-semibold text-dark">Tidak Dijawab</div>
                                </div>
                                <span class="badge rounded-pill px-2.5 py-1 text-secondary font-bold" style="background-color: #E2E8F0; color: #475569 !important; font-size: 0.75rem;">
                                    {{ $unansweredCount }} Butir
                                </span>
                            </div>
                        @endif

                    </div>

                    <!-- Divider Antara Legenda & Tombol Aksi -->
                    <hr class="my-3" style="border-color: rgba(51, 104, 160, 0.12);">

                    <!-- Detail Submission & Kelas -->
                    <div class="p-2.5 rounded-3 bg-light text-start small text-muted mb-3 border" style="font-size: 0.76rem;">
                        <div class="mb-1"><i class="ti ti-clock me-1 text-primary"></i> <strong>Selesai:</strong> {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') . ' WIB' : '-' }}</div>
                        <div><i class="ti ti-school me-1 text-info"></i> <strong>Kelas:</strong> {{ $quiz->schoolClass->name ?? 'Saya' }}</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column gap-2 pt-1">
                        <a href="{{ route('student.quizzes.index') }}" 
                           class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift text-decoration-none" 
                           style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); font-size: 0.88rem;">
                            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Kuis
                        </a>
                        <a href="{{ route('student.materials.index') }}" 
                           class="btn btn-outline-secondary w-100 rounded-pill py-2.5 font-bold d-flex align-items-center justify-content-center gap-2"
                           style="border-color: rgba(51, 104, 160, 0.25); font-size: 0.88rem;">
                            <i class="ti ti-book"></i> Buka Materi Pelajaran
                        </a>
                    </div>

                </aside>
            </div>

        </div>

    </div>

    <!-- Stepper Navigation JavaScript for Result Review -->
    <script>
        const totalQuestions = {{ (int)$totalQuestions }};
        let currentQuestion = 1;

        function goToQuestion(targetNum) {
            if (targetNum < 1 || targetNum > totalQuestions) return;

            // Sembunyikan semua kartu soal, tampilkan kartu target
            document.querySelectorAll('.question-step-card').forEach(card => {
                card.classList.remove('active');
            });
            const targetCard = document.getElementById('question-step-' + targetNum);
            if (targetCard) {
                targetCard.classList.add('active');
            }

            // Hapus glow ring aktif dari tombol sebelumnya
            const prevNavBtn = document.getElementById('nav-num-' + currentQuestion);
            if (prevNavBtn) {
                prevNavBtn.classList.remove('nav-btn-current');
            }

            // Tambahkan glow ring aktif ke tombol yang dituju
            currentQuestion = targetNum;
            const currentNavBtn = document.getElementById('nav-num-' + currentQuestion);
            if (currentNavBtn) {
                currentNavBtn.classList.add('nav-btn-current');
            }

            // Perbarui badge nomor aktif di Legenda
            const legendCurrentBadge = document.getElementById('legendCurrentNum');
            if (legendCurrentBadge) {
                legendCurrentBadge.innerText = 'No. ' + currentQuestion;
            }
        }
    </script>
</x-app-layout>
