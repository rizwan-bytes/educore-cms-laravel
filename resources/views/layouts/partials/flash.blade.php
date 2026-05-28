@if(session('success'))
<div class="alert-flash alert-flash-success" id="flashMsg">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="flash-close"><i class="fas fa-times"></i></button>
</div>
@endif

@if(session('error'))
<div class="alert-flash alert-flash-danger" id="flashMsg">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" class="flash-close"><i class="fas fa-times"></i></button>
</div>
@endif

@if(session('warning'))
<div class="alert-flash alert-flash-warning" id="flashMsg">
    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
    <button onclick="this.parentElement.remove()" class="flash-close"><i class="fas fa-times"></i></button>
</div>
@endif

@if(session('info'))
<div class="alert-flash alert-flash-info" id="flashMsg">
    <i class="fas fa-info-circle"></i> {{ session('info') }}
    <button onclick="this.parentElement.remove()" class="flash-close"><i class="fas fa-times"></i></button>
</div>
@endif
