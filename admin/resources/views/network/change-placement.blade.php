@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __("tree.change_placement") }}</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('change.placement.store') }}" class="row gy-2 gx-3 align-items-center" method="POST">
                @csrf
                <div class="col-md-3">
                    @if(request()->old('user'))
                        @php
                        $user = App\Models\User::find(old('user'));
                        @endphp
                    @endif
                    @if(request()->old('placement'))
                        @php
                        $placement = App\Models\User::find(old('placement'));
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
                        <label class="form-label">{{ __('tree.select_new_placement') }}</label>
                        <select name="placement" class="form-control treeview_frm_input select2-search-user @error('placement') is-invalid @enderror">
                            <option selected value="{{ old('placement') }}">@isset($placement) {{ $placement->username }} @endisset</option>
                        </select>
                        @error('placement')
                            <div class="invalid-feedback">
                                {{ __($message) }}
                            </div>
                        @enderror
                    </div>
                </div>
                @if ($moduleStatus->mlm_plan == "Binary")
                    <div class="col-md-3">
                        <div>
                            <label class="form-label">{{ __('tree.select_new_position') }}</label>
                            <select name="position" id="" class="form-control @error('position') is-invalid @enderror">
                                <option value="L">Left</option>
                                <option value="R">Right</option>
                            </select>
                            @error('position')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                @endif
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