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
            margin-bottom: 1.25rem;
            transition: border-color 0.2s ease;
        }
        .question-card-header {
            padding: 0.8rem 1.25rem;
            background: #F2EFE7;
            border-bottom: 1px solid rgba(51, 104, 160, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        /* Question Text Content Typography & Media Constraints */
        .question-text-wrapper {
            font-size: 1.05rem;
            line-height: 1.65;
            color: #1e293b;
            overflow-wrap: break-word;
            word-break: break-word;
        }
        .question-text-wrapper img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 10px;
            margin: 0.6rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            object-fit: contain;
        }
        .question-text-wrapper table {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            display: block;
            margin: 0.5rem 0;
            border-collapse: collapse;
        }
        .question-text-wrapper p {
            margin-bottom: 0.65rem;
        }
        .question-text-wrapper p:last-child {
            margin-bottom: 0;
        }

        /* Compact & Comfortable Option Tiles */
        .quiz-option-tile {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.85rem 1.15rem;
            border-radius: 12px;
            border: 1.5px solid rgba(51, 104, 160, 0.16);
            background: #ffffff;
            margin-bottom: 0.65rem;
            cursor: pointer;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            touch-action: manipulation;
        }
        .quiz-option-tile:hover {
            background: #f8fafc;
            border-color: #3368A0;
            transform: translateX(2px);
        }
        .quiz-option-tile.selected {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.06) 0%, rgba(59, 130, 246, 0.12) 100%) !important;
            border-color: #2563EB !important;
            box-shadow: 0 2px 12px rgba(37, 99, 235, 0.12) !important;
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
            flex-shrink: 0;
            margin-top: 1px;
            transition: all 0.18s ease;
        }
        .quiz-option-tile.selected .option-badge-letter {
            background: #2563EB !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
        }
        .option-text-wrapper {
            flex: 1;
            font-size: 0.95rem;
            line-height: 1.5;
            color: #1e293b;
            word-break: break-word;
            padding-top: 4px;
        }

        /* True/False Option Tiles */
        .quiz-option-tf {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem 0.75rem;
            border-radius: 14px;
            border: 1.5px solid rgba(51, 104, 160, 0.16);
            background: #ffffff;
            cursor: pointer;
            transition: all 0.18s ease;
            height: 100%;
            text-align: center;
            min-height: 85px;
            user-select: none;
            touch-action: manipulation;
        }
        .quiz-option-tf:hover {
            border-color: #3368A0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .quiz-option-tf.selected.tf-true {
            background: rgba(16, 185, 129, 0.08) !important;
            border-color: #10B981 !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15) !important;
        }
        .quiz-option-tf.selected.tf-false {
            background: rgba(239, 68, 68, 0.08) !important;
            border-color: #EF4444 !important;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.15) !important;
        }

        /* Timer Badge */
        .timer-badge-box {
            background: rgba(220, 53, 69, 0.92);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 50rem;
            padding: 7px 18px;
            box-shadow: 0 4px 16px rgba(220, 53, 69, 0.35);
        }
        .timer-warning {
            animation: pulse-timer 1.2s infinite;
        }
        @keyframes pulse-timer {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }

        /* Ultra-Modern Interactive Matching Question UI Styles */
        .matching-guide-banner {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 60%, #2b5788 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 4px 18px rgba(32, 69, 110, 0.16) !important;
            color: #ffffff !important;
        }

        .matching-premise-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 14px !important;
            border: 1.5px solid rgba(51, 104, 160, 0.14) !important;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }
        .matching-premise-card:hover {
            border-color: #3368A0 !important;
            box-shadow: 0 6px 18px rgba(51, 104, 160, 0.08) !important;
        }

        .matching-target-slot {
            border: 2px dashed #94A3B8;
            background: #F8FAFC;
            border-radius: 12px !important;
            transition: all 0.2s ease;
            user-select: none;
            touch-action: manipulation;
        }
        .matching-target-slot.is-empty:hover {
            border-color: #38BDF8 !important;
            background: #F0F9FF !important;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }
        .matching-target-slot.active-slot {
            border: 2px solid #0284C7 !important;
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%) !important;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.25) !important;
            animation: slotPulse 1.8s infinite ease-in-out;
        }
        @keyframes slotPulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.25); }
            50% { box-shadow: 0 0 0 6px rgba(56, 189, 248, 0.4); }
        }

        .matching-target-slot.has-match {
            border: 1.5px solid #10B981 !important;
            background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%) !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.12) !important;
        }

        .btn-match-chip {
            background: #ffffff;
            border: 1.5px solid #CBD5E1;
            color: #1E293B;
            border-radius: 50rem !important;
            padding: 0.55rem 1.15rem !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            touch-action: manipulation;
        }
        .btn-match-chip.is-available:hover {
            border-color: #3368A0 !important;
            background: #F0F9FF !important;
            color: #20456E !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(51, 104, 160, 0.16) !important;
        }
        .btn-match-chip.is-used {
            background: #F1F5F9 !important;
            border: 1.5px dashed #94A3B8 !important;
            color: #64748B !important;
            opacity: 0.85;
        }

        /* Stepper Navigation Buttons */
        .quiz-nav-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            font-weight: 700;
            font-size: 0.86rem;
            padding: 0.65rem 1.15rem;
            border-radius: 50rem;
            white-space: nowrap;
            transition: all 0.18s ease;
        }
        .quiz-nav-btn-next-wrap {
            flex: 1;
            display: flex;
        }
        .quiz-step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            padding: 0.45rem 0.85rem;
            border-radius: 50rem;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* Mobile Floating Palette Trigger */
        .btn-floating-palette {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 100%);
            color: #ffffff;
            border: 1.5px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 8px 24px rgba(32, 69, 110, 0.35);
            backdrop-filter: blur(8px);
            font-weight: 700;
            font-size: 0.84rem;
            transition: all 0.2s ease;
            user-select: none;
            touch-action: manipulation;
        }
        .btn-floating-palette:hover, .btn-floating-palette:active {
            transform: scale(1.04);
            color: #ffffff;
        }

        /* Mobile & Responsive Screen Adjustments (< 768px) */
        @media (max-width: 767.98px) {
            .quiz-attempt-hero {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }

            .quiz-attempt-hero h1 {
                font-size: 0.95rem !important;
                line-height: 1.35 !important;
                max-width: none !important;
            }

            .quiz-attempt-hero .text-white-50 {
                font-size: 0.74rem !important;
            }

            .quiz-attempt-hero .hero-icon-box {
                width: 38px !important;
                height: 38px !important;
            }
            .quiz-attempt-hero .hero-icon-box i {
                font-size: 1.1rem !important;
            }

            .timer-badge-box {
                padding: 6px 12px !important;
                border-radius: 50rem !important;
                gap: 5px !important;
            }

            .timer-badge-box #quizTimer {
                font-size: 1rem !important;
            }

            .question-card-header {
                padding: 0.85rem 1rem !important;
            }

            .question-card-modern {
                border-radius: 16px !important;
                margin-bottom: 1.35rem !important;
            }

            .question-card-modern .card-body-wrapper {
                padding: 1.15rem 1rem !important;
            }

            .question-text-wrapper {
                font-size: 1rem !important;
                line-height: 1.6 !important;
                margin-bottom: 1.25rem !important;
            }

            /* Responsive Matching Question Layout */
            .matching-guide-banner {
                padding: 0.85rem 1rem !important;
                gap: 0.75rem !important;
                border-radius: 14px !important;
                margin-bottom: 1.25rem !important;
            }
            .matching-guide-banner .rounded-circle {
                width: 34px !important;
                height: 34px !important;
            }
            .matching-guide-banner .rounded-circle i {
                font-size: 1rem !important;
            }
            .matching-guide-banner .fw-extrabold {
                font-size: 0.88rem !important;
            }
            .matching-guide-banner .text-white-50 {
                font-size: 0.78rem !important;
            }

            .matching-premise-card {
                padding: 0.9rem 1rem !important;
                border-radius: 14px !important;
                margin-bottom: 0.85rem !important;
            }

            .matching-target-slot {
                min-height: 44px !important;
                padding: 0.5rem 0.75rem !important;
                font-size: 0.86rem !important;
                word-break: break-word !important;
            }

            .btn-match-chip {
                padding: 0.55rem 1rem !important;
                font-size: 0.84rem !important;
                max-width: 100% !important;
                word-break: break-word !important;
                white-space: normal !important;
            }

            .matching-choices-pool {
                padding: 1rem !important;
                border-radius: 14px !important;
                margin-top: 1.25rem !important;
            }

            .quiz-option-tile {
                padding: 0.85rem 1rem !important;
                border-radius: 12px !important;
                margin-bottom: 0.75rem !important;
                gap: 0.8rem !important;
            }

            .option-badge-letter {
                width: 32px !important;
                height: 32px !important;
                font-size: 0.88rem !important;
                border-radius: 8px !important;
                margin-top: 1px !important;
            }

            .option-text-wrapper {
                font-size: 0.92rem !important;
                line-height: 1.5 !important;
                padding-top: 3px !important;
            }

            /* Stepper Navigation Responsive */
            .quiz-nav-btn {
                flex: 1 !important;
                padding: 0.65rem 0.85rem !important;
                font-size: 0.84rem !important;
                justify-content: center !important;
                gap: 0.5rem !important;
            }
            .quiz-nav-btn-next-wrap {
                flex: 1 !important;
                display: flex !important;
            }
            .quiz-step-badge {
                font-size: 0.78rem !important;
                padding: 5px 10px !important;
            }

            /* Responsive Palette */
            .palette-sticky-card {
                position: static !important;
                padding: 1.25rem 1.25rem !important;
                border-radius: 16px !important;
                margin-top: 1.5rem !important;
            }
            .palette-grid {
                grid-template-columns: repeat(auto-fill, minmax(38px, 1fr)) !important;
                gap: 8px !important;
            }
            .nav-question-btn {
                height: 38px !important;
                font-size: 0.85rem !important;
                border-radius: 8px !important;
            }

            /* Exam Modal Cards on Mobile */
            .exam-modal-card {
                max-width: 95vw !important;
                margin: 0.85rem auto !important;
                border-radius: 18px !important;
            }
            .exam-modal-body {
                padding: 1.15rem 1.25rem !important;
            }
        }

        /* Navigasi Nomor Soal (Palette) Lega, Luas & Rapi */
        .palette-sticky-card {
            position: sticky;
            top: 5.25rem !important;
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

        html, body {
            overflow-x: hidden !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            touch-action: pan-y !important;
            height: auto !important;
            min-height: 100% !important;
        }

        body {
            user-select: none !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            background-color: #F8FAFC !important;
        }

        /* Mobile Touch Scroll & Tap Fix */
        button, input, select, textarea, .matching-target-slot, .btn-match-chip, .quiz-option-tile {
            touch-action: manipulation !important;
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

        /* Overlay & Modal Proteksi Ujian (Scrollable & Responsive di Mobile) */
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
            padding: 1rem !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            touch-action: pan-y !important;
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
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            touch-action: pan-y !important;
            border: 1px solid rgba(226, 232, 240, 0.8);
            animation: popExamModal 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            margin: auto;
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
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="d-flex align-items-center justify-content-between gap-2">
                
                <!-- Left: Quiz Info (Compact & Truncated on Mobile) -->
                <div class="d-flex align-items-center gap-2.5 overflow-hidden">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0 shadow-sm hero-icon-box" style="background: rgba(255, 255, 255, 0.2); width: 42px; height: 42px;">
                        <i class="ti ti-checklist fs-4"></i>
                    </div>
                    <div class="overflow-hidden">
                        <h1 class="fs-6 fs-sm-5 fw-bold mb-0 text-white text-truncate" style="font-family: 'Jost', sans-serif;">
                            {{ $quiz->title }}
                        </h1>
                        <div class="text-white-50 small d-flex flex-wrap align-items-center gap-1.5" style="font-size: 0.78rem;">
                            <span class="text-truncate">{{ $quiz->subject->name ?? 'Mata Pelajaran' }}</span>
                            <span>•</span>
                            <span>{{ $quiz->questions->count() === $quiz->total_questions_count ? $quiz->total_questions_count . ' Soal' : $quiz->questions->count() . ' Nomor (' . $quiz->total_questions_count . ' Butir Soal)' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Auto-Save Status & Countdown Timer -->
                <div class="d-flex align-items-center gap-2 gap-sm-3 shrink-0">
                    
                    <!-- Auto-Save Status Indicator -->
                    <div id="saveStatusBadge" class="sync-badge d-none d-lg-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-cloud-check text-warning fs-5" id="saveStatusIcon"></i>
                        <span id="saveStatusText" class="fw-medium">Tersimpan Otomatis</span>
                    </div>

                    <!-- Countdown Timer -->
                    <div class="timer-badge-box d-flex align-items-center gap-2 text-white" id="timerContainer">
                        <i class="ti ti-clock-hour-4 fs-5 text-warning"></i>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="text-uppercase fw-semibold d-none d-sm-inline" style="font-size: 0.7rem; letter-spacing: 0.5px; opacity: 0.9;">Sisa Waktu:</span>
                            <span id="quizTimer" class="fs-5 fs-sm-4 fw-extrabold font-monospace text-white" style="line-height: 1;">--:--:--</span>
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
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 py-md-4">
        
        <form id="quizForm" action="{{ route('student.quizzes.submit', $quiz) }}" method="POST">
            @csrf

            <div class="row g-3 g-md-4 align-items-start">
                
                <!-- 1. Left Column: Lembar Soal (Satu Halaman Satu Soal, Ukuran Proporsional) -->
                <div class="col-lg-8 col-xl-8">
                    
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
                                        <span class="text-muted small fw-semibold">dari {{ $totalQuestions }} Nomor</span>
                                    </div>
                                    
                                    <div class="d-flex align-items-center gap-2">
                                        @if($question->isMatching())
                                            <span class="badge bg-white text-primary border rounded-pill px-2.5 py-1 font-bold shadow-2xs" style="font-size: 0.76rem;">
                                                <i class="ti ti-award me-1"></i> {{ $question->options->count() }} Poin (1 Poin / Pasangan)
                                            </span>
                                        @else
                                            <span class="badge bg-white text-primary border rounded-pill px-2.5 py-1 font-bold shadow-2xs" style="font-size: 0.76rem;">
                                                <i class="ti ti-award me-1"></i> 1 Poin
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Question Body (Proporsional, Nyaman Dibaca, Responsif di Mobile) -->
                                <div class="p-3 p-sm-3.5 p-md-4 bg-white">
                                    
                                    <!-- Teks Pertanyaan Soal -->
                                    <div class="question-text-wrapper fw-bold text-dark mb-3" style="font-family: 'Jost', sans-serif;">
                                        {!! nl2br(e($question->question_text)) !!}
                                    </div>

                                    <!-- Options Container (Adaptive based on Question Type) -->
                                    <div class="mb-4">
                                        @if($question->isMatching())
                                            <!-- MATCHING QUESTION INTERFACE (Interactive Target Cards & Choice Pool) -->
                                            @php
                                                $shuffledMatches = $question->options->pluck('match_text')->filter()->unique()->sortBy(fn($m) => md5($attempt->id . '_m_' . $m))->values();
                                                $userMatchingData = $savedMatchingAnswers[$question->id] ?? [];
                                            @endphp
                                            
                                            <!-- Interactive Guide Banner -->
                                            <div class="matching-guide-banner p-3 rounded-4 mb-4 d-flex align-items-center gap-3">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 38px; height: 38px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px); color: #ffffff;">
                                                    <i class="ti ti-arrows-left-right fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-extrabold text-white" style="font-size: 0.92rem; letter-spacing: 0.3px;">Panduan Soal Menjodohkan</div>
                                                    <div class="text-white-50 small" style="font-size: 0.8rem;">Sentuh / klik <strong>Slot Target</strong> pada pernyataan, lalu pilih opsi <strong>Jawaban</strong> di bawah. Setiap pasangan dinilai 1 poin secara mandiri.</div>
                                                </div>
                                            </div>

                                            <!-- Matching Interactive Container -->
                                            <div class="matching-container" data-question-id="{{ $question->id }}" data-question-index="{{ $stepNumber }}">
                                                
                                                <!-- 1. Left Premises List (Target Slots) -->
                                                <div class="mb-4">
                                                    <h6 class="fw-bold text-uppercase text-secondary mb-3 d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.8px;">
                                                        <i class="ti ti-list-check text-primary"></i> Pernyataan / Pertanyaan
                                                    </h6>
                                                    
                                                    <div class="vstack gap-3">
                                                        @foreach($question->options as $pairIndex => $opt)
                                                            @php
                                                                $currentMatch = $userMatchingData[$opt->id] ?? '';
                                                            @endphp
                                                            <div class="matching-premise-card p-3 rounded-4 border bg-white shadow-xs" id="premise-card-{{ $question->id }}-{{ $opt->id }}" data-option-id="{{ $opt->id }}">
                                                                <!-- Hidden Input for Form Submission & Auto-Save -->
                                                                <input type="hidden" 
                                                                       name="matching_answers[{{ $question->id }}][{{ $opt->id }}]" 
                                                                       class="matching-input"
                                                                       data-question-id="{{ $question->id }}"
                                                                       data-option-id="{{ $opt->id }}"
                                                                       data-question-index="{{ $stepNumber }}"
                                                                       value="{{ $currentMatch }}">

                                                                <div class="row align-items-center g-3">
                                                                    <!-- Premise Text & Number Badge -->
                                                                    <div class="col-12 col-md-6 d-flex align-items-start gap-2.5">
                                                                        <span class="badge rounded-circle flex-shrink-0 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 28px; height: 28px; background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); color: #ffffff; font-weight: 700; font-size: 0.82rem;">
                                                                            {{ $pairIndex + 1 }}
                                                                        </span>
                                                                        <div class="fw-semibold text-dark pt-0.5" style="font-size: 0.92rem; line-height: 1.45;">
                                                                            {{ $opt->option_text }}
                                                                        </div>
                                                                    </div>

                                                                    <!-- Target Slot Display -->
                                                                    <div class="col-12 col-md-6">
                                                                        <div class="matching-target-slot rounded-3 p-2.5 d-flex align-items-center justify-content-between transition-all {{ $currentMatch ? 'has-match' : 'is-empty' }}"
                                                                             onclick="activatePremiseSlot({{ $question->id }}, {{ $opt->id }})"
                                                                             id="slot-{{ $question->id }}-{{ $opt->id }}"
                                                                             style="min-height: 44px; cursor: pointer;">
                                                                            @if($currentMatch)
                                                                                <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                                                    <i class="ti ti-circle-check-filled fs-5 text-success"></i>
                                                                                    <span class="fw-bold text-truncate" style="font-size: 0.88rem; color: #065F46;">
                                                                                        {{ $currentMatch }}
                                                                                    </span>
                                                                                </div>
                                                                                <button type="button" 
                                                                                        class="btn btn-sm btn-light border-0 rounded-circle d-flex align-items-center justify-content-center text-danger p-0 shadow-2xs" 
                                                                                        style="width: 26px; height: 26px;"
                                                                                        onclick="clearMatchingPair(event, {{ $question->id }}, {{ $opt->id }})"
                                                                                        title="Hapus Pasangan">
                                                                                    <i class="ti ti-x fs-6"></i>
                                                                                </button>
                                                                            @else
                                                                                <div class="d-flex align-items-center gap-2.5 text-muted" style="font-size: 0.84rem;">
                                                                                    <i class="ti ti-plus-circle fs-5 text-primary opacity-75"></i>
                                                                                    <span class="fw-medium text-slate-500">Pilih pasangan jawaban...</span>
                                                                                </div>
                                                                                <span class="badge bg-white text-secondary border px-2 py-1 shadow-2xs" style="font-size: 0.72rem;">Pilih</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- 2. Right Choice Palette (Matches Pool) -->
                                                <div class="matching-choices-pool p-3 p-sm-3.5 rounded-4 bg-slate-50 border" style="background-color: #F8FAFC; border-color: rgba(51, 104, 160, 0.15) !important;">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <h6 class="fw-bold text-uppercase text-secondary mb-0 d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.8px;">
                                                            <i class="ti ti-category text-primary"></i> Pilihan Jawaban
                                                        </h6>
                                                        <span class="badge bg-primary-subtle text-primary fw-semibold px-2.5 py-1" style="font-size: 0.72rem;">
                                                            {{ count($shuffledMatches) }} Pilihan
                                                        </span>
                                                    </div>

                                                    <div class="d-flex flex-wrap gap-2.5" id="choice-pool-{{ $question->id }}">
                                                        @foreach($shuffledMatches as $match)
                                                            @php
                                                                $usedByOptId = null;
                                                                foreach($userMatchingData as $oId => $val) {
                                                                    if (trim($val) === trim($match)) {
                                                                        $usedByOptId = $oId;
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp
                                                            <button type="button" 
                                                                    class="btn-match-chip btn rounded-3 px-3 py-2 text-start transition-all d-inline-flex align-items-center gap-2 {{ $usedByOptId ? 'is-used' : 'is-available' }}"
                                                                    data-match-value="{{ $match }}"
                                                                    data-question-id="{{ $question->id }}"
                                                                    onclick="selectMatchChip({{ $question->id }}, {{ json_encode($match) }})">
                                                                <i class="ti {{ $usedByOptId ? 'ti-check text-success' : 'ti-point text-primary' }} fs-5"></i>
                                                                <span class="fw-semibold" style="font-size: 0.88rem;">{{ $match }}</span>
                                                                @if($usedByOptId)
                                                                    <span class="badge bg-success-subtle text-success-emphasis ms-1" style="font-size: 0.68rem;">Terpasang</span>
                                                                @endif
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>

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
                                                        <label class="quiz-option-tf {{ $isSelected ? 'selected ' . ($isTrue ? 'tf-true' : 'tf-false') : '' }}" for="opt-{{ $option->id }}">
                                                            <input type="radio" 
                                                                   name="answers[{{ $question->id }}]" 
                                                                   id="opt-{{ $option->id }}" 
                                                                   value="{{ $option->id }}" 
                                                                   data-question-id="{{ $question->id }}"
                                                                   data-question-index="{{ $stepNumber }}"
                                                                   data-option-id="{{ $option->id }}"
                                                                   data-is-true="{{ $isTrue ? 'true' : 'false' }}"
                                                                   {{ $isSelected ? 'checked' : '' }}
                                                                   class="form-check-input option-radio d-none">
                                                            <div class="rounded-circle p-2 mb-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: {{ $isTrue ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $isTrue ? '#059669' : '#dc2626' }};">
                                                                <i class="ti {{ $isTrue ? 'ti-check' : 'ti-x' }} fs-4"></i>
                                                            </div>
                                                            <span class="fw-bold fs-6 {{ $isTrue ? 'text-success' : 'text-danger' }}">{{ $option->option_text }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <!-- MULTIPLE CHOICE OPTIONS LIST (Sleek Modern Tiles, No Duplicate Radio Circle) -->
                                            <div class="d-flex flex-column gap-2.5">
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
                                                               class="form-check-input option-radio d-none">
                                                        <span class="option-badge-letter">{{ $letter }}</span>
                                                        <span class="option-text-wrapper">{{ $option->option_text }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Bottom Action & Navigation Bar (Rapi & Sejajar di Semua Ukuran Layar) -->
                                    <div class="pt-3.5 mt-4 border-top" style="border-color: #f1f5f9 !important;">
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            
                                            <!-- Tombol Sebelumnya -->
                                            @if($index > 0)
                                                <button type="button" 
                                                        onclick="goToQuestion({{ $index }})" 
                                                        class="btn btn-outline-secondary rounded-pill px-3 py-2 font-bold d-inline-flex align-items-center justify-content-center gap-1.5 hover-lift quiz-nav-btn"
                                                        style="font-size: 0.86rem;">
                                                    <i class="ti ti-arrow-left"></i> <span>Sebelumnya</span>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-light rounded-pill px-3 py-2 text-muted fw-semibold quiz-nav-btn" disabled style="opacity: 0.5; cursor: not-allowed; font-size: 0.86rem;">
                                                    <i class="ti ti-arrow-left"></i> <span>Sebelumnya</span>
                                                </button>
                                            @endif

                                            <!-- Question Step Badge -->
                                            <div class="badge rounded-pill px-2.5 py-1.5 font-bold text-secondary d-inline-flex align-items-center gap-1 quiz-step-badge" style="background: #F1F5F9; font-size: 0.78rem;">
                                                <i class="ti ti-file-text text-primary"></i> <span>{{ $stepNumber }} / {{ $totalQuestions }}</span>
                                            </div>

                                            <!-- Tombol Selanjutnya & Tombol Selesai -->
                                            <div class="quiz-nav-btn-next-wrap">
                                                @if($index < $totalQuestions - 1)
                                                    <button type="button" 
                                                            onclick="goToQuestion({{ $stepNumber + 1 }})" 
                                                            class="btn text-white rounded-pill px-3.5 py-2 font-bold d-inline-flex align-items-center justify-content-center gap-1.5 hover-lift quiz-nav-btn w-100"
                                                            style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); font-size: 0.86rem;">
                                                        <span>Selanjutnya</span> <i class="ti ti-arrow-right"></i>
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            onclick="openSubmitConfirmationModal();" 
                                                            class="btn text-white rounded-pill px-3.5 py-2 font-bold d-inline-flex align-items-center justify-content-center gap-1.5 hover-lift shadow-sm quiz-nav-btn w-100"
                                                            style="background: linear-gradient(135deg, #059669 0%, #10B981 100%); font-size: 0.86rem;">
                                                        <i class="ti ti-circle-check"></i> <span>Kumpulkan</span>
                                                    </button>
                                                @endif
                                            </div>

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
                <div class="col-lg-4 col-xl-4">
                    <aside class="palette-sticky-card" id="paletteCardSection">
                        
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

                            </div>
                        </div>

                        <!-- Divider Antara Legenda & Tombol Kumpulkan -->
                        <hr class="my-3.5" style="border-color: rgba(51, 104, 160, 0.12);">

                        <!-- Sidebar Quick Submit Button -->
                        <div class="pt-1">
                            <button type="button" 
                                    onclick="openSubmitConfirmationModal();" 
                                    class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift"
                                    style="background: linear-gradient(135deg, #059669 0%, #10B981 100%); font-size: 0.92rem;">
                                <i class="ti ti-circle-check fs-5"></i> Selesai & Kumpulkan
                            </button>
                        </div>

                    </aside>
                </div>

            </div>

        </form>

    </div>

    <!-- Floating Mobile Question Palette Shortcut (Quick Navigation on Mobile) -->
    <div class="d-md-none position-fixed" style="bottom: 1.25rem; right: 1rem; z-index: 1015;">
        <button type="button" 
                onclick="scrollToPalette()" 
                class="btn btn-floating-palette rounded-pill py-2 px-3.5 d-flex align-items-center gap-1.5 shadow-lg"
                title="Buka Navigasi Soal">
            <i class="ti ti-layout-grid fs-5"></i>
            <span>Soal No.</span>
            <span class="badge bg-white text-primary rounded-pill px-2 py-0.5 font-bold" id="mobileCurrentNumBadge">1</span>
        </button>
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
                        <i class="ti ti-maximize fs-5"></i> Saya Mengerti, Mulai Kuis
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

            // Perbarui badge nomor aktif di floating mobile button
            const mobileBadge = document.getElementById('mobileCurrentNumBadge');
            if (mobileBadge) {
                mobileBadge.innerText = currentQuestion;
            }
        }

        function scrollToPalette() {
            const paletteEl = document.getElementById('paletteCardSection');
            if (paletteEl) {
                paletteEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
            const startModal = document.getElementById('examStartModal');
            if (startModal) startModal.style.display = 'none';
            isExamActive = true;

            requestFullscreenSafe().catch(err => {
                console.warn('Fullscreen request rejected or not supported on this device:', err);
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
                        const tiles = questionCard.querySelectorAll('.quiz-option-tile, .quiz-option-tf');
                        tiles.forEach(t => t.classList.remove('selected', 'tf-true', 'tf-false'));
                    }
                    const parentTile = this.closest('.quiz-option-tile, .quiz-option-tf');
                    if (parentTile) {
                        parentTile.classList.add('selected');
                        const isTrue = this.getAttribute('data-is-true');
                        if (isTrue === 'true') {
                            parentTile.classList.add('tf-true');
                        } else if (isTrue === 'false') {
                            parentTile.classList.add('tf-false');
                        }
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

            // 6b. Interactive Matching Question Helper Functions & Auto-Save
            window.activeMatchingTarget = null;

            window.activatePremiseSlot = function(questionId, optionId) {
                const card = document.getElementById('premise-card-' + questionId + '-' + optionId);
                if (!card) return;
                
                const container = card.closest('.matching-container');
                if (container) {
                    container.querySelectorAll('.matching-target-slot').forEach(slot => {
                        slot.classList.remove('active-slot');
                    });
                }

                const slot = document.getElementById('slot-' + questionId + '-' + optionId);
                if (slot) {
                    slot.classList.add('active-slot');
                    window.activeMatchingTarget = { questionId: questionId, optionId: optionId };
                }
            };

            window.selectMatchChip = function(questionId, matchValue) {
                const container = document.querySelector(`.matching-container[data-question-id="${questionId}"]`);
                if (!container) return;

                let targetOptionId = null;

                if (window.activeMatchingTarget && window.activeMatchingTarget.questionId === questionId) {
                    targetOptionId = window.activeMatchingTarget.optionId;
                } else {
                    const emptyInput = container.querySelector('.matching-input[value=""]');
                    if (emptyInput) {
                        targetOptionId = emptyInput.getAttribute('data-option-id');
                    } else {
                        const firstInput = container.querySelector('.matching-input');
                        if (firstInput) targetOptionId = firstInput.getAttribute('data-option-id');
                    }
                }

                if (!targetOptionId) return;

                const input = container.querySelector(`.matching-input[data-option-id="${targetOptionId}"]`);
                if (input) {
                    input.value = matchValue;
                }

                window.activeMatchingTarget = null;
                container.querySelectorAll('.matching-target-slot').forEach(slot => slot.classList.remove('active-slot'));

                updateMatchingQuestionState(questionId);
                saveMatchingQuestionAnswer(questionId, input ? parseInt(input.getAttribute('data-question-index')) : 1);
            };

            window.clearMatchingPair = function(event, questionId, optionId) {
                if (event) event.stopPropagation();

                const container = document.querySelector(`.matching-container[data-question-id="${questionId}"]`);
                if (!container) return;

                const input = container.querySelector(`.matching-input[data-option-id="${optionId}"]`);
                if (input) {
                    input.value = '';
                }

                updateMatchingQuestionState(questionId);
                saveMatchingQuestionAnswer(questionId, input ? parseInt(input.getAttribute('data-question-index')) : 1);
            };

            function updateMatchingQuestionState(questionId) {
                const container = document.querySelector(`.matching-container[data-question-id="${questionId}"]`);
                if (!container) return;

                const inputs = container.querySelectorAll('.matching-input');
                const usedMatches = new Set();

                inputs.forEach(input => {
                    const optionId = input.getAttribute('data-option-id');
                    const val = input.value.trim();
                    const slot = document.getElementById(`slot-${questionId}-${optionId}`);
                    
                    if (val !== '') {
                        usedMatches.add(val);
                        if (slot) {
                            slot.className = 'matching-target-slot rounded-3 p-2.5 d-flex align-items-center justify-content-between transition-all has-match';
                            slot.style.cursor = 'pointer';
                            slot.innerHTML = `
                                <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                    <i class="ti ti-circle-check-filled fs-5 text-success"></i>
                                    <span class="fw-bold text-truncate" style="font-size: 0.9rem; color: #065F46;">${escapeHtml(val)}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-light border-0 rounded-circle d-flex align-items-center justify-content-center text-danger p-0 shadow-2xs" style="width: 28px; height: 28px;" onclick="clearMatchingPair(event, ${questionId}, ${optionId})" title="Hapus Pasangan">
                                    <i class="ti ti-x fs-6"></i>
                                </button>
                            `;
                        }
                    } else {
                        if (slot) {
                            slot.className = 'matching-target-slot rounded-3 p-2.5 d-flex align-items-center justify-content-between transition-all is-empty';
                            slot.style.cursor = 'pointer';
                            slot.innerHTML = `
                                <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.88rem;">
                                    <i class="ti ti-plus-circle fs-5 text-primary opacity-75"></i>
                                    <span class="fw-medium text-slate-500">Pilih pasangan jawaban...</span>
                                </div>
                                <span class="badge bg-white text-secondary border px-2 py-1 shadow-2xs" style="font-size: 0.72rem;">Klik Opsi</span>
                            `;
                        }
                    }
                });

                const chips = container.querySelectorAll('.btn-match-chip');
                chips.forEach(chip => {
                    const val = chip.getAttribute('data-match-value').trim();
                    if (usedMatches.has(val)) {
                        chip.className = 'btn-match-chip btn rounded-3 px-3 py-2 text-start transition-all d-inline-flex align-items-center gap-2 is-used';
                        chip.innerHTML = `
                            <i class="ti ti-check text-success fs-5"></i>
                            <span class="fw-semibold" style="font-size: 0.9rem;">${escapeHtml(val)}</span>
                            <span class="badge bg-success-subtle text-success-emphasis ms-1" style="font-size: 0.7rem;">Terpasang</span>
                        `;
                    } else {
                        chip.className = 'btn-match-chip btn rounded-3 px-3 py-2 text-start transition-all d-inline-flex align-items-center gap-2 is-available';
                        chip.innerHTML = `
                            <i class="ti ti-point text-primary fs-5"></i>
                            <span class="fw-semibold" style="font-size: 0.9rem;">${escapeHtml(val)}</span>
                        `;
                    }
                });
            }

            function saveMatchingQuestionAnswer(questionId, qIndex) {
                const card = document.getElementById('question-step-' + qIndex);
                const pairsData = {};
                let hasSelection = false;

                if (card) {
                    card.querySelectorAll('.matching-input').forEach(input => {
                        const optId = input.getAttribute('data-option-id');
                        if (input.value.trim() !== '') {
                            pairsData[optId] = input.value.trim();
                            hasSelection = true;
                        }
                    });
                }

                if (hasSelection) {
                    answeredSet.add(qIndex);
                } else {
                    answeredSet.delete(qIndex);
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
                        question_id: questionId,
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
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }

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
                    timerElement.innerText = "00:00:00";
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

                timerElement.innerText = 
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0');
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
