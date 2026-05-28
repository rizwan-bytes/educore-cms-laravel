<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ur' ? 'rtl' : 'ltr' }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="{{ app()->getLocale() === 'ur' ? 'rtl' : 'ltr' }} @yield('body-class')">
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app-wrapper">
    @include('layouts.partials.sidebar')

    <div class="main-content">
        @include('layouts.partials.topbar')

        <div class="page-body">
            @include('layouts.partials.flash')
            @yield('content')
        </div>

        @include('layouts.partials.footer')
    </div>
</div>

</body>
</html>
