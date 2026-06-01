{{-- ONYX Legal — Admin Layout --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — ONYX Advocates</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo-square.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo-square.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/logo-square.png') }}">
  <link rel="shortcut icon" href="{{ asset('images/logo-square.png') }}">

  {{-- All CSS served locally — zero CDN blocking requests --}}
  <link rel="stylesheet" href="{{ asset('vendor/fa/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.css') }}">
  {{-- Admin CSS --}}
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">

  @stack('styles')
</head>
<body>

{{-- Toast container --}}
<div id="oxToastContainer" class="ox-toast-container"></div>

{{-- Drawer overlay + panel --}}
<div id="oxDrawerOverlay" class="ox-drawer-overlay"></div>
<div id="oxDrawer" class="ox-drawer">
  <div class="ox-drawer-head">
    <span id="oxDrawerTitle" class="ox-drawer-title">Loading…</span>
    <button id="oxDrawerClose" class="ox-drawer-close" type="button">
      <i class="fas fa-times"></i>
    </button>
  </div>
  <div id="oxDrawerBody" class="ox-drawer-body">
    <div class="ox-spinner-wrap"><div class="ox-spinner"></div></div>
  </div>
</div>

{{-- Centered modal --}}
<div id="oxModalOverlay" class="ox-modal-overlay">
  <div id="oxModal" class="ox-modal md">
    <div class="ox-modal-head">
      <span id="oxModalTitle" class="ox-modal-title">Modal</span>
      <button id="oxModalClose" class="ox-drawer-close" type="button">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div id="oxModalBody" class="ox-modal-body">
      <div class="ox-spinner-wrap"><div class="ox-spinner"></div></div>
    </div>
  </div>
</div>

<div class="ad-wrapper">

  {{-- ═══════════ SIDEBAR ═══════════ --}}
  <aside class="ad-sidebar" id="adSidebar">

    <a href="{{ route('admin.dashboard') }}" class="ad-sidebar-brand">
      <div style="background:#fff;border-radius:7px;padding:5px 10px;display:inline-flex;align-items:center;flex-shrink:0;">
        <img src="{{ asset('images/logo-horizontal.png') }}"
             alt="ONYX Advocates"
             style="height:28px;width:auto;display:block;">
      </div>
      <button class="ad-sidebar-close" id="adSidebarClose" type="button"><i class="fas fa-times"></i></button>
    </a>

    @auth
    <div class="ad-sidebar-user" style="cursor:pointer;"
         onclick="ONYX.profile.open()" title="Edit my profile">
      @if(Auth::user()->avatar_url)
        <img src="{{ Auth::user()->avatar_url }}"
             alt="{{ Auth::user()->name }}"
             id="sidebarAvatarImg"
             style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;
                    border:2px solid rgba(196,149,106,.3);">
      @else
        <div class="ad-sidebar-avatar" id="sidebarAvatarInitials">
          {{ Auth::user()->initials }}
        </div>
        <img src="" alt="" id="sidebarAvatarImg"
             style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;
                    border:2px solid rgba(196,149,106,.3);display:none;">
      @endif
      <div class="ad-sidebar-user-info">
        <div class="ad-sidebar-uname" id="sidebarUserName">{{ Auth::user()->name ?? 'User' }}</div>
        <div class="ad-sidebar-role">{{ Auth::user()->role_label ?? ucfirst(Auth::user()->role ?? 'admin') }}</div>
      </div>
    </div>
    @endauth

    <nav class="ad-nav">
      <ul>
        <li class="ad-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('admin.dashboard') }}"><i class="fas fa-gauge-high"></i> Dashboard</a>
        </li>

        <li class="ad-nav-section">Legal</li>
        <li class="ad-nav-item {{ request()->routeIs('admin.cases.*') ? 'active' : '' }}">
          <a href="{{ route('admin.cases.index') }}"><i class="fas fa-scale-balanced"></i> Cases</a>
        </li>
        <li class="ad-nav-item {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
          <a href="{{ route('admin.clients.index') }}"><i class="fas fa-users"></i> Clients</a>
        </li>
        <li class="ad-nav-item {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
          <a href="{{ route('admin.documents.index') }}"><i class="fas fa-folder-open"></i> Documents</a>
        </li>

        <li class="ad-nav-section">Finance</li>
        <li class="ad-nav-item {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
          <a href="{{ route('admin.transactions.index') }}"><i class="fas fa-money-bill-transfer"></i> Transactions</a>
        </li>
        <li class="ad-nav-item {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
          <a href="{{ route('admin.accounts.index') }}"><i class="fas fa-building-columns"></i> Accounts</a>
        </li>
        <li class="ad-nav-item {{ request()->routeIs('admin.periods.*') ? 'active' : '' }}">
          <a href="{{ route('admin.periods.index') }}"><i class="fas fa-calendar-alt"></i> Periods</a>
        </li>

        @if(Auth::check() && in_array(Auth::user()->role, ['admin','officer']))
        <li class="ad-nav-section">Insight</li>
        <li class="ad-nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
          <a href="{{ route('admin.reports.index') }}"><i class="fas fa-chart-bar"></i> Reports</a>
        </li>
        @endif

        @if(Auth::check() && Auth::user()->role === 'admin')
        <li class="ad-nav-section">System</li>
        <li class="ad-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
          <a href="{{ route('admin.users.index') }}"><i class="fas fa-user-shield"></i> Users</a>
        </li>
        <li class="ad-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
          <a href="{{ route('admin.settings.index') }}"><i class="fas fa-gear"></i> Settings</a>
        </li>
        @endif
      </ul>
    </nav>

    <div class="ad-sidebar-footer">
      <form method="POST" action="{{ route('admin.logout') }}" style="width:100%">
        @csrf
        <button type="submit" class="ad-logout-btn">
          <i class="fas fa-right-from-bracket"></i> Sign Out
        </button>
      </form>
    </div>
  </aside>

  {{-- ═══════════ MAIN ═══════════ --}}
  <div class="ad-main">

    <header class="ad-topbar">
      <div class="ad-topbar-left">
        <button class="ad-topbar-toggle" id="adSidebarToggle" type="button"><i class="fas fa-bars"></i></button>
        <span class="ad-page-title">@yield('title', 'Dashboard')</span>
      </div>
      <div class="ad-topbar-right">
        {{-- Quick actions --}}
        <div style="display:flex;align-items:center;gap:2px;">
          <button onclick="ONYX.cases.quickCreate()" title="New Case"
                  style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;
                         background:none;border:1px solid var(--bd);border-radius:var(--r);
                         font-size:.72rem;font-weight:500;color:var(--mt);cursor:pointer;
                         font-family:inherit;white-space:nowrap;transition:all .12s;">
            <i class="fas fa-scale-balanced" style="font-size:.8rem;"></i>
            <span class="topbar-btn-text">Case</span>
          </button>
          <button onclick="ONYX.clients.quickCreate()" title="New Client"
                  style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;
                         background:none;border:1px solid var(--bd);border-radius:var(--r);
                         font-size:.72rem;font-weight:500;color:var(--mt);cursor:pointer;
                         font-family:inherit;white-space:nowrap;transition:all .12s;">
            <i class="fas fa-user-plus" style="font-size:.8rem;"></i>
            <span class="topbar-btn-text">Client</span>
          </button>
          <button onclick="ONYX.transactions.quickRecord()" title="Record Transaction"
                  style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;
                         background:none;border:1px solid var(--bd);border-radius:var(--r);
                         font-size:.72rem;font-weight:500;color:var(--mt);cursor:pointer;
                         font-family:inherit;white-space:nowrap;transition:all .12s;">
            <i class="fas fa-money-bill-transfer" style="font-size:.8rem;"></i>
            <span class="topbar-btn-text">Txn</span>
          </button>
        </div>

        <div style="width:1px;height:20px;background:var(--bd);margin:0 4px;"></div>

        {{-- User dropdown --}}
        <div class="ad-user-menu" id="adUserMenu">
          <div class="ad-user-trigger" id="adUserTrigger">
            @if(Auth::user()->avatar_url)
              <img src="{{ Auth::user()->avatar_url }}"
                   alt="{{ Auth::user()->name }}"
                   id="topbarAvatarImg"
                   style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            @else
              <div class="ad-user-avatar-sm" id="topbarAvatarInitials">
                {{ Auth::user()->initials }}
              </div>
              <img src="" alt="" id="topbarAvatarImg"
                   style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0;display:none;">
            @endif
            <span class="ad-user-name" id="topbarUserName">{{ Auth::user()->name ?? 'User' }}</span>
            <i class="fas fa-chevron-down ad-user-caret"></i>
          </div>
          <div class="ad-user-dropdown" style="min-width:200px;">
            {{-- Profile header --}}
            <div style="padding:10px 14px 8px;display:flex;align-items:center;gap:9px;border-bottom:1px solid var(--bd);margin-bottom:4px;">
              @if(Auth::user()->avatar_url)
                <img src="{{ Auth::user()->avatar_url }}"
                     id="dropdownAvatarImg"
                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;flex-shrink:0;">
              @else
                <div id="dropdownAvatarInitials"
                     style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--br),var(--ac));
                            display:flex;align-items:center;justify-content:center;
                            font-weight:700;color:#fff;font-size:.8125rem;flex-shrink:0;">
                  {{ Auth::user()->initials }}
                </div>
                <img src="" alt="" id="dropdownAvatarImg"
                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;flex-shrink:0;display:none;">
              @endif
              <div style="min-width:0;">
                <div style="font-size:.8rem;font-weight:700;color:var(--tx);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" id="dropdownUserName">
                  {{ Auth::user()->name ?? 'User' }}
                </div>
                <div style="font-size:.65rem;color:var(--mt);">{{ Auth::user()->role_label ?? '' }}</div>
              </div>
            </div>
            <a href="#" onclick="event.preventDefault();ONYX.profile.open()">
              <i class="fas fa-user-pen"></i> My Profile
            </a>
            <a href="#" onclick="event.preventDefault();ONYX.profile.changePassword()">
              <i class="fas fa-lock"></i> Change Password
            </a>
            @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.settings.index') }}">
              <i class="fas fa-gear"></i> Settings
            </a>
            @endif
            <hr>
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <button type="submit" class="danger"><i class="fas fa-right-from-bracket"></i> Sign Out</button>
            </form>
          </div>
        </div>
      </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success') || session('error') || session('warning') || $errors->any())
    <div class="ad-flash-wrap">
      @if(session('success'))
        <div class="ad-alert ad-alert-success ad-auto-dismiss">
          <i class="fas fa-check-circle"></i> {{ session('success') }}
          <button class="ad-alert-x" onclick="this.closest('.ad-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
      @endif
      @if(session('error'))
        <div class="ad-alert ad-alert-error">
          <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
          <button class="ad-alert-x" onclick="this.closest('.ad-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
      @endif
      @if(session('warning'))
        <div class="ad-alert ad-alert-warning">
          <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
          <button class="ad-alert-x" onclick="this.closest('.ad-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
      @endif
      @if($errors->any())
        <div class="ad-alert ad-alert-error">
          <i class="fas fa-exclamation-circle"></i>
          <div><strong>Fix the following:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
          <button class="ad-alert-x" onclick="this.closest('.ad-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
      @endif
    </div>
    @endif

    <main class="ad-content">
      @yield('content')
    </main>

  </div>
</div>

{{-- ONYX_CONFIG must come BEFORE admin.js so the constants are readable on load --}}
<script>
  (function () {
    var loc  = window.location;
    var base = loc.pathname.replace(/\/admin(\/.*)?$/, '');
    var root = loc.protocol + '//' + loc.host + base;
    window.ONYX_CONFIG = {
      token: '{{ csrf_token() }}',
      base:  root + '/admin',
      api:   root + '/admin/api',
      user: {
        id:         {{ Auth::id() }},
        name:       '{{ addslashes(Auth::user()->name ?? '') }}',
        email:      '{{ addslashes(Auth::user()->email ?? '') }}',
        phone:      '{{ addslashes(Auth::user()->phone ?? '') }}',
        bio:        '{{ addslashes(Auth::user()->bio ?? '') }}',
        role:       '{{ Auth::user()->role ?? "admin" }}',
        role_label: '{{ Auth::user()->role_label ?? "" }}',
        admin:      {{ Auth::user()->isAdmin() ? 'true' : 'false' }},
        avatar_url: '{{ Auth::user()->avatar_url ?? "" }}',
        initials:   '{{ Auth::user()->initials ?? "" }}',
      }
    };
  })();
</script>

{{-- All JS served locally — zero CDN blocking requests --}}
<script src="{{ asset('vendor/js/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('vendor/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>
<script src="{{ asset('vendor/js/sortable.min.js') }}"></script>
<script src="{{ asset('vendor/js/chart.min.js') }}"></script>
{{-- Admin JS --}}
<script src="{{ asset('js/admin.js') }}?v={{ filemtime(public_path('js/admin.js')) }}"></script>

@stack('scripts')
</body>
</html>
