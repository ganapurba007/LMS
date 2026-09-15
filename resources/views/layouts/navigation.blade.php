@php
    $userNotifications = Auth::check() ? Auth::user()->notifications()->latest()->take(6)->get() : collect();
    $unreadNotifCount = Auth::check() ? Auth::user()->notifications()->where('is_read', false)->count() : 0;
@endphp

<nav x-data="{ open: false, openNotif: false, scrolled: false }" 
     x-init="scrolled = (window.pageYOffset > 20)" 
     @scroll.window="scrolled = (window.pageYOffset > 20)" 
     :class="scrolled ? 'arsha-nav-scrolled' : 'arsha-nav-top'"
     class="edusite-header arsha-header sticky-top transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            
            <!-- Left: Brand Logo -->
            <div class="flex items-center gap-4 shrink-0">
                <a href="{{ Auth::user() && Auth::user()->isGuru() ? route('admin.dashboard') : route('dashboard') }}"
                   style="display: flex; align-items: center; gap: 10px; text-decoration: none;"
                   onmouseenter="this.querySelector('.brand-icon').style.transform='scale(1.08) rotate(-4deg)'; this.querySelector('.brand-name').style.color='#bae6fd';"
                   onmouseleave="this.querySelector('.brand-icon').style.transform='scale(1) rotate(0deg)'; this.querySelector('.brand-name').style.color='#ffffff';">
                    <!-- Icon Image -->
                    <img src="{{ asset('images/icon.png') }}"
                         alt="RuangTerra Icon"
                         class="brand-icon"
                         style="width: 56px; height: 62px; object-fit: contain; filter: drop-shadow(0 2px 10px rgba(56,189,248,0.55)); transition: transform 0.25s ease;">
                    <!-- Brand Name -->
                    <div style="line-height: 1.1;">
                        <div class="brand-name"
                             style="font-family: 'Jost', sans-serif; font-size: 1.5rem; font-weight: 900; color: #ffffff; letter-spacing: 0.5px; transition: color 0.2s ease;">
                            Ruang<span style="color: #38bdf8; text-shadow: 0 0 16px rgba(56,189,248,0.6);">Terra</span>
                        </div>
                        <div style="font-size: 9.5px; font-weight: 600; color: rgba(148,197,253,0.75); letter-spacing: 1.8px; text-transform: uppercase; margin-top: 1px;">
                            Learning Platform
                        </div>
                    </div>
                </a>
            </div>

            <!-- Middle: Main Menu Navigation Links (Clean Text-Only Styling with Refined Glassmorphic States) -->
            <div class="arsha-nav-desktop items-center space-x-1.5 lg:space-x-2.5">
                <a href="{{ route('dashboard') }}" 
                   class="arsha-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                @if(Auth::check())
                    <a href="{{ route('student.materials.index') }}" 
                       class="arsha-nav-link {{ request()->routeIs('student.materials.*') ? 'active' : '' }}">
                        Courses / Materi
                    </a>
                    <a href="{{ route('student.assignments.index') }}" 
                       class="arsha-nav-link {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
                        Tugas Kelas
                    </a>
                    <a href="{{ route('student.quizzes.index') }}" 
                       class="arsha-nav-link {{ request()->routeIs('student.quizzes.*') ? 'active' : '' }}">
                        Kuis Online
                    </a>
                    <a href="{{ route('student.report.index') }}" 
                       class="arsha-nav-link {{ request()->routeIs('student.report.*') ? 'active' : '' }}">
                        Laporan Diri
                    </a>
                @endif
            </div>

            <!-- Right: User Account & Notifications -->
            <div class="arsha-nav-desktop items-center gap-3">
                <!-- Notification Indicator Bell & Dropdown -->
                <div class="relative" @click.outside="openNotif = false">
                    <button @click="openNotif = !openNotif"
                            class="notif-bell-btn relative flex items-center justify-center focus:outline-none"
                            style="width: 38px; height: 38px; background: transparent; border: none; padding: 0;"
                            onmouseenter="this.querySelector('i').style.color='#bae6fd'; this.querySelector('i').style.transform='scale(1.18) rotate(-10deg)';"
                            onmouseleave="this.querySelector('i').style.color='#ffffff'; this.querySelector('i').style.transform='scale(1) rotate(0deg)';">
                        <i class="ti ti-bell-ringing text-xl" style="color: #ffffff; transition: color 0.2s ease, transform 0.2s ease;"></i>
                        @if($unreadNotifCount > 0)
                            <!-- Red dot indicator -->
                            <span style="position: absolute; top: 2px; right: 2px; width: 10px; height: 10px; display: flex; align-items: center; justify-content: center;">
                                <span class="animate-ping" style="position: absolute; display: inline-flex; width: 10px; height: 10px; border-radius: 9999px; background-color: #ef4444; opacity: 0.75;"></span>
                                <span style="position: relative; display: inline-flex; width: 8px; height: 8px; border-radius: 9999px; background-color: #dc2626; border: 2px solid #ffffff; box-shadow: 0 0 4px rgba(220,38,38,0.6);"></span>
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="openNotif"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         style="position: absolute; right: 0; margin-top: 12px; width: 360px; border-radius: 16px; overflow: hidden; z-index: 9999; background: rgba(10, 18, 40, 0.98); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 24px 60px rgba(0,0,0,0.55), 0 0 0 1px rgba(56,189,248,0.08);"
                         x-cloak>

                        <!-- Header -->
                        <div style="padding: 14px 16px; background: linear-gradient(135deg, rgba(14,165,233,0.18) 0%, rgba(30,41,59,0.6) 100%); border-bottom: 1px solid rgba(255,255,255,0.10); display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(14,165,233,0.25); border: 1px solid rgba(56,189,248,0.35); display: flex; align-items: center; justify-content: center;">
                                    <i class="ti ti-bell-ringing" style="font-size: 17px; color: #38bdf8;"></i>
                                </div>
                                <div>
                                    <div style="font-size: 14px; font-weight: 800; color: #ffffff; line-height: 1.2;">Notifikasi</div>
                                    @if($unreadNotifCount > 0)
                                        <div style="display: flex; align-items: center; gap: 5px; margin-top: 2px;">
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #ef4444; display: inline-block; animation: pulse 1.5s infinite;"></span>
                                            <span style="font-size: 11px; font-weight: 600; color: #fca5a5;">{{ $unreadNotifCount }} belum dibaca</span>
                                        </div>
                                    @else
                                        <div style="display: flex; align-items: center; gap: 5px; margin-top: 2px;">
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #34d399; display: inline-block;"></span>
                                            <span style="font-size: 11px; font-weight: 600; color: #6ee7b7;">Semua sudah dibaca</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($unreadNotifCount > 0)
                                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                                    @csrf
                                    <button type="submit"
                                            style="font-size: 11px; font-weight: 700; color: #7dd3fc; background: rgba(14,165,233,0.18); border: 1px solid rgba(56,189,248,0.35); border-radius: 20px; padding: 5px 11px; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s;"
                                            onmouseenter="this.style.background='rgba(14,165,233,0.35)'; this.style.color='#ffffff';"
                                            onmouseleave="this.style.background='rgba(14,165,233,0.18)'; this.style.color='#7dd3fc';">
                                        <i class="ti ti-checks" style="font-size: 12px;"></i> Baca Semua
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Notification List -->
                        <div style="max-height: 380px; overflow-y: auto; padding: 8px;">
                            @forelse($userNotifications as $notif)
                                @php
                                    $typeConfig = match($notif->type) {
                                        'new_material'   => ['icon' => 'ti-book-2',    'iconColor' => '#38bdf8', 'bgColor' => 'rgba(56,189,248,0.15)',  'borderColor' => 'rgba(56,189,248,0.5)',  'badge' => 'Materi',  'badgeBg' => 'rgba(56,189,248,0.2)',  'badgeColor' => '#7dd3fc', 'badgeBorder' => 'rgba(56,189,248,0.4)'],
                                        'new_assignment' => ['icon' => 'ti-file-pencil','iconColor' => '#fbbf24', 'bgColor' => 'rgba(251,191,36,0.15)',  'borderColor' => 'rgba(251,191,36,0.5)',  'badge' => 'Tugas',   'badgeBg' => 'rgba(251,191,36,0.2)',  'badgeColor' => '#fde68a', 'badgeBorder' => 'rgba(251,191,36,0.4)'],
                                        'new_quiz'       => ['icon' => 'ti-alarm',      'iconColor' => '#fb7185', 'bgColor' => 'rgba(244,63,94,0.15)',   'borderColor' => 'rgba(244,63,94,0.5)',   'badge' => 'Kuis',    'badgeBg' => 'rgba(244,63,94,0.2)',   'badgeColor' => '#fda4af', 'badgeBorder' => 'rgba(244,63,94,0.4)'],
                                        default          => ['icon' => 'ti-messages',   'iconColor' => '#34d399', 'bgColor' => 'rgba(16,185,129,0.15)',  'borderColor' => 'rgba(16,185,129,0.5)', 'badge' => 'Diskusi', 'badgeBg' => 'rgba(16,185,129,0.2)',  'badgeColor' => '#6ee7b7', 'badgeBorder' => 'rgba(16,185,129,0.4)'],
                                    };
                                @endphp

                                <a href="{{ $notif->resolved_url ?? '#' }}"
                                   onclick="event.preventDefault(); document.getElementById('mark-read-form-{{ $notif->id }}').submit();"
                                   style="display: flex; align-items: flex-start; gap: 12px; padding: 11px 12px; border-radius: 12px; margin-bottom: 5px; text-decoration: none; cursor: pointer; transition: background 0.2s;
                                          {{ $notif->is_read
                                              ? 'background: rgba(255,255,255,0.03); opacity: 0.7;'
                                              : 'background: rgba(255,255,255,0.06); border-left: 3px solid ' . $typeConfig['borderColor'] . ';' }}"
                                   onmouseenter="this.style.background='rgba(255,255,255,0.1)';"
                                   onmouseleave="this.style.background='{{ $notif->is_read ? 'rgba(255,255,255,0.03)' : 'rgba(255,255,255,0.06)' }}';">

                                    <!-- Icon -->
                                    <div style="flex-shrink: 0; width: 38px; height: 38px; border-radius: 10px; background: {{ $typeConfig['bgColor'] }}; border: 1px solid {{ $typeConfig['borderColor'] }}; display: flex; align-items: center; justify-content: center; margin-top: 1px;">
                                        <i class="ti {{ $typeConfig['icon'] }}" style="font-size: 17px; color: {{ $typeConfig['iconColor'] }};"></i>
                                    </div>

                                    <!-- Content -->
                                    <div style="flex: 1; min-width: 0;">
                                        <!-- Top row: badge + time -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 4px;">
                                            <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: {{ $typeConfig['badgeBg'] }}; color: {{ $typeConfig['badgeColor'] }}; border: 1px solid {{ $typeConfig['badgeBorder'] }}; letter-spacing: 0.3px;">
                                                {{ $typeConfig['badge'] }}
                                            </span>
                                            <span style="font-size: 10px; color: #64748b; white-space: nowrap; display: flex; align-items: center; gap: 3px;">
                                                <i class="ti ti-clock" style="font-size: 10px;"></i>
                                                {{ $notif->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <!-- Title -->
                                        <div style="font-size: 12px; font-weight: 700; color: #f1f5f9; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $notif->title }}
                                        </div>

                                        <!-- Message -->
                                        <div style="font-size: 11px; color: #94a3b8; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $notif->message }}
                                        </div>
                                    </div>

                                    <!-- Unread badge -->
                                    @if(!$notif->is_read)
                                        <div style="flex-shrink: 0; margin-top: 2px;">
                                            <span style="display: inline-flex; align-items: center; font-size: 9px; font-weight: 800; padding: 2px 7px; border-radius: 4px; background: rgba(220,38,38,0.25); color: #fca5a5; border: 1px solid rgba(239,68,68,0.45); letter-spacing: 0.3px;">
                                                BARU
                                            </span>
                                        </div>
                                    @endif
                                </a>

                                <form id="mark-read-form-{{ $notif->id }}" method="POST" action="{{ route('notifications.mark-read', $notif->id) }}" style="display: none;">
                                    @csrf
                                </form>
                            @empty
                                <!-- Empty State -->
                                <div style="padding: 32px 16px; text-align: center;">
                                    <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                        <i class="ti ti-bell-off" style="font-size: 26px; color: #475569;"></i>
                                    </div>
                                    <div style="font-size: 13px; font-weight: 700; color: #cbd5e1; margin-bottom: 4px;">Tidak ada notifikasi</div>
                                    <div style="font-size: 11px; color: #475569;">Semua notifikasi sudah kamu baca 🎉</div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Footer -->
                        <div style="padding: 10px 16px; border-top: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.02); text-align: center;">
                            <span style="font-size: 10px; font-weight: 600; color: #546988ff; letter-spacing: 0.5px; text-transform: uppercase;">RuangTerra · Learning Management System</span>
                        </div>
                    </div>
                </div>

                @if(Auth::user() && Auth::user()->isGuru())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm text-white font-bold rounded-full px-4 py-2 text-decoration-none shadow-sm hover-lift" style="background: rgba(255, 255, 255, 0.25); border: 1px solid rgba(255, 255, 255, 0.4);">
                        <i class="ti ti-dashboard me-1 text-sky-300"></i> Admin Guru
                    </a>
                @endif

                <!-- User Dropdown (Alpine.js manual) -->
                <div style="position: relative;" @click.outside="openUser = false" x-data="{ openUser: false }">
                    <!-- Trigger -->
                    <button @click="openUser = !openUser"
                            style="display: inline-flex; align-items: center; gap: 10px; background: transparent; border: none; padding: 4px 2px; cursor: pointer; outline: none;"
                            onmouseenter="this.querySelector('.user-name-text').style.color='#bae6fd';"
                            onmouseleave="this.querySelector('.user-name-text').style.color='#ffffff';">
                        <!-- Avatar -->
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: #ffffff; flex-shrink: 0; box-shadow: 0 2px 8px rgba(56,189,248,0.4);">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <!-- Name & Class -->
                        <div style="text-align: left; line-height: 1.2;">
                            <div class="user-name-text" style="font-size: 13px; font-weight: 700; color: #ffffff; transition: color 0.2s ease;">{{ Auth::user()->name }}</div>
                            <div style="font-size: 11px; font-weight: 500; color: #7dd3fc;">{{ Auth::user()->schoolClass->name ?? 'Siswa SMA' }}</div>
                        </div>
                        <!-- Chevron -->
                        <i class="ti ti-chevron-down" :style="openUser ? 'transform: rotate(180deg); color: #38bdf8;' : 'transform: rotate(0deg); color: #7dd3fc;'" style="font-size: 13px; transition: transform 0.2s ease, color 0.2s ease;"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="openUser"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         style="position: absolute; right: 0; margin-top: 12px; width: 220px; border-radius: 14px; overflow: hidden; z-index: 9999; background: rgba(10, 18, 40, 0.98); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 0 1px rgba(56,189,248,0.07);"
                         x-cloak>

                        <!-- User Info Header -->
                        <div style="padding: 14px 16px; background: linear-gradient(135deg, rgba(14,165,233,0.15) 0%, rgba(30,41,59,0.5) 100%); border-bottom: 1px solid rgba(255,255,255,0.09);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; color: #ffffff; flex-shrink: 0; box-shadow: 0 2px 8px rgba(56,189,248,0.35);">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-size: 13px; font-weight: 700; color: #f1f5f9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</div>
                                    <div style="font-size: 11px; color: #7dd3fc; margin-top: 1px;">{{ Auth::user()->schoolClass->name ?? 'Siswa SMA' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div style="padding: 6px;">
                            <a href="{{ route('profile.edit') }}"
                               style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; text-decoration: none; color: #cbd5e1; font-size: 13px; font-weight: 500; transition: background 0.15s;"
                               onmouseenter="this.style.background='rgba(255,255,255,0.08)'; this.style.color='#ffffff';"
                               onmouseleave="this.style.background='transparent'; this.style.color='#cbd5e1';">
                                <span style="width: 30px; height: 30px; border-radius: 8px; background: rgba(56,189,248,0.15); border: 1px solid rgba(56,189,248,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="ti ti-user" style="font-size: 14px; color: #38bdf8;"></i>
                                </span>
                                Profil Saya
                            </a>

                            <div style="height: 1px; background: rgba(255,255,255,0.07); margin: 4px 6px;"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();"
                                   style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; text-decoration: none; color: #fca5a5; font-size: 13px; font-weight: 500; transition: background 0.15s;"
                                   onmouseenter="this.style.background='rgba(239,68,68,0.12)'; this.style.color='#fca5a5';"
                                   onmouseleave="this.style.background='transparent'; this.style.color='#fca5a5';">
                                    <span style="width: 30px; height: 30px; border-radius: 8px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="ti ti-logout" style="font-size: 14px; color: #f87171;"></i>
                                    </span>
                                    Keluar (Logout)
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile: Notification + Hamburger (ONLY visible on mobile) -->
            <div class="arsha-hamburger-btn" style="display: flex; align-items: center; gap: 6px;">

                <!-- Mobile Notification Bell -->
                <div style="position: relative;" @click.outside="openNotif = false">
                    <button @click="openNotif = !openNotif"
                            style="width: 38px; height: 38px; background: transparent; border: none; padding: 0; cursor: pointer; outline: none; display: flex; align-items: center; justify-content: center; position: relative;"
                            onmouseenter="this.querySelector('i').style.color='#bae6fd';"
                            onmouseleave="this.querySelector('i').style.color='#ffffff';">
                        <i class="ti ti-bell-ringing" style="font-size: 20px; color: #ffffff; transition: color 0.2s;"></i>
                        @if($unreadNotifCount > 0)
                            <span style="position: absolute; top: 4px; right: 4px; width: 9px; height: 9px; display: flex; align-items: center; justify-content: center;">
                                <span class="animate-ping" style="position: absolute; display: inline-flex; width: 9px; height: 9px; border-radius: 50%; background-color: #ef4444; opacity: 0.75;"></span>
                                <span style="position: relative; display: inline-flex; width: 7px; height: 7px; border-radius: 50%; background-color: #dc2626; border: 1.5px solid #fff;"></span>
                            </span>
                        @endif
                    </button>

                    <!-- Mobile Notif Dropdown -->
                    <div x-show="openNotif"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         style="position: fixed; right: 12px; top: 72px; width: calc(100vw - 24px); max-width: 360px; border-radius: 16px; overflow: hidden; z-index: 9999; background: rgba(10,18,40,0.98); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 24px 60px rgba(0,0,0,0.6);"
                         x-cloak>

                        <!-- Header -->
                        <div style="padding: 14px 16px; background: linear-gradient(135deg, rgba(14,165,233,0.18) 0%, rgba(30,41,59,0.6) 100%); border-bottom: 1px solid rgba(255,255,255,0.10); display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 34px; height: 34px; border-radius: 9px; background: rgba(14,165,233,0.25); border: 1px solid rgba(56,189,248,0.35); display: flex; align-items: center; justify-content: center;">
                                    <i class="ti ti-bell-ringing" style="font-size: 16px; color: #38bdf8;"></i>
                                </div>
                                <div>
                                    <div style="font-size: 14px; font-weight: 800; color: #ffffff;">Notifikasi</div>
                                    @if($unreadNotifCount > 0)
                                        <div style="display: flex; align-items: center; gap: 5px; margin-top: 2px;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                                            <span style="font-size: 11px; font-weight: 600; color: #fca5a5;">{{ $unreadNotifCount }} belum dibaca</span>
                                        </div>
                                    @else
                                        <div style="display: flex; align-items: center; gap: 5px; margin-top: 2px;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #34d399; display: inline-block;"></span>
                                            <span style="font-size: 11px; font-weight: 600; color: #6ee7b7;">Semua sudah dibaca</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($unreadNotifCount > 0)
                                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                                    @csrf
                                    <button type="submit" style="font-size: 11px; font-weight: 700; color: #7dd3fc; background: rgba(14,165,233,0.18); border: 1px solid rgba(56,189,248,0.35); border-radius: 20px; padding: 4px 10px; cursor: pointer;">
                                        <i class="ti ti-checks" style="font-size: 11px;"></i> Baca Semua
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Notif List (reuse same items) -->
                        <div style="max-height: 320px; overflow-y: auto; padding: 8px;">
                            @forelse($userNotifications as $notif)
                                @php
                                    $typeConfigM = match($notif->type) {
                                        'new_material'   => ['icon' => 'ti-book-2',    'iconColor' => '#38bdf8', 'bgColor' => 'rgba(56,189,248,0.15)',  'borderColor' => 'rgba(56,189,248,0.5)',  'badge' => 'Materi'],
                                        'new_assignment' => ['icon' => 'ti-file-pencil','iconColor' => '#fbbf24', 'bgColor' => 'rgba(251,191,36,0.15)',  'borderColor' => 'rgba(251,191,36,0.5)',  'badge' => 'Tugas'],
                                        'new_quiz'       => ['icon' => 'ti-alarm',      'iconColor' => '#fb7185', 'bgColor' => 'rgba(244,63,94,0.15)',   'borderColor' => 'rgba(244,63,94,0.5)',   'badge' => 'Kuis'],
                                        default          => ['icon' => 'ti-messages',   'iconColor' => '#34d399', 'bgColor' => 'rgba(16,185,129,0.15)',  'borderColor' => 'rgba(16,185,129,0.5)', 'badge' => 'Diskusi'],
                                    };
                                @endphp
                                <a href="{{ $notif->resolved_url ?? '#' }}"
                                   onclick="event.preventDefault(); document.getElementById('mob-mark-read-{{ $notif->id }}').submit();"
                                   style="display: flex; align-items: flex-start; gap: 10px; padding: 10px 11px; border-radius: 11px; margin-bottom: 4px; text-decoration: none;
                                          {{ $notif->is_read ? 'background: rgba(255,255,255,0.03); opacity: 0.7;' : 'background: rgba(255,255,255,0.06); border-left: 3px solid ' . $typeConfigM['borderColor'] . ';' }}">
                                    <div style="width: 34px; height: 34px; border-radius: 9px; background: {{ $typeConfigM['bgColor'] }}; border: 1px solid {{ $typeConfigM['borderColor'] }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="ti {{ $typeConfigM['icon'] }}" style="font-size: 15px; color: {{ $typeConfigM['iconColor'] }};"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 11px; font-weight: 700; color: #f1f5f9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $notif->title }}</div>
                                        <div style="font-size: 10px; color: #94a3b8; margin-top: 2px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $notif->message }}</div>
                                        <div style="font-size: 10px; color: #475569; margin-top: 3px;"><i class="ti ti-clock" style="font-size: 9px;"></i> {{ $notif->created_at->diffForHumans() }}</div>
                                    </div>
                                    @if(!$notif->is_read)
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #dc2626; flex-shrink: 0; margin-top: 4px;"></span>
                                    @endif
                                </a>
                                <form id="mob-mark-read-{{ $notif->id }}" method="POST" action="{{ route('notifications.mark-read', $notif->id) }}" style="display:none;">@csrf</form>
                            @empty
                                <div style="padding: 28px 16px; text-align: center;">
                                    <i class="ti ti-bell-off" style="font-size: 28px; color: #334155;"></i>
                                    <div style="font-size: 12px; font-weight: 600; color: #475569; margin-top: 8px;">Tidak ada notifikasi</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Hamburger Toggle -->
                <button @click="open = !open"
                        style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22); padding: 0; cursor: pointer; outline: none; display: flex; align-items: center; justify-content: center; border-radius: 10px; transition: background 0.2s, border-color 0.2s;"
                        onmouseenter="this.style.background='rgba(255,255,255,0.22)'; this.style.borderColor='rgba(255,255,255,0.4)';"
                        onmouseleave="this.style.background='rgba(255,255,255,0.12)'; this.style.borderColor='rgba(255,255,255,0.22)';">
                    <svg style="width: 20px; height: 20px;" stroke="#ffffff" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Drawer -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden"
         style="background: rgba(8, 15, 35, 0.98); backdrop-filter: blur(20px); border-top: 1px solid rgba(255,255,255,0.1); box-shadow: 0 20px 40px rgba(0,0,0,0.5);">

        <!-- User Profile Card -->
        <div style="margin: 14px 14px 0; padding: 14px; border-radius: 14px; background: linear-gradient(135deg, rgba(14,165,233,0.15) 0%, rgba(30,41,59,0.5) 100%); border: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 800; color: #fff; flex-shrink: 0; box-shadow: 0 2px 10px rgba(56,189,248,0.4);">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <div style="min-width: 0;">
                    <div style="font-size: 14px; font-weight: 700; color: #f1f5f9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 11px; color: #7dd3fc; margin-top: 2px;">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div style="padding: 10px 14px;">
            <div style="font-size: 10px; font-weight: 700; color: #475569; letter-spacing: 1.5px; text-transform: uppercase; padding: 4px 4px 8px;">Menu</div>

            @php
                $mobileLinks = [
                    ['href' => route('dashboard'), 'label' => 'Dashboard', 'icon' => 'ti-home', 'active' => request()->routeIs('dashboard')],
                ];
                if(Auth::check()) {
                    $mobileLinks[] = ['href' => route('student.materials.index'),   'label' => 'Courses / Materi', 'icon' => 'ti-book-2',      'active' => request()->routeIs('student.materials.*')];
                    $mobileLinks[] = ['href' => route('student.assignments.index'), 'label' => 'Tugas Kelas',     'icon' => 'ti-file-pencil', 'active' => request()->routeIs('student.assignments.*')];
                    $mobileLinks[] = ['href' => route('student.quizzes.index'),     'label' => 'Kuis Online',     'icon' => 'ti-alarm',       'active' => request()->routeIs('student.quizzes.*')];
                    $mobileLinks[] = ['href' => route('student.report.index'),      'label' => 'Laporan Diri',    'icon' => 'ti-chart-bar',   'active' => request()->routeIs('student.report.*')];
                }
            @endphp

            <div style="display: flex; flex-direction: column; gap: 4px;">
                @foreach($mobileLinks as $link)
                    <a href="{{ $link['href'] }}"
                       style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 500; transition: background 0.15s;
                              {{ $link['active'] ? 'background: rgba(56,189,248,0.18); color: #38bdf8; border: 1px solid rgba(56,189,248,0.3);' : 'background: rgba(255,255,255,0.03); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.05);' }}">
                        <span style="width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
                                     {{ $link['active'] ? 'background: rgba(56,189,248,0.25); border: 1px solid rgba(56,189,248,0.4);' : 'background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);' }}">
                            <i class="ti {{ $link['icon'] }}" style="font-size: 15px; {{ $link['active'] ? 'color: #38bdf8;' : 'color: #94a3b8;' }}"></i>
                        </span>
                        {{ $link['label'] }}
                        @if($link['active'])
                            <span style="margin-left: auto; width: 6px; height: 6px; border-radius: 50%; background: #38bdf8;"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Divider -->
        <div style="height: 1px; background: rgba(255,255,255,0.07); margin: 0 14px;"></div>

        <!-- Account Actions -->
        <div style="padding: 10px 14px 16px;">
            <div style="font-size: 10px; font-weight: 700; color: #475569; letter-spacing: 1.5px; text-transform: uppercase; padding: 4px 4px 8px;">Akun</div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <a href="{{ route('profile.edit') }}"
                   style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 500; color: #cbd5e1; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                    <span style="width: 32px; height: 32px; border-radius: 9px; background: rgba(56,189,248,0.12); border: 1px solid rgba(56,189,248,0.25); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="ti ti-user" style="font-size: 15px; color: #38bdf8;"></i>
                    </span>
                    Profil Saya
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 500; color: #fca5a5; background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.15);">
                        <span style="width: 32px; height: 32px; border-radius: 9px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="ti ti-logout" style="font-size: 15px; color: #f87171;"></i>
                        </span>
                        Keluar (Logout)
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
