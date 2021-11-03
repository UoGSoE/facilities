@isset($error)
<div class="alert alert-danger" role="alert">
   {{ $error }}
</div>
@endisset
@isset($success)
<div class="alert alert-success" role="alert">
   {{ $success }}
</div>
@endisset
