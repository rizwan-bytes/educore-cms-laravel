<div class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        @if(\App\Services\SettingService::get('college_logo'))
            <img src="{{ asset('storage/' . \App\Services\SettingService::get('college_logo')) }}"
                 alt="{{ \App\Services\SettingService::get('college_name', 'EduCore') }}"
                 class="sidebar-logo-img">
        @else
            <div class="brand-icon"><i class="fas fa-graduation-cap"></i></div>
        @endif
        <div>
            <span class="brand-text">{{ \App\Services\SettingService::get('college_name', 'EduCore') }}</span>
            <span class="brand-sub">{{ \App\Services\SettingService::get('college_tagline', 'Management System') }}</span>
        </div>
    </div>

    {{-- User profile link (between brand and nav, same as original) --}}
    @auth
    <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="sidebar-user" style="text-decoration:none" title="View Profile">
        @if(auth()->user()->avatar)
            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="user-avatar-img">
        @else
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        @endif
        <div style="min-width:0;flex:1">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <span class="badge-role">{{ ucfirst(auth()->user()->role) }}</span>
        </div>
        <i class="fas fa-pen" style="font-size:.6rem;color:var(--muted);flex-shrink:0"></i>
    </a>
    @endauth

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        @auth
            @if(auth()->user()->isAdmin())
                @include('layouts.partials.sidebar-admin')
            @elseif(auth()->user()->isTeacher())
                @include('layouts.partials.sidebar-teacher')
            @elseif(auth()->user()->isStudent())
                @include('layouts.partials.sidebar-student')
            @endif
        @endauth
    </nav>

    {{-- Footer: logout only --}}
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="nav-item text-danger-soft"
                    style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
                <i class="fas fa-right-from-bracket"></i>
                <span>{{ __('common.logout') }}</span>
            </button>
        </form>
    </div>

</div>
