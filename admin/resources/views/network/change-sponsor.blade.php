@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __("tree.change_sponsor") }}</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('change.sponsor.store') }}" class="row gy-2 gx-3 align-items-center" method="POST">
                @csrf
                <div class="col-md-3">
                    @if(request()->old('user'))
                        @php
                        $user = App\Models\User::find(old('user'));
                        @endphp
                    @endif
                    @if(request()->old('new_sponsor'))
                        @php
                        $newSponsor = App\Models\User::find(old('new_sponsor'));
                        @endphp
                    @endif
                    <div>
                        <label class="form-label">{{ __('tree.select_username') }}</label>
                        <select name="user" class="form-control treeview_frm_input select2-search-user @error('user') is-invalid @enderror">
                            <option selected value="{{ old('user') }}">@isset($user) {{ $user->username }} @endisset</option>
                        </select>
                        @error('user')
                        <div class="invalid-feedback">
                            {{ __($message) }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div>
                        <label class="form-label">{{ __('tree.select_new_sponsor') }}</label>
                        <select name="new_sponsor" class="form-control treeview_frm_input select2-search-user @error('new_sponsor') is-invalid @enderror">
                            <option selected value="{{ old('new_sponsor') }}">@isset($newSponsor) {{ $newSponsor->username }} @endisset</option>
                        </select>
                        @error('new_sponsor')
                            <div class="invalid-feedback">
                                {{ __($message) }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-md">{{ __('tree.change') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $( () =>{
            getUsers();
        });
    </script>
@endpush
