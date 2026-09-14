<x-app-layout>
    <!-- Include Bootstrap, Tabler Icons & Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        /* Modern Sticky Header Bar (Lega, Mewah & Tidak Mepet) */
        .quiz-attempt-hero {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 60%, #2b5788 100%);
            border-bottom: 3px solid #66A3BF;
            color: #ffffff;
            position: sticky;
            top: 0 !important;
            z-index: 1020;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            padding-top: 1.25rem !important;
            padding-bottom: 1.25rem !important;
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

        /* Compact & Balanced Question Card (Tidak Perlu Scroll) */
        .question-card-modern {
            background: #ffffff;
            border-radius: 16px;
            border: 1.5px solid rgba(51, 104, 160, 0.16);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-bottom: 1.25rem;
            transition: border-color 0.2s ease;
        }
        .question-card-header {
            padding: 0.8rem 1.4rem;
            background: #F2EFE7;
            border-bottom: 1px solid rgba(51, 104, 160, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Compact & Comfortable Option Tiles (Sedikit Diperkecil Agar Pas Viewport) */
        .quiz-option-tile {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.15rem;
            border-radius: 10px;
            border: 1.5px solid rgba(51, 104, 160, 0.15);
            background: #ffffff;
            margin-bottom: 0.6rem;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            user-select: none;
        }
        .quiz-option-tile:hover {
            background: #f8fafc;
            border-color: #3368A0;
            transform: translateX(3px);
        }
        .quiz-option-tile.selected {
            background: rgba(37, 99, 235, 0.08);
            border-color: #2563EB;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12);
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
            background: #e2e8f0;
            color: #334155;
            margin-right: 0.95rem;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }
        .quiz-option-tile.selected .option-badge-letter {
            background: #2563EB;
            color: #ffffff;
        }

        /* Timer Badge */
        .timer-badge-box {
            background: rgba(220, 53, 69, 0.92);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 50rem;
            padding: 8px 22px;
            box-shadow: 0 4px 16px rgba(220, 53, 69, 0.35);
        }
        .timer-warning {
            animation: pulse-timer 1.2s infinite;
        }
        @keyframes pulse-timer {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }

        /* Navigasi Nomor Soal (Palette) Lega, Luas & Rapi */
        .palette-sticky-card {
            position: sticky;
            top: 6.5rem !important;
            background: #ffffff;
            border-radius: 18px;
            border: 1.5px solid rgba(51, 104, 160, 0.16);
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.05);
            padding: 1.5rem 1.65rem !important;
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
        
        /* 1. Belum dijawab: Abu-abu netral */
        .nav-btn-unanswered {
            background-color: #F1F5F9;
            color: #475569;
            border: 1.5px solid #CBD5E1;
        }
        .nav-btn-unanswered:hover {
            background-color: #E2E8F0;
            border-color: #94A3B8;
            color: #1E293B;
            transform: translateY(-2px);
        }

        /* 2. Sudah dijawab: Hijau tegas */
        .nav-btn-answered {
            background-color: #10B981 !important;
            color: #ffffff !important;
            border: 1.5px solid #059669 !important;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
        }
        .nav-btn-answered:hover {
            background-color: #059669 !important;
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        /* 3. Sedang Dikerjakan / Aktif: BIRU MENYALA */
        .nav-btn-current {
            background-color: #2563EB !important;
            color: #ffffff !important;
            border: 2px solid #1D4ED8 !important;
            box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.35), 0 4px 10px rgba(37, 99, 235, 0.25) !important;
            transform: scale(1.06);
            z-index: 2;
        }
        .nav-btn-current:hover {
            background-color: #1D4ED8 !important;
            color: #ffffff !important;
        }

        /* Keterangan Warna (Legend) Lega & Rapi */
        .legend-box-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 13px;
            border-radius: 11px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            transition: background-color 0.15s ease;
        }
        .legend-indicator-dot {
            width: 15px;
            height: 15px;
            border-radius: 4px;
            flex-shrink: 0;
        }
        .legend-indicator-dot.dot-current {
            background-color: #2563EB;
            border: 1.5px solid #1D4ED8;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
        }
        .legend-indicator-dot.dot-answered {
            background-color: #10B981;
            border: 1.5px solid #059669;
        }
        .legend-indicator-dot.dot-unanswered {
            background-color: #F1F5F9;
            border: 1.5px solid #CBD5E1;
        }

        /* Live Sync Badge */
        .sync-badge {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 0.8rem;
            padding: 7px 16px;
            border-radius: 50rem;
        }

        /* ==========================================================================
           SECURE EXAM LOCKDOWN & ANTI-CHEAT ENVIRONMENT
           ========================================================================== */
        /* Sembunyikan seluruh navigasi portal luar, header website, dan footer agar layar 100% fokus kuis */
        nav,
        .edusite-header,
        .arsha-header,
        header:not(.quiz-attempt-hero),
        .arsha-footer,
        footer,
        .mobile-nav-bar,
        .mobile-bottom-nav {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            min-height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        body {
            user-select: none !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            background-color: #F8FAFC !important;
            overflow-x: hidden !important;
        }

        .min-h-screen {
            padding-bottom: 0 !important;
            padding-top: 0 !important;
        }

        .quiz-attempt-hero {
            top: 0 !important;
            position: sticky !important;
            z-index: 1020 !important;
        }

        /* Overlay & Modal Proteksi Ujian */
        .exam-overlay-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(10px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            animation: fadeInExamModal 0.2s ease-in-out;
        }
        @keyframes fadeInExamModal {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .exam-modal-card {
            background: #ffffff;
            border-radius: 20px;
            max-width: 540px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
            animation: popExamModal 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes popExamModal {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .violation-pill-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 1.25rem 0;
        }
        .violation-dot {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #F1F5F9;
            border: 2px solid #CBD5E1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 800;
            color: #64748B;
            transition: all 0.2s ease;
        }
        .violation-dot.active {
            background: #DC2626;
            border-color: #991B1B;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.25);
            transform: scale(1.08);
        }

        /* Toast Peringatan Navigasi Back */
        .exam-toast-container {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 99998;
            pointer-events: none;
        }
        .exam-toast {
            background: #1E293B;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 50rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            border: 1.5px solid #EF4444;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            animation: slideUpToast 0.3s ease;
        }
        @keyframes slideUpToast {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .exam-modal-body {
            padding: 1.5rem 1.75rem;
            background: #ffffff;
        }
    </style>

    <!-- Sticky Top Bar: Info Kuis, Auto-Save Status, & Countdown Timer -->
    <header class="quiz-attempt-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
                
                <!-- Left: Quiz Info -->
                <div class="d-flex align-items-center gap-3.5">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0 shadow-sm" style="background: rgba(255, 255, 255, 0.2); width: 48px; height: 48px;">
                        <i class="ti ti-checklist fs-3"></i>
                    </div>
                    <div>
                        <h1 class="fs-5 fw-bold mb-1 text-white" style="font-family: 'Jost', sans-serif;">
                            {{ $quiz->title }}
                        </h1>
                        <div class="text-white-50 small d-flex align-items-center gap-2" style="font-size: 0.82rem;">
                            <span>{{ $quiz->subject->name ?? 'Mata Pelajaran' }}</span>
                            <span>•</span>
                            <span>Total {{ $quiz->questions->count() }} Butir Soal</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Auto-Save Status & Countdown Timer -->
                <div class="d-flex flex-wrap align-items-center gap-3.5">
                    
                    <!-- Auto-Save Status Indicator -->
                    <div id="saveStatusBadge" class="sync-badge d-none d-md-inline-flex align-items-center gap-2">
                        <i class="ti ti-cloud-check text-warning fs-5" id="saveStatusIcon"></i>
                        <span id="saveStatusText" class="fw-medium">Jawaban Tersimpan Otomatis</span>
                    </div>

                    <!-- Countdown Timer -->
                    <div class="timer-badge-box d-flex align-items-center gap-2.5 text-white" id="timerContainer">
                        <i class="ti ti-clock-hour-4 fs-4 text-warning"></i>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-uppercase fw-semibold d-none d-sm-inline" style="font-size: 0.72rem; letter-spacing: 0.5px; opacity: 0.9;">Sisa Waktu:</span>
                            <span id="quizTimer" class="fs-4 fw-extrabold font-monospace text-white" style="line-height: 1;">--:--</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </header>

    @php
        $totalQuestions = $quiz->questions->count();
        $initialAnsweredCount = count($savedAnswers ?? []);
        $initialUnansweredCount = max(0, $totalQuestions - $initialAnsweredCount);
        $initialProgress = $totalQuestions > 0 ? round(($initialAnsweredCount / $totalQuestions) * 100) : 0;
    @endphp

    <!-- Main Content Area: 2 Kolom Rapi, Lega & Nyaman -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top: 2.75rem !important; padding-bottom: 3.5rem !important;">
        
        <form id="quizForm" action="{{ route('student.quizzes.submit', $quiz) }}" method="POST">
            @csrf

            <div class="row g-4 align-items-start">
                
                <!-- 1. Left Column: Lembar Soal (Satu Halaman Satu Soal, Ukuran Proporsional) -->
                <div class="col-lg-7 col-xl-8">
                    
                    @forelse($quiz->questions as $index => $question)
                        @php
                            $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                            $currentSavedOptionId = $savedAnswers[$question->id] ?? null;
                            $stepNumber = $index + 1;
                        @endphp

                        <!-- Single Question Card Container -->
                        <div class="question-step-card {{ $index === 0 ? 'active' : '' }}" 
                             id="question-step-{{ $stepNumber }}" 
                             data-step="{{ $stepNumber }}">
                            
                            <div class="question-card-modern">
                                
                                <!-- Question Header (Kompak & Elegan) -->
                                <div class="question-card-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge px-2.5 py-1 rounded-pill font-bold d-inline-flex align-items-center gap-1" style="background: #2563EB; color: #ffffff; font-size: 0.8rem;">
                                            <i class="ti ti-edit fs-6"></i> Soal No. {{ $stepNumber }}
                                        </span>
                                        <span class="text-muted small fw-semibold">dari {{ $totalQuestions }} Soal</span>
                                    </div>
                                    
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-white text-primary border rounded-pill px-2.5 py-1 font-bold shadow-2xs" style="font-size: 0.76rem;">
                                            <i class="ti ti-award me-1"></i> {{ $quiz->points_per_question ?? 100 }} Poin
                                        </span>
                                    </div>
                                </div>

                                <!-- Question Body (Proporsional, Nyaman Dibaca, Tidak Perlu Scroll) -->
                                <div class="p-3.5 p-md-4 bg-white">
                                    
                                    <!-- Teks Pertanyaan Soal -->
                                    <div class="fw-bold text-dark mb-3" style="font-size: 1.05rem; line-height: 1.6; font-family: 'Jost', sans-serif;">
                                        {!! nl2br(e($question->question_text)) !!}
                                    </div>

                                    <!-- Options Container (Adaptive based on Question Type) -->
                                    <div class="mb-3">
                                        @if($question->isMatching())
                                            <!-- MATCHING QUESTION INTERFACE -->
                                            @php
                                                $shuffledMatches = $question->options->pluck('match_text')->filter()->unique()->shuffle();
                                                $userMatchingData = $savedMatchingAnswers[$question->id] ?? [];
                                            @endphp
                                            <div class="alert alert-info py-2 px-3 mb-3 small d-flex align-items-center gap-2" style="background: rgba(14, 165, 233, 0.08); border-color: rgba(56, 189, 248, 0.3); color: #0284c7;">
                                                <i class="ti ti-arrows-left-right fs-5"></i>
                                                <span>Pasangkan setiap pernyataan di kolom kiri dengan jawaban yang sesuai di kolom kanan:</span>
                                            </div>
                                            <div class="vstack gap-2.5">
                                                @foreach($question->options as $pairIndex => $opt)
                                                    <div class="p-3 rounded-3 border bg-light d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                                        <div class="fw-semibold text-dark small" style="max-width: 50%;">
                                                            <span class="badge bg-primary rounded-circle me-1.5" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">{{ $pairIndex + 1 }}</span>
                                                            {{ $opt->option_text }}
                                                        </div>
                                                        <div class="flex-grow-1" style="max-width: 48%;">
                                                            <select name="matching_answers[{{ $question->id }}][{{ $opt->id }}]" 
                                                                    class="form-select form-select-sm matching-select" 
                                                                    data-question-id="{{ $question->id }}"
                                                                    data-option-id="{{ $opt->id }}"
                                                                    data-question-index="{{ $stepNumber }}"
                                                                    style="border-radius: 8px; border-color: #cbd5e1; font-size: 0.88rem;">
                                                                <option value="">-- Pilih Pasangan --</option>
                                                                @foreach($shuffledMatches as $match)
                                                                    <option value="{{ $match }}" {{ ($userMatchingData[$opt->id] ?? '') === $match ? 'selected' : '' }}>
                                                                        {{ $match }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($question->isTrueFalse())
                                            <!-- TRUE / FALSE QUESTION INTERFACE -->
                                            <div class="row g-3">
                                                @foreach($question->options as $option)
                                                    @php
                                                        $isTrue = ($option->option_text === 'Benar');
                                                        $isSelected = ($currentSavedOptionId == $option->id);
                                                    @endphp
                                                    <div class="col-6">
                                                        <label class="quiz-option-tile d-flex flex-column align-items-center justify-content-center p-3 text-center h-100 {{ $isSelected ? 'selected' : '' }}" for="opt-{{ $option->id }}" style="cursor: pointer; min-height: 90px;">
                                                            <input type="radio" 
                                                                   name="answers[{{ $question->id }}]" 
                                                                   id="opt-{{ $option->id }}" 
                                                                   value="{{ $option->id }}" 
                                                                   data-question-id="{{ $question->id }}"
                                                                   data-question-index="{{ $stepNumber }}"
                                                                   data-option-id="{{ $option->id }}"
                                                                   {{ $isSelected ? 'checked' : '' }}
                                                                   class="form-check-input option-radio d-none">
                                                            <div class="rounded-circle p-2 mb-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: {{ $isTrue ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $isTrue ? '#059669' : '#dc2626' }};">
                                                                <i class="ti {{ $isTrue ? 'ti-check' : 'ti-x' }} fs-4"></i>
                                                            </div>
                                                            <span class="fw-bold fs-6 {{ $isTrue ? 'text-success' : 'text-danger' }}">{{ $option->option_text }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <!-- MULTIPLE CHOICE OPTIONS LIST -->
                                            <div class="d-flex flex-column">
                                                @foreach($question->options as $optIndex => $option)
                                                    @php
                                                        $letter = $letters[$optIndex % count($letters)];
                                                        $isSelected = ($currentSavedOptionId == $option->id);
                                                    @endphp

                                                    <label class="quiz-option-tile {{ $isSelected ? 'selected' : '' }}" for="opt-{{ $option->id }}">
                                                        <input type="radio" 
                                                               name="answers[{{ $question->id }}]" 
                                                               id="opt-{{ $option->id }}" 
                                                               value="{{ $option->id }}" 
                                                               data-question-id="{{ $question->id }}"
                                                               data-question-index="{{ $stepNumber }}"
                                                               data-option-id="{{ $option->id }}"
                                                               {{ $isSelected ? 'checked' : '' }}
                                                               class="form-check-input option-radio" 
                                                               style="width: 1.18rem; height: 1.18rem; margin-right: 1.15rem !important; cursor: pointer;">
                                                        <span class="option-badge-letter">{{ $letter }}</span>
                                                        <span class="text-dark fw-medium" style="font-size: 0.92rem; line-height: 1.45;">{{ $option->option_text }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Bottom Action & Navigation Bar (Langsung Terlihat Tanpa Scroll) -->
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3 border-top" style="border-color: #f1f5f9 !important;">
                                        
                                        <!-- Tombol Sebelumnya -->
                                        @if($index > 0)
                                            <button type="button" 
                                                    onclick="goToQuestion({{ $index }})" 
                                                    class="btn btn-outline-secondary rounded-pill px-3.5 py-2 font-bold d-inline-flex align-items-center gap-1.5 hover-lift"
                                                    style="font-size: 0.88rem;">
                                                <i class="ti ti-arrow-left"></i> Soal Sebelumnya
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-light rounded-pill px-3.5 py-2 text-muted fw-semibold" disabled style="opacity: 0.5; cursor: not-allowed; font-size: 0.88rem;">
                                                <i class="ti ti-arrow-left"></i> Soal Sebelumnya
                                            </button>
                                        @endif

                                        <div class="text-muted small fw-medium" style="font-size: 0.82rem;">
                                            Nomor <strong>{{ $stepNumber }}</strong> dari <strong>{{ $totalQuestions }}</strong>
                                        </div>

                                        <!-- Tombol Selanjutnya & Tombol Selesai -->
                                        <div class="d-flex align-items-center gap-2">
                                            @if($index < $totalQuestions - 1)
                                                <button type="button" 
                                                        onclick="goToQuestion({{ $stepNumber + 1 }})" 
                                                        class="btn text-white rounded-pill px-4 py-2 font-bold d-inline-flex align-items-center gap-1.5 hover-lift"
                                                        style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); font-size: 0.88rem;">
                                                    Soal Selanjutnya <i class="ti ti-arrow-right"></i>
                                                </button>
                                            @endif

                                            <!-- Tombol Selesai & Kumpulkan pada Soal Terakhir -->
                                            @if($index === $totalQuestions - 1)
                                                <button type="button" 
                                                        onclick="openSubmitConfirmationModal();" 
                                                        class="btn text-white rounded-pill px-4 py-2 font-bold d-inline-flex align-items-center gap-1.5 hover-lift shadow-sm"
                                                        style="background: linear-gradient(135deg, #059669 0%, #10B981 100%); font-size: 0.88rem;">
                                                    <i class="ti ti-circle-check"></i> Selesai & Kumpulkan
                                                </button>
                                            @endif
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    @empty
                        <div class="card border-0 rounded-4 shadow-sm text-center py-5">
                            <div class="card-body p-5">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="background: rgba(245, 158, 11, 0.12); width: 72px; height: 72px;">
                                    <i class="ti ti-alert-triangle text-warning fs-1"></i>
                                </div>
                                <h4 class="fw-bold mb-2 text-dark">Tidak Ada Butir Soal dalam Kuis Ini</h4>
                                <p class="text-muted small mb-4">Guru pengampu belum mengunggah pertanyaan untuk kuis ini. Silakan kembali lagi nanti.</p>
                                <a href="{{ route('student.quizzes.index') }}" class="btn btn-primary rounded-pill px-4 py-2 font-bold" style="background: #20456E;">
                                    <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar Kuis
                                </a>
                            </div>
                        </div>
                    @endforelse

                    <!-- Hidden input to track violation counts -->
                    <input type="hidden" name="violation_count" id="violationCountInput" value="0">

                    <!-- Hidden fallback submit button for accessibility and automated testing -->
                    <button type="submit" 
                            id="submitQuizBtn" 
                            class="d-none">
                    </button>

                </div>

                <!-- 2. Right Column: Navigasi Soal (Question Palette) & Keterangan Warna (Legend) Lega & Luas -->
                <div class="col-lg-5 col-xl-4">
                    <aside class="palette-sticky-card">
                        
                        <!-- Palette Header -->
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2.5 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #20456E, #3368A0); width: 34px; height: 34px;">
                                    <i class="ti ti-layout-grid fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 1.05rem;">
                                        Navigasi Soal
                                    </h6>
                                    <div class="text-muted small" style="font-size: 0.76rem;">Klik nomor untuk menuju soal</div>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-1 font-bold small" style="background: rgba(51, 104, 160, 0.1); color: #20456E;">
                                {{ $totalQuestions }} Butir
                            </span>
                        </div>

                        <!-- Progress Bar Pengerjaan (Berjarak Lega ke Kotak Nomor Soal) -->
                        <div class="mb-4 pb-1">
                            <div class="d-flex align-items-center justify-content-between small text-muted mb-2">
                                <span class="fw-semibold">Progres Pengerjaan</span>
                                <span class="fw-bold text-primary" id="progressPercentageText">{{ $initialProgress }}%</span>
                            </div>
                            <div class="progress" style="height: 7px; border-radius: 50rem; background-color: #E2E8F0;">
                                <div id="progressBarFill" 
                                     class="progress-bar rounded-pill" 
                                     role="progressbar" 
                                     style="width: {{ $initialProgress }}%; background: linear-gradient(90deg, #3368A0, #10B981);" 
                                     aria-valuenow="{{ $initialProgress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <!-- Grid Nomor Soal (Lega & Simetris) -->
                        <div class="palette-grid mb-3.5" id="questionPaletteGrid">
                            @foreach($quiz->questions as $index => $q)
                                @php
                                    $num = $index + 1;
                                    $isAnswered = isset($savedAnswers[$q->id]) && !empty($savedAnswers[$q->id]);
                                    $isFirst = ($index === 0);
                                @endphp
                                <button type="button" 
                                        id="nav-num-{{ $num }}" 
                                        onclick="goToQuestion({{ $num }})" 
                                        class="nav-question-btn {{ $isFirst ? 'nav-btn-current' : ($isAnswered ? 'nav-btn-answered' : 'nav-btn-unanswered') }}"
                                        title="Buka Soal {{ $num }} ({{ $isAnswered ? 'Sudah Dijawab' : 'Belum Dijawab' }})">
                                    {{ $num }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Divider -->
                        <hr class="my-3" style="border-color: rgba(51, 104, 160, 0.12);">

                        <!-- Keterangan Warna (Legend) Lega di Bawah Navigasi Soal -->
                        <div class="mb-3.5">
                            <div class="text-uppercase fw-bold text-secondary mb-2" style="font-size: 0.72rem; letter-spacing: 0.6px;">
                                <i class="ti ti-info-circle me-1"></i> Keterangan Warna
                            </div>

                            <div class="d-flex flex-column gap-2">
                                
                                <!-- Legend 1: Sedang Dikerjakan (BIRU) -->
                                <div class="legend-box-item" style="background: rgba(37, 99, 235, 0.06); border-color: rgba(37, 99, 235, 0.25);">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="legend-indicator-dot dot-current"></div>
                                        <div class="small fw-bold text-primary">Sedang Dikerjakan</div>
                                    </div>
                                    <span id="legendCurrentNum" class="badge rounded-pill px-2.5 py-1 text-white font-bold" style="background-color: #2563EB; font-size: 0.75rem;">
                                        No. 1
                                    </span>
                                </div>

                                <!-- Legend 2: Sudah Dijawab (HIJAU) -->
                                <div class="legend-box-item">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="legend-indicator-dot dot-answered"></div>
                                        <div class="small fw-semibold text-dark">Sudah Dijawab</div>
                                    </div>
                                    <span id="legendAnsweredCount" class="badge rounded-pill px-2.5 py-1 text-white font-bold" style="background-color: #10B981; font-size: 0.75rem;">
                                        {{ $initialAnsweredCount }} Soal
                                    </span>
                                </div>

                                <!-- Legend 3: Belum Dijawab (ABU-ABU) -->
                                <div class="legend-box-item">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="legend-indicator-dot dot-unanswered"></div>
                                        <div class="small fw-semibold text-dark">Belum Dijawab</div>
                                    </div>
                                    <span id="legendUnansweredCount" class="badge rounded-pill px-2.5 py-1 text-secondary font-bold" style="background-color: #E2E8F0; color: #475569 !important; font-size: 0.75rem;">
                                        {{ $initialUnansweredCount }} Soal
                                    </span>
                                </div>

                        <!-- Divider Antara Legenda & Tombol Kumpulkan -->
                        <hr class="my-4" style="border-color: rgba(51, 104, 160, 0.12);">

                        <!-- Sidebar Quick Submit Button (Berjarak Lega & Nyaman) -->
                        <div class="pt-1">
                            <button type="button" 
                                    onclick="openSubmitConfirmationModal();" 
                                    class="btn btn-outline-success w-100 rounded-pill py-2.5 font-bold shadow-2xs d-flex align-items-center justify-content-center gap-2 hover-lift"
                                    style="font-size: 0.9rem;">
                                <i class="ti ti-check"></i> Kumpulkan Kuis
                            </button>
                        </div>

                    </aside>
                </div>

            </div>

        </form>

    </div>

    <!-- =========================================================================
         EXAM LOCKDOWN OVERLAYS & MODALS
         ========================================================================= -->

    <!-- 1. Modal Gerbang Mulai Kuis (Wajib Masuk Fullscreen) -->
    <div id="examStartModal" class="exam-overlay-backdrop" style="display: none;">
        <div class="exam-modal-card">
            <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%);">
                <div class="rounded-circle bg-white text-primary mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 58px; height: 58px;">
                    <i class="ti ti-shield-lock fs-1" style="color: #20456E;"></i>
                </div>
                <h4 class="fw-bold mb-1" style="font-family: 'Jost', sans-serif;">Kuis Terproteksi RuangTerra</h4>
                <p class="small text-white-50 mb-0">Mode Layar Penuh (Fullscreen) & Pengawasan Anti-Curang</p>
            </div>
            <div class="exam-modal-body text-dark">
                <div class="alert alert-light border rounded-3 p-3 mb-3 small" style="background: #F8FAFC; border-color: #E2E8F0 !important;">
                    <div class="fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                        <i class="ti ti-info-circle text-primary fs-5"></i> Peraturan Ketat Selama Kuis Berlangsung:
                    </div>
                    <ul class="mb-0 ps-3 text-secondary d-flex flex-column gap-1.5" style="line-height: 1.5;">
                        <li>Kuis <strong>wajib dikerjakan dalam mode Layar Penuh (Fullscreen)</strong>.</li>
                        <li><strong>DILARANG membuka tab lain</strong>, berpindah jendela, atau meminimalkan browser.</li>
                        <li><strong>DILARANG keluar dari kuis</strong> sebelum Anda menyelesaikan dan mengumpulkan kuis.</li>
                        <li><strong>Batas toleransi pelanggaran HANYA 1 KALI</strong>: Berpindah tab, keluar layar penuh, atau beralih jendela akan langsung dicatat sebagai pelanggaran.</li>
                        <li><strong class="text-danger">Jika melanggar (1 kali saja), lembar kuis akan LANGSUNG OTOMATIS DIKUMPULKAN ke server</strong> dan dinilai apa adanya!</li>
                    </ul>
                </div>

                <div class="text-center pt-1">
                    <button type="button" 
                            id="btnEnterFullscreenExam" 
                            onclick="startFullscreenExam()" 
                            class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift"
                            style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); font-size: 0.95rem;">
                        <i class="ti ti-maximize fs-5"></i> Saya Mengerti, Mulai Kuis & Masuk Layar Penuh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Modal Peringatan Pelanggaran Pindah Tab / Layar Penuh Lepas / Blur Jendela -->
    <div id="tabViolationModal" class="exam-overlay-backdrop" style="display: none;">
        <div class="exam-modal-card">
            <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);">
                <div class="rounded-circle bg-white text-danger mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 58px; height: 58px;">
                    <i class="ti ti-alert-octagon fs-1" style="color: #dc2626;"></i>
                </div>
                <h4 class="fw-bold mb-1" style="font-family: 'Jost', sans-serif;">PELANGGARAN TERDETEKSI!</h4>
                <p class="small text-white-50 mb-0">Terdeteksi Membuka Tab Baru atau Meninggalkan Jendela Kuis</p>
            </div>
            <div class="exam-modal-body text-center">
                <div class="alert alert-danger rounded-3 py-2.5 px-3 small font-bold mb-3" style="background-color: #fef2f2; border-color: #fecaca; color: #991b1b;">
                    <i class="ti ti-ban me-1 fs-5"></i> Batas Toleransi Pelanggaran (1 Kali) Telah Terlampaui!
                </div>

                <p class="text-secondary small mb-3" style="line-height: 1.6;">
                    Sistem mendeteksi Anda meninggalkan jendela kuis, beralih ke tab/aplikasi lain, atau keluar dari layar penuh. Sesuai ketentuan pengawasan, lembar kuis Anda <strong>sedang otomatis dikumpulkan ke server dan dinilai apa adanya</strong>.
                </p>

                <div class="p-3 rounded-3 mb-3 text-start border" style="background: #FFF5F5; border-color: #FED7D7 !important;">
                    <div class="d-flex align-items-center gap-2 text-danger fw-bold small mb-1">
                        <i class="ti ti-clock-pause fs-5"></i> Status Penyerahan Jawaban:
                    </div>
                    <div class="small text-muted">
                        Seluruh jawaban Anda yang telah tersimpan sedang diserahkan ke server untuk dinilai. Harap tunggu sebentar...
                    </div>
                </div>

                <button type="button" 
                        id="btnAckViolation"
                        disabled
                        class="btn btn-danger w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
                        style="font-size: 0.95rem;">
                    <i class="ti ti-loader ti-spin fs-5"></i> Mengumpulkan Kuis Otomatis...
                </button>
            </div>
        </div>
    </div>

    <!-- 4. Modal Konfirmasi Pengumpulan Kuis -->
    <div id="submitConfirmModal" class="exam-overlay-backdrop" style="display: none;">
        <div class="exam-modal-card">
            <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #065f46 0%, #059669 100%);">
                <div class="rounded-circle bg-white text-success mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 58px; height: 58px;">
                    <i class="ti ti-file-check fs-1" style="color: #059669;"></i>
                </div>
                <h4 class="fw-bold mb-1" style="font-family: 'Jost', sans-serif;">Konfirmasi Kumpulkan Kuis</h4>
                <p class="small text-white-50 mb-0">Pastikan seluruh jawaban telah Anda periksa</p>
            </div>
            <div class="exam-modal-body">
                <div class="row g-2 mb-3 text-center">
                    <div class="col-6">
                        <div class="p-3 rounded-3 border" style="background: #F8FAFC;">
                            <div class="small text-muted fw-semibold">Sudah Dijawab</div>
                            <div class="fs-4 fw-extrabold text-success" id="modalAnsweredCount">0</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 border" style="background: #F8FAFC;">
                            <div class="small text-muted fw-semibold">Belum Dijawab</div>
                            <div class="fs-4 fw-extrabold text-danger" id="modalUnansweredCount">0</div>
                        </div>
                    </div>
                </div>

                <div id="unansweredWarningBox" class="alert alert-warning border rounded-3 p-3 mb-3 small d-none" style="background-color: #fffbeb; border-color: #fde68a !important; color: #92400e;">
                    <i class="ti ti-alert-triangle me-1 fs-5"></i>
                    <strong>Perhatian:</strong> Masih ada <span id="warningUnansweredNum">0</span> butir soal yang belum Anda jawab! Apakah Anda tetap ingin mengumpulkan?
                </div>

                <p class="small text-muted text-center mb-4">
                    Setelah kuis dikumpulkan, Anda tidak dapat kembali mengubah jawaban. Kuis akan dinilai secara otomatis oleh sistem.
                </p>

                <div class="d-flex align-items-center gap-2.5">
                    <button type="button" 
                            onclick="closeSubmitModal()" 
                            class="btn btn-outline-secondary w-50 rounded-pill py-2.5 font-bold"
                            style="font-size: 0.9rem;">
                        Periksa Kembali
                    </button>
                    <button type="button" 
                            onclick="confirmActualSubmit()" 
                            class="btn text-white w-50 rounded-pill py-2.5 font-bold shadow-sm hover-lift"
                            style="background: linear-gradient(135deg, #059669 0%, #10B981 100%); font-size: 0.9rem;">
                        <i class="ti ti-check me-1"></i> Ya, Kumpulkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Toast Notifikasi Penolakan Tombol Back -->
    <div id="backNavToast" class="exam-toast-container d-none">
        <div class="exam-toast">
            <i class="ti ti-ban text-danger fs-4"></i>
            <span>Tombol 'Kembali' dinonaktifkan! Kuis harus dikumpulkan untuk keluar.</span>
        </div>
    </div>

    <!-- Countdown Timer, Single-Question Stepper, Auto-Save & Navigation Sync JavaScript -->
    <script>
        const totalQuestions = {{ (int)$totalQuestions }};
        const saveAnswerUrl = "{{ route('student.quizzes.save-answer', $quiz) }}";
        const csrfToken = "{{ csrf_token() }}";

        let currentQuestion = 1;
        const answeredSet = new Set();

        // Populate initially answered question numbers
        @foreach($quiz->questions as $index => $q)
            @if($q->isMatching())
                @if(isset($savedMatchingAnswers[$q->id]) && !empty($savedMatchingAnswers[$q->id]))
                    answeredSet.add({{ $index + 1 }});
                @endif
            @else
                @if(isset($savedAnswers[$q->id]) && !empty($savedAnswers[$q->id]))
                    answeredSet.add({{ $index + 1 }});
                @endif
            @endif
        @endforeach

        // 1. Single Question Navigation (Satu Halaman Satu Soal)
        function goToQuestion(targetNum) {
            if (targetNum < 1 || targetNum > totalQuestions) return;

            // Sembunyikan semua kartu soal, tampilkan target
            document.querySelectorAll('.question-step-card').forEach(card => {
                card.classList.remove('active');
            });
            const targetCard = document.getElementById('question-step-' + targetNum);
            if (targetCard) {
                targetCard.classList.add('active');
            }

            // Perbarui styling tombol nomor di Navigasi Soal
            const prevNavBtn = document.getElementById('nav-num-' + currentQuestion);
            if (prevNavBtn) {
                prevNavBtn.classList.remove('nav-btn-current');
                if (answeredSet.has(currentQuestion)) {
                    prevNavBtn.classList.add('nav-btn-answered');
                    prevNavBtn.classList.remove('nav-btn-unanswered');
                } else {
                    prevNavBtn.classList.add('nav-btn-unanswered');
                    prevNavBtn.classList.remove('nav-btn-answered');
                }
            }

            // Tandai tombol yang dituju dengan WARNA BIRU (nav-btn-current)
            currentQuestion = targetNum;
            const currentNavBtn = document.getElementById('nav-num-' + currentQuestion);
            if (currentNavBtn) {
                currentNavBtn.classList.remove('nav-btn-answered', 'nav-btn-unanswered');
                currentNavBtn.classList.add('nav-btn-current');
            }

            // Perbarui badge nomor aktif di Legenda
            const legendCurrentBadge = document.getElementById('legendCurrentNum');
            if (legendCurrentBadge) {
                legendCurrentBadge.innerText = 'No. ' + currentQuestion;
            }
        }

        function updateLegendAndProgress() {
            const answeredCount = answeredSet.size;
            const unansweredCount = Math.max(0, totalQuestions - answeredCount);
            const percent = totalQuestions > 0 ? Math.round((answeredCount / totalQuestions) * 100) : 0;

            // Update Legend Badges
            const legendAnsweredEl = document.getElementById('legendAnsweredCount');
            const legendUnansweredEl = document.getElementById('legendUnansweredCount');
            if (legendAnsweredEl) legendAnsweredEl.innerText = answeredCount + ' Soal';
            if (legendUnansweredEl) legendUnansweredEl.innerText = unansweredCount + ' Soal';

            // Update Progress Bar
            const progressFill = document.getElementById('progressBarFill');
            const progressText = document.getElementById('progressPercentageText');
            if (progressFill) progressFill.style.width = percent + '%';
            if (progressText) progressText.innerText = percent + '%';
        }

        function setSyncStatus(status) {
            const badge = document.getElementById('saveStatusBadge');
            const icon = document.getElementById('saveStatusIcon');
            const text = document.getElementById('saveStatusText');
            if (!badge) return;

            if (status === 'saving') {
                badge.classList.remove('d-none');
                icon.className = 'ti ti-loader ti-spin text-warning fs-6';
                text.innerText = 'Menyimpan jawaban...';
            } else if (status === 'saved') {
                badge.classList.remove('d-none');
                icon.className = 'ti ti-cloud-check text-success fs-6';
                text.innerText = 'Jawaban tersimpan otomatis';
            } else if (status === 'error') {
                badge.classList.remove('d-none');
                icon.className = 'ti ti-alert-circle text-danger fs-6';
                text.innerText = 'Koneksi terganggu';
            }
        }

        // =========================================================================
        // EXAM LOCKDOWN, FULLSCREEN ENFORCEMENT & ANTI-CHEAT JAVASCRIPT
        // =========================================================================
        let isExamActive = false;
        let isSubmitting = false;
        let violationCount = 0;
        const MAX_VIOLATIONS = 1;
        const storageKey = 'quiz_end_{{ $quiz->id }}_{{ $attempt->id }}';
        const violationStorageKey = 'quiz_violation_{{ $quiz->id }}_{{ $attempt->id }}';

        // 1. Synthesized Audio Beep for Warning Alerts
        function playWarningBeep() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(480, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.35);
                gain.gain.setValueAtTime(0.35, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            } catch (e) {
                // Fallback silently if audio blocked by browser policy
            }
        }

        // 2. Fullscreen Handlers
        function isFullscreenActive() {
            return !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
        }

        function requestFullscreenSafe() {
            const el = document.documentElement;
            if (el.requestFullscreen) {
                return el.requestFullscreen();
            } else if (el.webkitRequestFullscreen) {
                return el.webkitRequestFullscreen();
            } else if (el.mozRequestFullScreen) {
                return el.mozRequestFullScreen();
            } else if (el.msRequestFullscreen) {
                return el.msRequestFullscreen();
            }
            return Promise.resolve();
        }

        function startFullscreenExam() {
            requestFullscreenSafe().catch(err => {
                console.warn('Fullscreen request rejected or not supported:', err);
            }).finally(() => {
                const startModal = document.getElementById('examStartModal');
                if (startModal) startModal.style.display = 'none';
                isExamActive = true;
            });
        }

        function restoreFullscreenExam() {
            requestFullscreenSafe().catch(err => {
                console.warn('Fullscreen request rejected:', err);
            }).finally(() => {
                const restoreOverlay = document.getElementById('fullscreenRestoreOverlay');
                if (restoreOverlay) restoreOverlay.style.display = 'none';
            });
        }

        // 3. Tab Switch, Blur Window & Fullscreen Exit Violation Handlers (Maksimal 1 Kali Toleransi)
        let lastViolationTime = 0;
        function handleTabSwitchViolation() {
            if (!isExamActive || isSubmitting) return;

            // Hindari trigger ganda dalam 1 detik
            const now = Date.now();
            if (now - lastViolationTime < 1000) return;
            lastViolationTime = now;

            violationCount++;
            localStorage.setItem(violationStorageKey, violationCount);
            playWarningBeep();

            // Tampilkan Modal Peringatan Pelanggaran
            const violationModal = document.getElementById('tabViolationModal');
            if (violationModal) {
                violationModal.style.display = 'flex';
            }

            // Kuis Otomatis Dikumpulkan
            isSubmitting = true;
            localStorage.removeItem(violationStorageKey);
            localStorage.removeItem(storageKey);

            setTimeout(() => {
                const quizForm = document.getElementById('quizForm');
                if (quizForm) {
                    quizForm.submit();
                }
            }, 1500);
        }

        // 4. Modal Konfirmasi Kumpulkan Kuis
        function openSubmitConfirmationModal() {
            const answeredCount = answeredSet.size;
            const unansweredCount = Math.max(0, totalQuestions - answeredCount);

            const elAnswered = document.getElementById('modalAnsweredCount');
            const elUnanswered = document.getElementById('modalUnansweredCount');
            const warningBox = document.getElementById('unansweredWarningBox');
            const warningNum = document.getElementById('warningUnansweredNum');

            if (elAnswered) elAnswered.innerText = answeredCount;
            if (elUnanswered) elUnanswered.innerText = unansweredCount;

            if (unansweredCount > 0) {
                if (warningBox) warningBox.classList.remove('d-none');
                if (warningNum) warningNum.innerText = unansweredCount;
            } else {
                if (warningBox) warningBox.classList.add('d-none');
            }

            const modal = document.getElementById('submitConfirmModal');
            if (modal) modal.style.display = 'flex';
        }

        function closeSubmitModal() {
            const modal = document.getElementById('submitConfirmModal');
            if (modal) modal.style.display = 'none';
        }

        function confirmActualSubmit() {
            isSubmitting = true;
            localStorage.removeItem(violationStorageKey);
            localStorage.removeItem(storageKey);
            document.getElementById('quizForm').submit();
        }

        // 5. Navigasi Lock (Back Button & Exit Prevention)
        history.pushState(null, null, location.href);
        window.addEventListener('popstate', function () {
            if (!isSubmitting) {
                history.pushState(null, null, location.href);
                showBackToast();
            }
        });

        let toastTimer = null;
        function showBackToast() {
            const toast = document.getElementById('backNavToast');
            if (!toast) return;
            toast.classList.remove('d-none');
            playWarningBeep();
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                toast.classList.add('d-none');
            }, 3500);
        }

        // Lock Browser Refresh & Tab Close
        window.addEventListener('beforeunload', function (e) {
            if (!isSubmitting) {
                e.preventDefault();
                e.returnValue = 'Kuis sedang berlangsung! Anda tidak dapat meninggalkan halaman sebelum kuis selesai dikumpulkan.';
                return e.returnValue;
            }
        });

        // Lock ContextMenu (Right Click)
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            return false;
        });

        // Lock Keyboard Shortcuts (DevTools, View Source, Copy-Paste, New Tab)
        document.addEventListener('keydown', function (e) {
            // F12 key
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                return false;
            }
            // Ctrl/Meta + Shortcuts
            if (e.ctrlKey || e.metaKey) {
                const key = e.key.toLowerCase();
                if (['c', 'v', 'x', 'u', 's', 'p', 'w', 't', 'n'].includes(key)) {
                    e.preventDefault();
                    return false;
                }
                // Ctrl + Shift + (I, J, C)
                if (e.shiftKey && ['i', 'j', 'c'].includes(key)) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi pelanggaran tersimpan
            const savedViolations = parseInt(localStorage.getItem(violationStorageKey), 10);
            if (savedViolations && !isNaN(savedViolations)) {
                violationCount = savedViolations;
                updateViolationUI();
            }

            // Tampilkan modal fullscreen jika belum fullscreen dan kuis memiliki soal
            if (totalQuestions > 0) {
                if (isFullscreenActive()) {
                    isExamActive = true;
                } else {
                    const startModal = document.getElementById('examStartModal');
                    if (startModal) startModal.style.display = 'flex';
                }
            }

            // Monitor perubahan status Fullscreen (Keluar Fullscreen = Pelanggaran Langsung Kuis Dikumpulkan)
            document.addEventListener('fullscreenchange', function () {
                if (!isFullscreenActive() && isExamActive && !isSubmitting) {
                    handleTabSwitchViolation();
                }
            });

            // Monitor perpindahan tab / minimize (Page Visibility API)
            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    handleTabSwitchViolation();
                }
            });

            // Monitor hilangnya fokus jendela (Alt+Tab / klik ke aplikasi lain)
            window.addEventListener('blur', function () {
                setTimeout(() => {
                    if (!document.hasFocus() && isExamActive && !isSubmitting) {
                        handleTabSwitchViolation();
                    }
                }, 250);
            });

            // 6. Interactive Radio Selection & AJAX Auto-Save
            const radioInputs = document.querySelectorAll('.option-radio');

            radioInputs.forEach(radio => {
                radio.addEventListener('change', function () {
                    const questionCard = this.closest('.question-step-card');
                    if (questionCard) {
                        const tiles = questionCard.querySelectorAll('.quiz-option-tile');
                        tiles.forEach(t => t.classList.remove('selected'));
                    }
                    const parentTile = this.closest('.quiz-option-tile');
                    if (parentTile) {
                        parentTile.classList.add('selected');
                    }

                    const qIndex = parseInt(this.getAttribute('data-question-index'));
                    const qId = this.getAttribute('data-question-id');
                    const optId = this.getAttribute('data-option-id');

                    answeredSet.add(qIndex);
                    updateLegendAndProgress();

                    // Send AJAX Auto-Save to Server
                    setSyncStatus('saving');
                    fetch(saveAnswerUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: qId,
                            option_id: optId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'expired') {
                            isSubmitting = true;
                            alert('Batas waktu kuis telah berakhir. Lembar kuis akan otomatis dikumpulkan.');
                            document.getElementById('quizForm').submit();
                        } else {
                            setSyncStatus('saved');
                        }
                    })
                    .catch(err => {
                        console.warn('Auto-save error:', err);
                        setSyncStatus('error');
                    });
                });
            });

            // 6b. Matching Select Change Listener & AJAX Auto-Save
            const matchingSelects = document.querySelectorAll('.matching-select');
            matchingSelects.forEach(select => {
                select.addEventListener('change', function () {
                    const qId = this.getAttribute('data-question-id');
                    const qIndex = parseInt(this.getAttribute('data-question-index'));
                    const card = this.closest('.question-step-card');

                    const pairsData = {};
                    let hasSelection = false;
                    if (card) {
                        card.querySelectorAll('.matching-select').forEach(sel => {
                            const optId = sel.getAttribute('data-option-id');
                            if (sel.value) {
                                pairsData[optId] = sel.value;
                                hasSelection = true;
                            }
                        });
                    }

                    if (hasSelection) {
                        answeredSet.add(qIndex);
                    }
                    updateLegendAndProgress();

                    setSyncStatus('saving');
                    fetch(saveAnswerUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: qId,
                            answer_data: pairsData
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'expired') {
                            isSubmitting = true;
                            alert('Batas waktu kuis telah berakhir. Lembar kuis akan otomatis dikumpulkan.');
                            document.getElementById('quizForm').submit();
                        } else {
                            setSyncStatus('saved');
                        }
                    })
                    .catch(err => {
                        console.warn('Auto-save error:', err);
                        setSyncStatus('error');
                    });
                });
            });

            // 7. Real-Time Countdown Timer (Persistent server countdown & browser state)
            const serverRemainingSeconds = {{ (int)$remainingSeconds }};

            // Hitung target waktu selesai berbasis timestamp server dan clock client
            let targetEndTime = Date.now() + (serverRemainingSeconds * 1000);

            // Sinkronisasi dengan localStorage jika kuis sedang berjalan (agar transisi refresh mulus tanpa lag)
            const storedEndTime = parseInt(localStorage.getItem(storageKey), 10);
            if (storedEndTime && Math.abs(storedEndTime - targetEndTime) < 5000) {
                targetEndTime = storedEndTime;
            } else {
                localStorage.setItem(storageKey, targetEndTime);
            }

            const timerElement = document.getElementById('quizTimer');
            const timerContainer = document.getElementById('timerContainer');
            const quizForm = document.getElementById('quizForm');
            let autoSubmitted = false;

            function updateTimer() {
                const now = Date.now();
                const remaining = Math.max(0, Math.floor((targetEndTime - now) / 1000));

                if (remaining <= 0) {
                    timerElement.innerText = "00:00";
                    localStorage.removeItem(storageKey);
                    localStorage.removeItem(violationStorageKey);
                    if (!autoSubmitted) {
                        autoSubmitted = true;
                        isSubmitting = true;
                        alert('Waktu pengerjaan kuis telah habis! Jawaban Anda akan otomatis dikumpulkan ke sistem.');
                        quizForm.submit();
                    }
                    return;
                }

                if (remaining <= 300 && timerContainer) {
                    timerContainer.classList.add('timer-warning');
                }

                const hours = Math.floor(remaining / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                const seconds = remaining % 60;

                if (hours > 0) {
                    timerElement.innerText = 
                        String(hours).padStart(2, '0') + ':' +
                        String(minutes).padStart(2, '0') + ':' + 
                        String(seconds).padStart(2, '0');
                } else {
                    timerElement.innerText = 
                        String(minutes).padStart(2, '0') + ':' + 
                        String(seconds).padStart(2, '0');
                }
            }

            // Bersihkan storage ketika form dikumpulkan
            if (quizForm) {
                quizForm.addEventListener('submit', function () {
                    isSubmitting = true;
                    localStorage.removeItem(storageKey);
                    localStorage.removeItem(violationStorageKey);
                });
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        });
    </script>
</x-app-layout>
