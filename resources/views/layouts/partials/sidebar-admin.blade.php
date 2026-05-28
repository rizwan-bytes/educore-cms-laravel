@php
    $cur = request()->route()?->getName() ?? '';
    $a = fn($p) => str_starts_with($cur, $p) ? 'active' : '';
@endphp

{{-- Main --}}
<a href="{{ route('admin.dashboard') }}" class="nav-item {{ $a('admin.dashboard') }}">
    <i class="fas fa-gauge"></i>
    <span>{{ __('dashboard.title') }}</span>
</a>

{{-- People --}}
<div class="nav-section">{{ __('common.people') }}</div>
<a href="{{ route('admin.users.index') }}" class="nav-item {{ $a('admin.users') }}">
    <i class="fas fa-users"></i>
    <span>{{ __('common.users') }}</span>
</a>
<a href="{{ route('admin.students.index') }}" class="nav-item {{ $a('admin.students') }}">
    <i class="fas fa-user-graduate"></i>
    <span>{{ __('students.title') }}</span>
</a>
<a href="{{ route('admin.teachers.index') }}" class="nav-item {{ $a('admin.teachers') }}">
    <i class="fas fa-chalkboard-user"></i>
    <span>{{ __('teachers.title') }}</span>
</a>
<a href="{{ route('admin.classes.index') }}" class="nav-item {{ $a('admin.classes') }}">
    <i class="fas fa-layer-group"></i>
    <span>{{ __('classes.title') }}</span>
</a>
<a href="{{ route('admin.subjects.index') }}" class="nav-item {{ $a('admin.subjects') }}">
    <i class="fas fa-book-open"></i>
    <span>{{ __('subjects.title') }}</span>
</a>

{{-- Academics --}}
<div class="nav-section">{{ __('common.academics') }}</div>
<a href="{{ route('admin.attendance.index') }}" class="nav-item {{ $a('admin.attendance') }}">
    <i class="fas fa-calendar-check"></i>
    <span>{{ __('attendance.title') }}</span>
</a>
<a href="{{ route('admin.timetable.index') }}" class="nav-item {{ $a('admin.timetable') }}">
    <i class="fas fa-table-cells"></i>
    <span>{{ __('common.timetable') }}</span>
</a>
<a href="{{ route('admin.exams.index') }}" class="nav-item {{ $a('admin.exams') }}">
    <i class="fas fa-file-pen"></i>
    <span>{{ __('exams.title') }}</span>
</a>
<a href="{{ route('admin.results.index') }}" class="nav-item {{ $a('admin.results') }}">
    <i class="fas fa-chart-bar"></i>
    <span>{{ __('common.results') }}</span>
</a>
<a href="{{ route('admin.report-card.index') }}" class="nav-item {{ $a('admin.report-card') }}">
    <i class="fas fa-id-card"></i>
    <span>{{ __('common.report_cards') }}</span>
</a>

{{-- Finance --}}
<div class="nav-section">{{ __('common.finance') }}</div>
<a href="{{ route('admin.fees.index') }}" class="nav-item {{ $a('admin.fees') }}">
    <i class="fas fa-money-bill-wave"></i>
    <span>{{ __('fees.title') }}</span>
</a>
<a href="{{ route('admin.fee-structures.index') }}" class="nav-item {{ $a('admin.fee-structures') }}">
    <i class="fas fa-layer-group"></i>
    <span>{{ __('fees.structures') }}</span>
</a>
<a href="{{ route('admin.fee-generate.index') }}" class="nav-item {{ $a('admin.fee-generate') }}">
    <i class="fas fa-wand-magic-sparkles"></i>
    <span>{{ __('fees.batch_generate') }}</span>
</a>
<a href="{{ route('admin.scholarships.index') }}" class="nav-item {{ $a('admin.scholarships') }}">
    <i class="fas fa-award"></i>
    <span>{{ __('fees.scholarships') }}</span>
</a>
<a href="{{ route('admin.fee-analytics.index') }}" class="nav-item {{ $a('admin.fee-analytics') }}">
    <i class="fas fa-chart-pie"></i>
    <span>{{ __('fees.analytics') }}</span>
</a>
<a href="{{ route('admin.fee-reports.index') }}" class="nav-item {{ $a('admin.fee-reports') }}">
    <i class="fas fa-file-csv"></i>
    <span>{{ __('fees.reports') }}</span>
</a>
<a href="{{ route('admin.fee-portal-links.index') }}" class="nav-item {{ $a('admin.fee-portal-links') }}">
    <i class="fas fa-link"></i>
    <span>{{ __('fees.parent_portals') }}</span>
</a>
<a href="{{ route('admin.payment-proofs.index') }}" class="nav-item {{ $a('admin.payment-proofs') }}">
    <i class="fas fa-images"></i>
    <span>{{ __('fees.payment_proofs') }}</span>
</a>

{{-- Diary --}}
<div class="nav-section">{{ __('common.diary') }}</div>
<a href="{{ route('admin.diary.index') }}" class="nav-item {{ $a('admin.diary') }}">
    <i class="fas fa-book"></i>
    <span>{{ __('diary.title') }}</span>
</a>
<a href="{{ route('admin.diary-qr.index') }}" class="nav-item {{ $a('admin.diary-qr') }}">
    <i class="fas fa-qrcode"></i>
    <span>{{ __('diary.qr_codes') }}</span>
</a>

{{-- Communication --}}
<div class="nav-section">{{ __('common.communication') }}</div>
<a href="{{ route('admin.notices.index') }}" class="nav-item {{ $a('admin.notices') }}">
    <i class="fas fa-bell"></i>
    <span>{{ __('notices.title') }}</span>
</a>
<a href="{{ route('admin.notifications.index') }}" class="nav-item {{ $a('admin.notifications') }}">
    <i class="fas fa-paper-plane"></i>
    <span>{{ __('common.notifications') }}</span>
</a>
<a href="{{ route('admin.syllabus.index') }}" class="nav-item {{ $a('admin.syllabus') }}">
    <i class="fas fa-book-open"></i>
    <span>{{ __('common.syllabus') }}</span>
</a>
<a href="{{ route('admin.library.index') }}" class="nav-item {{ $a('admin.library') }}">
    <i class="fas fa-book-bookmark"></i>
    <span>{{ __('common.library') }}</span>
</a>
<a href="{{ route('admin.at-risk.index') }}" class="nav-item {{ $a('admin.at-risk') }}">
    <i class="fas fa-circle-exclamation"></i>
    <span>{{ __('common.at_risk') }}</span>
</a>
<a href="{{ route('admin.transcripts.index') }}" class="nav-item {{ $a('admin.transcripts') }}">
    <i class="fas fa-id-badge"></i>
    <span>{{ __('common.transcripts') }}</span>
</a>

{{-- HR --}}
<div class="nav-section">{{ __('common.hr') }}</div>
<a href="{{ route('admin.leaves.index') }}" class="nav-item {{ $a('admin.leaves') }}">
    <i class="fas fa-calendar-minus"></i>
    <span>{{ __('common.leave_management') }}</span>
</a>
<a href="{{ route('admin.payroll.index') }}" class="nav-item {{ $a('admin.payroll') }}">
    <i class="fas fa-money-bill-wave"></i>
    <span>{{ __('common.payroll') }}</span>
</a>
<a href="{{ route('admin.ptm.index') }}" class="nav-item {{ $a('admin.ptm') }}">
    <i class="fas fa-handshake"></i>
    <span>{{ __('common.ptm') }}</span>
</a>
<a href="{{ route('admin.admissions.index') }}" class="nav-item {{ $a('admin.admissions') }}">
    <i class="fas fa-file-pen"></i>
    <span>{{ __('common.admissions') }}</span>
</a>

{{-- System --}}
<div class="nav-section">{{ __('common.system') }}</div>
<a href="{{ route('admin.profile.index') }}" class="nav-item {{ $a('admin.profile') }}">
    <i class="fas fa-circle-user"></i>
    <span>{{ __('common.my_profile') }}</span>
</a>
<a href="{{ route('admin.security.index') }}" class="nav-item {{ $a('admin.security') }}">
    <i class="fas fa-shield-halved"></i>
    <span>{{ __('common.security') }}</span>
</a>
<a href="{{ route('admin.hec-reports.index') }}" class="nav-item {{ $a('admin.hec-reports') }}">
    <i class="fas fa-file-contract"></i>
    <span>{{ __('common.hec_reports') }}</span>
</a>
<a href="{{ route('admin.settings.index') }}" class="nav-item {{ $a('admin.settings') }}">
    <i class="fas fa-gear"></i>
    <span>{{ __('settings.title') }}</span>
</a>
