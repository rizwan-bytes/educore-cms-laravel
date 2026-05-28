@php
    $cur = request()->route()?->getName() ?? '';
    $a = fn($p) => str_starts_with($cur, $p) ? 'active' : '';
@endphp

<a href="{{ route('teacher.dashboard') }}" class="nav-item {{ $a('teacher.dashboard') }}">
    <i class="fas fa-gauge"></i>
    <span>{{ __('dashboard.title') }}</span>
</a>

<div class="nav-section">{{ __('common.academics') }}</div>
<a href="{{ route('teacher.attendance.index') }}" class="nav-item {{ $a('teacher.attendance') }}">
    <i class="fas fa-calendar-check"></i>
    <span>{{ __('attendance.title') }}</span>
</a>
<a href="{{ route('teacher.timetable.index') }}" class="nav-item {{ $a('teacher.timetable') }}">
    <i class="fas fa-table-cells"></i>
    <span>{{ __('common.timetable') }}</span>
</a>
<a href="{{ route('teacher.results.index') }}" class="nav-item {{ $a('teacher.results') }}">
    <i class="fas fa-chart-bar"></i>
    <span>{{ __('exams.results') }}</span>
</a>

<div class="nav-section">{{ __('common.diary') }}</div>
<a href="{{ route('teacher.diary.index') }}" class="nav-item {{ $a('teacher.diary') }}">
    <i class="fas fa-book"></i>
    <span>{{ __('diary.title') }}</span>
</a>
<a href="{{ route('teacher.diary-analytics.index') }}" class="nav-item {{ $a('teacher.diary-analytics') }}">
    <i class="fas fa-chart-pie"></i>
    <span>{{ __('diary.analytics') }}</span>
</a>
<a href="{{ route('teacher.homework-submissions.index') }}" class="nav-item {{ $a('teacher.homework-submissions') }}">
    <i class="fas fa-images"></i>
    <span>{{ __('diary.hw_submitted') }}</span>
</a>
<a href="{{ route('teacher.diary-templates.index') }}" class="nav-item {{ $a('teacher.diary-templates') }}">
    <i class="fas fa-layer-group"></i>
    <span>{{ __('diary.templates') }}</span>
</a>

<div class="nav-section">{{ __('common.communication') }}</div>
<a href="{{ route('teacher.notices.index') }}" class="nav-item {{ $a('teacher.notices') }}">
    <i class="fas fa-bell"></i>
    <span>{{ __('notices.title') }}</span>
</a>
<a href="{{ route('teacher.syllabus.index') }}" class="nav-item {{ $a('teacher.syllabus') }}">
    <i class="fas fa-book-open"></i>
    <span>{{ __('common.syllabus') }}</span>
</a>
<a href="{{ route('teacher.library.index') }}" class="nav-item {{ $a('teacher.library') }}">
    <i class="fas fa-book-bookmark"></i>
    <span>{{ __('common.library') }}</span>
</a>

<div class="nav-section">{{ __('common.hr') }}</div>
<a href="{{ route('teacher.leaves.index') }}" class="nav-item {{ $a('teacher.leaves') }}">
    <i class="fas fa-calendar-minus"></i>
    <span>{{ __('common.my_leaves') }}</span>
</a>
<a href="{{ route('teacher.ptm.index') }}" class="nav-item {{ $a('teacher.ptm') }}">
    <i class="fas fa-handshake"></i>
    <span>{{ __('common.ptm_schedule') }}</span>
</a>

<div class="nav-section">{{ __('common.account') }}</div>
<a href="{{ route('teacher.profile.index') }}" class="nav-item {{ $a('teacher.profile') }}">
    <i class="fas fa-circle-user"></i>
    <span>{{ __('common.my_profile') }}</span>
</a>
