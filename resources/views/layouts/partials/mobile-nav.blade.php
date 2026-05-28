<nav class="mobile-bottom-nav">
    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>{{ __('dashboard.title') }}</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i>
                <span>{{ __('students.title') }}</span>
            </a>
            <a href="{{ route('admin.fees.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.fees*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i>
                <span>{{ __('fees.title') }}</span>
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>{{ __('attendance.title') }}</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>{{ __('settings.title') }}</span>
            </a>
        @elseif(auth()->user()->isTeacher())
            <a href="{{ route('teacher.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i><span>{{ __('dashboard.title') }}</span>
            </a>
            <a href="{{ route('teacher.attendance.index') }}" class="mobile-nav-item">
                <i class="fas fa-calendar-check"></i><span>{{ __('attendance.title') }}</span>
            </a>
            <a href="{{ route('teacher.results.index') }}" class="mobile-nav-item">
                <i class="fas fa-file-alt"></i><span>{{ __('exams.title') }}</span>
            </a>
            <a href="{{ route('teacher.diary.index') }}" class="mobile-nav-item">
                <i class="fas fa-book-open"></i><span>{{ __('diary.title') }}</span>
            </a>
        @elseif(auth()->user()->isStudent())
            <a href="{{ route('student.dashboard') }}" class="mobile-nav-item">
                <i class="fas fa-tachometer-alt"></i><span>{{ __('dashboard.title') }}</span>
            </a>
            <a href="{{ route('student.attendance.index') }}" class="mobile-nav-item">
                <i class="fas fa-calendar-check"></i><span>{{ __('attendance.title') }}</span>
            </a>
            <a href="{{ route('student.results.index') }}" class="mobile-nav-item">
                <i class="fas fa-chart-bar"></i><span>{{ __('exams.title') }}</span>
            </a>
            <a href="{{ route('student.fees.index') }}" class="mobile-nav-item">
                <i class="fas fa-money-bill"></i><span>{{ __('fees.title') }}</span>
            </a>
        @endif
    @endauth
</nav>
