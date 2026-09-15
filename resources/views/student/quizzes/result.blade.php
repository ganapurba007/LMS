<x-app-layout :suppressGlobalAlerts="true">
    <!-- Include Bootstrap, Tabler Icons & Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        /* Hero Section */
        .quiz-result-hero {
            background: linear-gradient(135deg, #1e3d60 0%, #2b5788 55%, #20456E 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #66A3BF;
            color: #ffffff;
            padding-top: 1.75rem !important;
            padding-bottom: 1.75rem !important;
        }
        .quiz-result-hero::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(102, 163, 191, 0.2) 0%, transparent 70%);
            pointer-events: none;
        }
        .quiz-result-hero::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 25%;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            pointer-events: none;
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
            border-radius: 14px;
            border: 1.5px solid rgba(51, 104, 160, 0.16);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-bottom: 1.25rem;
            transition: border-color 0.2s ease;
        }
        @media (min-width: 768px) {
            .question-card-modern {
                border-radius: 16px;
                margin-bottom: 1.5rem;
            }
        }
        .question-card-header {
            padding: 0.75rem 1rem;
            background: #F8FAFC;
            border-bottom: 1px solid rgba(51, 104, 160, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        @media (min-width: 768px) {
            .question-card-header {
                padding: 0.85rem 1.35rem;
            }
        }

        /* Review Option Tiles */
        .review-option-tile {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.65rem;
            padding: 0.75rem 0.95rem;
            border-radius: 12px;
            border: 1.5px solid rgba(51, 104, 160, 0.15);
            background: #ffffff;
            margin-bottom: 0.65rem;
            transition: all 0.15s ease-in-out;
            flex-direction: column;
        }
        @media (min-width: 576px) {
            .review-option-tile {
                flex-direction: row;
                align-items: center;
                padding: 0.85rem 1.15rem;
                margin-bottom: 0.75rem;
            }
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
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.84rem;
            margin-right: 0.65rem;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }
        @media (min-width: 768px) {
            .option-badge-letter {
                width: 32px;
                height: 32px;
                font-size: 0.88rem;
                margin-right: 0.75rem;
            }
        }

        /* Navigasi Nomor Soal (Palette) Sticky Sidebar */
        .palette-sticky-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1.5px solid rgba(51, 104, 160, 0.16);
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.05);
            padding: 1.15rem 1.25rem !important;
            overflow: hidden;
        }
        @media (min-width: 992px) {
            .palette-sticky-card {
                position: sticky;
                top: 5rem;
                border-radius: 18px;
                padding: 1.35rem 1.45rem !important;
            }
        }
        .palette-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(34px, 1fr));
            gap: 7px;
        }
        @media (min-width: 768px) {
            .palette-grid {
                grid-template-columns: repeat(auto-fill, minmax(36px, 1fr));
                gap: 8px;
            }
        }
        .nav-question-btn {
            height: 35px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.82rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
            border: none;
        }
        @media (min-width: 768px) {
            .nav-question-btn {
                height: 36px;
                font-size: 0.85rem;
            }
        }
        
        /* Status Warna Nomor Soal Hasil Kuis */
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

        .nav-btn-unanswered {
            background-color: #F1F5F9 !important;
            color: #475569 !important;
            border: 1.5px solid #CBD5E1 !important;
        }
        .nav-btn-unanswered:hover {
            background-color: #E2E8F0 !important;
            transform: translateY(-2px);
        }

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

        /* Floating Mobile Palette Trigger */
        .btn-floating-palette {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 100%);
            color: #ffffff;
            border: 1.5px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 8px 24px rgba(32, 69, 110, 0.35);
            backdrop-filter: blur(8px);
            font-weight: 700;
            font-size: 0.82rem;
            transition: all 0.2s ease;
            user-select: none;
            touch-action: manipulation;
        }
        .btn-floating-palette:hover, .btn-floating-palette:active {
            transform: scale(1.04);
            color: #ffffff;
        }

        /* Typography & Hover Utilities */
        .hero-title {
            font-size: clamp(1.25rem, 3.2vw, 1.85rem);
            font-weight: 800;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
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
        $durationFormatted = $attempt->duration_formatted;
    @endphp

    <!-- 1. Dedicated Quiz Result Page Hero -->
    <section class="quiz-result-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 position-relative z-1">
            
            <!-- Top Bar: Pelajaran & Tombol Kembali -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5 mb-3">
                <!-- Pelajaran -->
                <span class="badge px-3 py-1.5 rounded-pill shadow-xs font-bold d-inline-flex align-items-center gap-1.5" 
                      style="background-color: #F2EFE7; color: #20456E !important; font-size: 0.8rem;">
                    <i class="ti ti-tag text-primary"></i> {{ $quiz->subject->name ?? 'Mata Pelajaran' }}
                </span>

                <!-- Tombol Kembali -->
                <a href="{{ route('student.quizzes.index') }}" 
                   class="btn btn-sm rounded-pill px-3 py-1.5 font-bold d-inline-flex align-items-center gap-1.5 text-white text-decoration-none shadow-sm hover-lift" 
                   style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(8px); font-size: 0.82rem;">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Judul & Tanggal Selesai Kuis -->
            <div>
                <h1 class="hero-title mb-2 text-white" style="font-family: 'Jost', sans-serif;">
                    Hasil &amp; Review: {{ $quiz->title }}
                </h1>
                
                <div class="d-flex align-items-center gap-1.5 text-white-50 small" style="font-size: 0.82rem;">
                    <i class="ti ti-calendar text-warning"></i>
                    <span>Selesai: {{ $attempt->submitted_at ? $attempt->submitted_at->translatedFormat('d F Y, H:i') . ' WIB' : ($attempt->created_at ? $attempt->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-') }}</span>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. Main Content Area: 2 Kolom (Satu Halaman Satu Soal & Panel Navigasi Nomor Soal) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top: 2rem !important; padding-bottom: 3.5rem !important;">
        
        <!-- Flash Message Notification -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-3 mb-md-4 d-flex align-items-center gap-2 p-3" role="alert" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0 !important;">
                <i class="ti ti-circle-check fs-4 shrink-0"></i>
                <div class="fw-semibold small">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-3 mb-md-4 d-flex align-items-center gap-2 p-3" role="alert" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca !important;">
                <i class="ti ti-alert-triangle fs-4 shrink-0"></i>
                <div class="fw-semibold small">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 rounded-4 shadow-sm mb-3 mb-md-4 d-flex align-items-center gap-2 p-3" role="alert" style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd !important;">
                <i class="ti ti-info-circle fs-4 shrink-0"></i>
                <div class="fw-semibold small">{{ session('info') }}</div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-3 g-md-4 align-items-start">
            
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
                                <div class="d-flex align-items-center gap-1.5 gap-sm-2 flex-wrap">
                                    <span class="badge px-2.5 py-1 rounded-pill font-bold d-inline-flex align-items-center gap-1" style="background: #2563EB; color: #ffffff; font-size: 0.78rem;">
                                        <i class="ti ti-file-text fs-6"></i> Soal No. {{ $stepNumber }}
                                    </span>
                                    <span class="text-muted small fw-semibold" style="font-size: 0.74rem;">dari {{ $totalQuestions }} Soal</span>
                                    
                                    @if($isMatching)
                                        <span class="badge bg-indigo-subtle text-indigo border border-indigo-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            <i class="ti ti-arrows-exchange"></i> Menjodohkan
                                        </span>
                                    @elseif($isTrueFalse)
                                        <span class="badge bg-purple-subtle text-purple border border-purple-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            <i class="ti ti-check-details"></i> Benar / Salah
                                        </span>
                                    @else
                                        <span class="badge bg-blue-subtle text-primary border border-blue-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            <i class="ti ti-list-check"></i> Pilihan Ganda
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    @if($isMatching)
                                        @if($isFullCorrect)
                                            <span class="badge bg-success text-white rounded-pill px-2.5 py-1 font-bold shadow-2xs" style="font-size: 0.76rem;">
                                                <i class="ti ti-check me-0.5"></i> Benar Semua ({{ $correctPairs }}/{{ $totalPairs }})
                                            </span>
                                        @elseif($isPartialCorrect)
                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 font-bold shadow-2xs" style="font-size: 0.76rem;">
                                                <i class="ti ti-info-circle me-0.5"></i> Sebagian Benar ({{ $correctPairs }}/{{ $totalPairs }})
                                            </span>
                                        @elseif($isAnswered)
                                            <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 font-bold shadow-2xs" style="font-size: 0.76rem;">
                                                <i class="ti ti-x me-0.5"></i> Salah Semua
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1 font-bold" style="font-size: 0.76rem;">
                                                <i class="ti ti-minus me-0.5"></i> Tidak Dijawab
                                            </span>
                                        @endif
                                    @else
                                        @if($isCorrect)
                                            <span class="badge bg-success text-white rounded-pill px-2.5 py-1 font-bold shadow-2xs" style="font-size: 0.76rem;">
                                                <i class="ti ti-check me-0.5"></i> Jawaban Benar
                                            </span>
                                        @elseif($isAnswered)
                                            <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 font-bold shadow-2xs" style="font-size: 0.76rem;">
                                                <i class="ti ti-x me-0.5"></i> Jawaban Salah
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1 font-bold" style="font-size: 0.76rem;">
                                                <i class="ti ti-minus me-0.5"></i> Tidak Dijawab
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Question Text & Media -->
                            <div class="p-3 p-sm-4 p-md-4">
                                
                                <div class="fw-semibold text-dark mb-3" style="font-size: 0.96rem; line-height: 1.65;">
                                    {!! nl2br(e($question->question_text)) !!}
                                </div>

                                @if($question->image_path)
                                    <div class="mb-3 text-center text-sm-start">
                                        <img src="{{ asset('storage/' . $question->image_path) }}" 
                                             alt="Gambar Soal" 
                                             class="img-fluid rounded-3 border shadow-xs" 
                                             style="max-height: 280px; object-fit: contain;">
                                    </div>
                                @endif

                                <!-- Review Options Area -->
                                <div class="mt-3 pt-2 border-top">
                                    <div class="small fw-bold text-muted text-uppercase mb-2.5" style="font-size: 0.7rem; letter-spacing: 0.6px;">
                                        {{ $isMatching ? 'Koreksi Pasangan Soal & Jawaban:' : 'Pilihan Jawaban & Kunci:' }}
                                    </div>

                                    @if($isMatching)
                                        <!-- Tipe Soal Menjodohkan (Matching) Review Table -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                                <thead style="background-color: #F8FAFC;">
                                                    <tr>
                                                        <th style="width: 35%;">Pernyataan / Soal</th>
                                                        <th style="width: 30%;">Jawaban Anda</th>
                                                        <th style="width: 30%;">Kunci Jawaban</th>
                                                        <th class="text-center" style="width: 5%;">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($question->options as $optIndex => $option)
                                                        @php
                                                            $chosenMatch = $pairsAnswer[$option->id] ?? null;
                                                            $isPairCorrect = $chosenMatch && trim($chosenMatch) === trim($option->match_text);
                                                        @endphp
                                                        <tr class="{{ $isPairCorrect ? 'table-success-subtle' : ($chosenMatch ? 'table-danger-subtle' : '') }}">
                                                            <td class="fw-semibold text-dark">{{ $option->option_text }}</td>
                                                            <td>
                                                                @if($chosenMatch)
                                                                    <span class="fw-bold {{ $isPairCorrect ? 'text-success' : 'text-danger' }}">
                                                                        {{ $chosenMatch }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted fst-italic">- Tidak Dipilih -</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-success fw-bold">
                                                                <i class="ti ti-check me-0.5"></i> {{ $option->match_text }}
                                                            </td>
                                                            <td class="text-center">
                                                                @if($isPairCorrect)
                                                                    <i class="ti ti-circle-check-filled text-success fs-5"></i>
                                                                @elseif($chosenMatch)
                                                                    <i class="ti ti-circle-x-filled text-danger fs-5"></i>
                                                                @else
                                                                    <i class="ti ti-minus text-secondary fs-5"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <!-- Tipe Pilihan Ganda & Benar/Salah Review Tiles -->
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($question->options as $optIndex => $option)
                                                @php
                                                    $isThisSelected = ($selectedOptionId === $option->id);
                                                    $isThisCorrectKey = (bool)$option->is_correct;
                                                    
                                                    $tileClass = 'review-option-tile ';
                                                    $badgeBg = '#F1F5F9';
                                                    $badgeColor = '#334155';
                                                    $badgeBorder = '#CBD5E1';

                                                    if ($isThisSelected && $isThisCorrectKey) {
                                                        $tileClass .= 'tile-correct-selected';
                                                        $badgeBg = '#10B981';
                                                        $badgeColor = '#ffffff';
                                                        $badgeBorder = '#059669';
                                                    } elseif ($isThisSelected && !$isThisCorrectKey) {
                                                        $tileClass .= 'tile-wrong-selected';
                                                        $badgeBg = '#EF4444';
                                                        $badgeColor = '#ffffff';
                                                        $badgeBorder = '#DC2626';
                                                    } elseif (!$isThisSelected && $isThisCorrectKey) {
                                                        $tileClass .= 'tile-correct-unselected';
                                                        $badgeBg = '#10B981';
                                                        $badgeColor = '#ffffff';
                                                        $badgeBorder = '#059669';
                                                    }
                                                @endphp

                                                <div class="{{ $tileClass }}">
                                                    
                                                    <!-- Option Content -->
                                                    <div class="d-flex align-items-center flex-grow-1 min-w-0">
                                                        <span class="option-badge-letter" style="background-color: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1.5px solid {{ $badgeBorder }};">
                                                            @if($isTrueFalse)
                                                                <i class="ti {{ strtolower($option->option_text) == 'benar' ? 'ti-check' : 'ti-x' }}"></i>
                                                            @else
                                                                {{ $letters[$optIndex] ?? ($optIndex + 1) }}
                                                            @endif
                                                        </span>
                                                        <span class="fw-semibold text-dark text-break" style="font-size: 0.88rem;">
                                                            {{ $option->option_text }}
                                                        </span>
                                                    </div>

                                                    <!-- Option Status Indicator Tag -->
                                                    <div class="shrink-0 ms-auto ms-sm-0 mt-1 mt-sm-0">
                                                        @if($isThisSelected && $isThisCorrectKey)
                                                            <span class="badge rounded-pill px-2.5 py-1 font-bold text-white shadow-2xs" style="background: #10B981; font-size: 0.74rem;">
                                                                <i class="ti ti-circle-check me-0.5"></i> Jawaban Anda (Benar)
                                                            </span>
                                                        @elseif($isThisSelected && !$isThisCorrectKey)
                                                            <span class="badge rounded-pill px-2.5 py-1 font-bold text-white shadow-2xs" style="background: #EF4444; font-size: 0.74rem;">
                                                                <i class="ti ti-circle-x me-0.5"></i> Jawaban Anda (Salah)
                                                            </span>
                                                        @elseif(!$isThisSelected && $isThisCorrectKey)
                                                            <span class="badge rounded-pill px-2.5 py-1 font-bold text-success border border-success-subtle shadow-2xs" style="background: #dcfce7; font-size: 0.74rem;">
                                                                <i class="ti ti-key me-0.5"></i> Kunci Jawaban Benar
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>

                            </div>

                            <!-- Card Footer: Stepper Navigation Buttons (Kembali & Selanjutnya) -->
                            <div class="p-3 bg-light-subtle border-top d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                
                                <div>
                                    @if($stepNumber > 1)
                                        <button type="button" 
                                                class="btn btn-outline-secondary rounded-pill px-3 py-1.5 font-bold d-inline-flex align-items-center gap-1"
                                                style="font-size: 0.82rem;"
                                                onclick="goToQuestion({{ $stepNumber - 1 }})">
                                            <i class="ti ti-chevron-left"></i> Soal Sebelumnya
                                        </button>
                                    @else
                                        <span class="text-muted small ps-1" style="font-size: 0.76rem;">Awal Soal</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    
                                    @if($stepNumber < $totalQuestions)
                                        <button type="button" 
                                                class="btn btn-primary rounded-pill px-3.5 py-1.5 font-bold d-inline-flex align-items-center gap-1 shadow-2xs hover-lift"
                                                style="font-size: 0.82rem; background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); border: none;"
                                                onclick="goToQuestion({{ $stepNumber + 1 }})">
                                            Soal Selanjutnya <i class="ti ti-chevron-right"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('student.quizzes.index') }}" 
                                           class="btn btn-success rounded-pill px-3.5 py-1.5 font-bold d-inline-flex align-items-center gap-1 shadow-2xs hover-lift text-decoration-none"
                                           style="font-size: 0.82rem;">
                                            <i class="ti ti-check"></i> Selesai Review
                                        </a>
                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>
                @empty
                    <div class="card border-0 rounded-4 shadow-sm p-4 text-center">
                        <i class="ti ti-file-off text-muted fs-1 mb-2"></i>
                        <p class="text-muted mb-0">Tidak ada soal dalam kuis ini.</p>
                    </div>
                @endforelse

            </div>

            <!-- 2. Right Column: Sidebar Navigasi Soal & Ringkasan Hasil Pengerjaan -->
            <div class="col-lg-5 col-xl-4">
                <aside class="palette-sticky-card" id="paletteCardSection">
                    
                    <!-- Sidebar Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                            <i class="ti ti-layout-grid text-primary fs-5"></i> Navigasi Soal
                        </h6>
                        <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 font-bold small" style="font-size: 0.72rem;">
                            {{ $totalQuestions }} Butir
                        </span>
                    </div>

                    <!-- Mini Summary Cards Grid: Skor & Waktu Pengerjaan -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border text-center" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                                <div class="text-success fw-extrabold fs-4 mb-0" style="line-height: 1.1;">{{ $attempt->score }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Skor Akhir</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border text-center" style="background: #eff6ff; border-color: #bfdbfe !important;">
                                <div class="text-primary fw-extrabold fs-6 mb-0" style="line-height: 1.35;">{{ $durationFormatted }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Waktu Pengerjaan</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar Tingkat Akurasi -->
                    <div class="mb-3 pb-1">
                        <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.76rem;">
                            <span class="text-muted fw-semibold">Akurasi Soal</span>
                            <span class="fw-bold text-success">{{ $correctCount }} / {{ $totalScorableItems }} ({{ $accuracyPercent }}%)</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 10px; background-color: #E2E8F0;">
                            <div class="progress-bar rounded-pill" 
                                 role="progressbar" 
                                 style="width: {{ $accuracyPercent }}%; background: linear-gradient(90deg, #10B981, #059669);" 
                                 aria-valuenow="{{ $accuracyPercent }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Kotak Nomor Soal (Palette Grid) -->
                    <div class="palette-grid mb-3" id="questionsPaletteGrid">
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
                    <div class="d-flex flex-column gap-1.5 mb-3">
                        
                        <!-- Sedang Dilihat (BIRU) -->
                        <div class="legend-box-item" style="background: rgba(37, 99, 235, 0.06); border-color: rgba(37, 99, 235, 0.25);">
                            <div class="d-flex align-items-center gap-2">
                                <div class="legend-indicator-dot dot-current"></div>
                                <div class="small fw-semibold text-primary" style="font-size: 0.76rem;">Sedang Dilihat</div>
                            </div>
                            <span id="legendCurrentNum" class="badge rounded-pill px-2 py-0.5 text-white font-bold" style="background-color: #2563EB; font-size: 0.72rem;">
                                No. 1
                            </span>
                        </div>

                        <!-- Jawaban Benar (HIJAU) -->
                        <div class="legend-box-item">
                            <div class="d-flex align-items-center gap-2">
                                <div class="legend-indicator-dot dot-correct"></div>
                                <div class="small fw-semibold text-dark" style="font-size: 0.76rem;">Jawaban Benar</div>
                            </div>
                            <span class="badge rounded-pill px-2 py-0.5 text-white font-bold" style="background-color: #10B981; font-size: 0.72rem;">
                                {{ $correctCount }} Butir
                            </span>
                        </div>

                        <!-- Jawaban Salah (MERAH) -->
                        <div class="legend-box-item">
                            <div class="d-flex align-items-center gap-2">
                                <div class="legend-indicator-dot dot-wrong"></div>
                                <div class="small fw-semibold text-dark" style="font-size: 0.76rem;">Jawaban Salah</div>
                            </div>
                            <span class="badge rounded-pill px-2 py-0.5 text-white font-bold" style="background-color: #EF4444; font-size: 0.72rem;">
                                {{ $wrongCount }} Butir
                            </span>
                        </div>

                        @if($unansweredCount > 0)
                            <!-- Tidak Dijawab (ABU-ABU) -->
                            <div class="legend-box-item">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="legend-indicator-dot dot-unanswered"></div>
                                    <div class="small fw-semibold text-dark" style="font-size: 0.76rem;">Tidak Dijawab</div>
                                </div>
                                <span class="badge rounded-pill px-2 py-0.5 text-secondary font-bold" style="background-color: #E2E8F0; color: #475569 !important; font-size: 0.72rem;">
                                    {{ $unansweredCount }} Butir
                                </span>
                            </div>
                        @endif

                    </div>

                    <!-- Divider Antara Legenda & Tombol Aksi -->
                    <hr class="my-2.5" style="border-color: rgba(51, 104, 160, 0.12);">

                    <!-- Detail Submission & Kelas -->
                    <div class="p-2.5 rounded-3 bg-light text-start small text-muted mb-3 border" style="font-size: 0.74rem;">
                        <div class="mb-1"><i class="ti ti-clock me-1 text-primary"></i> <strong>Selesai:</strong> {{ $attempt->submitted_at ? $attempt->submitted_at->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</div>
                        <div><i class="ti ti-school me-1 text-info"></i> <strong>Kelas:</strong> {{ $quiz->schoolClass->name ?? 'Saya' }}</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column gap-2 pt-1">
                        <a href="{{ route('student.quizzes.index') }}" 
                           class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift text-decoration-none" 
                           style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); font-size: 0.85rem;">
                            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Kuis
                        </a>
                        <a href="{{ route('student.materials.index') }}" 
                           class="btn btn-outline-secondary w-100 rounded-pill py-2.5 font-bold d-flex align-items-center justify-content-center gap-2 hover-lift"
                           style="border-color: rgba(51, 104, 160, 0.25); font-size: 0.85rem;">
                            <i class="ti ti-book"></i> Buka Materi Pelajaran
                        </a>
                    </div>

                </aside>
            </div>

        </div>

    </div>

    <!-- Floating Mobile Palette Trigger -->
    <div class="d-lg-none position-fixed" style="bottom: 1.25rem; right: 1rem; z-index: 1015;">
        <button type="button" 
                onclick="scrollToPalette()" 
                class="btn btn-floating-palette rounded-pill py-2 px-3 d-flex align-items-center gap-1.5 shadow-lg"
                title="Buka Navigasi Soal">
            <i class="ti ti-layout-grid fs-5"></i>
            <span>Soal No.</span>
            <span class="badge bg-white text-primary rounded-pill px-2 py-0.5 font-bold" id="mobileCurrentNumBadge">1</span>
        </button>
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

            // Perbarui badge nomor aktif di floating mobile button
            const mobileBadge = document.getElementById('mobileCurrentNumBadge');
            if (mobileBadge) {
                mobileBadge.innerText = currentQuestion;
            }

            // Scroll halus ke atas kartu soal saat pindah nomor
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function scrollToPalette() {
            const paletteEl = document.getElementById('paletteCardSection');
            if (paletteEl) {
                paletteEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    </script>
</x-app-layout>
