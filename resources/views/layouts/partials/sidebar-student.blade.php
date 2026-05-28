@php
    $cur = request()->route()?->getName() ?? '';
    $a = fn($p) => str_starts_with($cur, $p) ? 'active' : '';
@endphp

<a href="{{ route('student.dashboard') }}" class="nav-item {{ $a('student.dashboard') }}">
    <i class="fas fa-gauge"></i>
    <span>{{ __('dashboard.title') }}</span>
</a>

<div class="nav-section">{{ __('common.academics') }}</div>
<a href="{{ route('student.attendance.index') }}" class="nav-item {{ $a('student.attendance') }}">
    <i class="fas fa-calendar-check"></i>
    <span>{{ __('attendance.title') }}</span>
</a>
<a href="{{ route('student.timetable.index') }}" class="nav-item {{ $a('student.timetable') }}">
    <i class="fas fa-table-cells"></i>
    <span>{{ __('common.timetable') }}</span>
</a>
<a href="{{ route('student.results.index') }}" class="nav-item {{ $a('student.results') }}">
    <i class="fas fa-chart-bar"></i>
    <span>{{ __('exams.results') }}</span>
</a>
<a href="{{ route('student.report-card.index') }}" class="nav-item {{ $a('student.report-card') }}">
    <i class="fas fa-id-card"></i>
    <span>{{ __('common.report_card') }}</span>
</a>
<a href="{{ route('student.syllabus.index') }}" class="nav-item {{ $a('student.syllabus') }}">
    <i class="fas fa-book-open"></i>
    <span>{{ __('common.syllabus') }}</span>
</a>
<a href="{{ route('student.library.index') }}" class="nav-item {{ $a('student.library') }}">
    <i class="fas fa-book-bookmark"></i>
    <span>{{ __('common.library') }}</span>
</a>
<a href="{{ route('student.transcript.index') }}" class="nav-item {{ $a('student.transcript') }}">
    <i class="fas fa-id-badge"></i>
    <span>{{ __('common.transcript') }}</span>
</a>

<div class="nav-section">{{ __('common.finance') }}</div>
<a href="{{ route('student.fees.index') }}" class="nav-item {{ $a('student.fees') }}">
    <i class="fas fa-money-bill-wave"></i>
    <span>{{ __('fees.title') }}</span>
</a>

<div class="nav-section">{{ __('common.diary') }}</div>
<a href="{{ route('student.diary.index') }}" class="nav-item {{ $a('student.diary') }}">
    <i class="fas fa-book"></i>
    <span>{{ __('diary.title') }}</span>
</a>

<div class="nav-section">{{ __('common.communication') }}</div>
<a href="{{ route('student.notices.index') }}" class="nav-item {{ $a('student.notices') }}">
    <i class="fas fa-bell"></i>
    <span>{{ __('notices.title') }}</span>
</a>

<div class="nav-section">{{ __('common.account') }}</div>
<a href="{{ route('student.profile.index') }}" class="nav-item {{ $a('student.profile') }}">
    <i class="fas fa-circle-user"></i>
    <span>{{ __('common.my_profile') }}</span>
</a>
