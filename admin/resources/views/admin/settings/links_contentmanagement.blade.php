<div class="d-inline">
    <a href="{{ route('welcome.letter') }}" class="nav-item @if (Route::currentRouteName() == route('welcome.letter')) 'active' @else'' @endif text-decoration-none p-2">{{ __('common.welcome_letter') }}</a>
    <a href="{{ route('terms.conditions') }}"
        class="nav-item @if (Route::currentRouteName() == route('terms.conditions')) 'active'
   @else
    '' @endif text-decoration-none p-2">{{ __('common.terms_and_conditions') }}</a>

    <a href="{{ route('replication.site') }}" class="nav-item text-decoration-none p-2">{{ __('replica.replica_site') }}</a>

</div>

@if ($errors->any())
    <div class="alert alert-danger mt-5">
        <p><strong>Opps Something went wrong</strong></p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success mt-5">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger mt-5">{{ session('error') }}</div>
@endif
