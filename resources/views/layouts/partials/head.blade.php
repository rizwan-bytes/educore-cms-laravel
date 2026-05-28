<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', __('common.app_name')) — {{ \App\Services\SettingService::get('college_name', 'EduCore') }}</title>

{{-- Noto Nastaliq Urdu (Google Fonts — CDN OK for fonts, RTL only) --}}
<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap" rel="stylesheet">
{{-- Inter font loaded locally via style.css @import --}}

{{-- Local Vendor CSS --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/datatables/dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/datatables/buttons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.css') }}">

{{-- Custom CSS --}}
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

{{-- Chart.js — must be in head --}}
<script src="{{ asset('assets/vendor/chartjs/chart.umd.min.js') }}"></script>

{{-- Dynamic theme colors (if customized per tenant) --}}
@php
    $primaryColor = \App\Services\SettingService::get('primary_color', '#6366f1');
    $cyanColor    = \App\Services\SettingService::get('secondary_color', '#06b6d4');
    $useCustom    = ($primaryColor !== '#6366f1' || $cyanColor !== '#06b6d4');
@endphp
@if($useCustom)
<style>
    :root {
        --primary: {{ $primaryColor }};
        --cyan: {{ $cyanColor }};
    }
</style>
@endif

@stack('styles')
