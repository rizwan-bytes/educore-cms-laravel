<div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <div class="topbar-breadcrumb">
        <span>{{ ucfirst(auth()->user()->role ?? '') }}</span>
        <i class="fas fa-chevron-right sep"></i>
        <span class="current">@yield('title', __('dashboard.title'))</span>
    </div>

    <div class="topbar-actions">

        {{-- Language Toggle --}}
        <div class="lang-toggle" style="display:flex;gap:2px">
            <button onclick="switchLang('en')"
                    class="topbar-chip {{ app()->getLocale() === 'en' ? 'lang-active' : '' }}"
                    style="cursor:pointer;border:none;{{ app()->getLocale() === 'en' ? 'background:var(--primary-glow);border-color:rgba(99,102,241,.3);color:var(--primary-lt);' : '' }}">
                EN
            </button>
            <button onclick="switchLang('ur')"
                    class="topbar-chip {{ app()->getLocale() === 'ur' ? 'lang-active' : '' }}"
                    style="cursor:pointer;border:none;{{ app()->getLocale() === 'ur' ? 'background:var(--primary-glow);border-color:rgba(99,102,241,.3);color:var(--primary-lt);' : '' }}">
                اردو
            </button>
        </div>

        {{-- Date chip --}}
        <div class="topbar-chip">
            <i class="fas fa-calendar-days"></i>
            {{ now()->format('d M Y') }}
        </div>

        {{-- Bell notification (admin only) --}}
        @auth
        @if(auth()->user()->isAdmin())
        <div style="position:relative" id="bellWrap">
            <button id="bellBtn" onclick="toggleBell(event)"
                    class="topbar-icon-btn" title="{{ __('common.notifications') }}">
                <i class="fas fa-bell"></i>
                {{-- Dot shown when there are unread notifs (Phase 2+) --}}
                {{-- <span class="dot"></span> --}}
            </button>

            <div id="bellDropdown"
                 style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:270px;
                        background:var(--card);border:1px solid var(--border);border-radius:12px;
                        box-shadow:0 8px 32px rgba(0,0,0,.5);z-index:9999;overflow:hidden">
                <div style="padding:.7rem 1rem;border-bottom:1px solid var(--border);font-size:.72rem;
                            font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.08em">
                    {{ __('common.notifications') }}
                </div>
                <div style="padding:1.5rem 1rem;text-align:center;color:var(--text-2);font-size:.83rem">
                    <i class="fas fa-check-circle" style="color:#10b981;font-size:1.4rem;display:block;margin-bottom:.5rem"></i>
                    All caught up!
                </div>
            </div>
        </div>
        @endif
        @endauth

        {{-- Logout icon --}}
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="topbar-icon-btn" title="{{ __('common.logout') }}">
                <i class="fas fa-right-from-bracket"></i>
            </button>
        </form>

    </div>
</div>

<script>
function toggleBell(e) {
    e.stopPropagation();
    var d = document.getElementById('bellDropdown');
    if (d) d.style.display = d.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('bellWrap');
    var drop = document.getElementById('bellDropdown');
    if (wrap && drop && !wrap.contains(e.target)) {
        drop.style.display = 'none';
    }
});
</script>
