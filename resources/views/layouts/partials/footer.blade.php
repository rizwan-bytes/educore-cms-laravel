{{-- Local Vendor JS --}}
<script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/vendor/axios/axios.min.js') }}"></script>

{{-- Custom JS --}}
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

{{-- Language switcher --}}
<script>
function switchLang(locale) {
    axios.post('{{ route("locale.switch") }}', { locale: locale })
         .then(() => location.reload())
         .catch(() => location.reload());
}
</script>

{{-- Mobile Bottom Nav --}}
@auth
    @include('layouts.partials.mobile-nav')
@endauth

@stack('scripts')
