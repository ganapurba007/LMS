<!-- Sidebar Navigation -->
<aside class="app-sidebar">
  <div class="sidebar-header">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
      <div class="sidebar-brand-icon">
        <img src="{{ asset('images/icon.png') }}"
             alt="{{ config('app.name') }} Icon"
             style="width:26px;height:26px;object-fit:contain;">
      </div>
      <span>{{ config('app.name') }}</span>
    </a>
    <button type="button" class="navbar-toggle-btn d-lg-none sidebar-toggle-btn" aria-label="Close sidebar">
      <i class="ti ti-x"></i>
    </button>
  </div>

  <div class="sidebar-content">
    <div class="sidebar-section-label">Main</div>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <i class="ti ti-dashboard"></i>
          <span>Dashboard</span>
        </a>
      </li>
    </ul>

    <div class="sidebar-section-label">Master Data</div>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.roles.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
          <i class="ti ti-shield-check"></i>
          <span>Role</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.users.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
          <i class="ti ti-users"></i>
          <span>User</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.classes.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
          <i class="ti ti-school"></i>
          <span>Kelas</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.subjects.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
          <i class="ti ti-book"></i>
          <span>Mata Pelajaran</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.question-banks.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.question-banks.*') ? 'active' : '' }}">
          <i class="ti ti-database"></i>
          <span>Bank Soal</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.material-banks.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.material-banks.*') ? 'active' : '' }}">
          <i class="ti ti-books"></i>
          <span>Bank Materi</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.assignment-banks.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.assignment-banks.*') ? 'active' : '' }}">
          <i class="ti ti-notebook"></i>
          <span>Bank Tugas</span>
        </a>
      </li>
    </ul>

    <div class="sidebar-section-label">Pembelajaran & Evaluasi</div>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.materials.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.materials.*') ? 'active' : '' }}">
          <i class="ti ti-file-text"></i>
          <span>Materi</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.assignments.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
          <i class="ti ti-clipboard-list"></i>
          <span>Tugas</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.quizzes.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
          <i class="ti ti-help-circle"></i>
          <span>Kuis & Ujian</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.submissions.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}">
          <i class="ti ti-checkup-list"></i>
          <span>Koreksi Tugas</span>
        </a>
      </li>
    </ul>

    <div class="sidebar-section-label">Laporan</div>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="{{ route('admin.reports.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
          <i class="ti ti-chart-bar"></i>
          <span>Laporan</span>
        </a>
      </li>
    </ul>
  </div>

  <div class="sidebar-footer">
    <div class="d-flex align-items-center gap-2">
      <div class="position-relative">
        <div class="avatar avatar-sm bg-primary text-white">
          {{ strtoupper(substr(Auth::user()->name ?? 'G', 0, 2)) }}
        </div>
        <span class="status-indicator status-indicator-online position-absolute bottom-0 end-0"></span>
      </div>
      <div class="overflow-hidden">
        <div class="fw-semibold text-truncate small heading-custom">{{ Auth::user()->name ?? 'User' }}</div>
        <div class="text-muted-custom small text-truncate">{{ Auth::user()->role->display_name ?? 'Guru / Admin' }}</div>
      </div>
    </div>
  </div>
</aside>
