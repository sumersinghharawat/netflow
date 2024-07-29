@extends('layouts.app')
@section('title', 'Epin-Settings')

@section('content')
    <div class="container-fluid settings_page">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.advancedSettings.inc.links')
                <div class="">
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        <h4>
                            <i class="fas fa-project-diagram"></i> {{ __('settings.epin_configuration') }}
                        </h4>
                        <p class="text-justify mt-lg-2">
                            {{ __('settings.epin_configure_set') }}
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                </div>

            </div>
            <div class="card-body">
                <div class="col-md-3">
                    <form action="{{ route('pinconfig.update', $config->id) }}" method="post">
                        @csrf
                        <label>{{ __('settings.epin_character_set') }} <span class="text-danger">*</span></label>
                        <div class="form-group d-flex" style="gap:10px">

                            <select class="form-select " name="character_set" id="pin_character">
                                <option value="alphabet" {{ $config->character_set == 'alphabet' ? 'selected' : '' }}>
                                    {{ __('settings.alphabets') }}
                                </option>
                                <option value="numeral" {{ $config->character_set == 'numeral' ? 'selected' : '' }}>
                                    {{ __('settings.numerals') }}</option>
                                <option value="alphanumeric"
                                    {{ $config->character_set == 'alphanumeric' ? 'selected' : '' }}>
                                    {{ __('settings.alphanumerals') }}</option>
                            </select>
                            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                        </div>

                    </form>
                </div>
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">{{ __('settings.add_new_epin_amount') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('pinNumber.store') }}" method="post">
                        @csrf
                        <label for="epin">{{ __('settings.epin_amount') }} <span class="text-danger">*</span></label>
                        <div class="form-group  d-flex" style="gap:10px">
                            <div class="input-group">
                                <div class="input-group-text">$</div>
                                <input type="number" name="amount" id="epin"
                                    class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}">
                            </div>
                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <button type="submit" class="btn btn-primary">{{ __('settings.add') }} </button>
                        </div>

                    </form>
                </div>
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">{{ __('settings.available_epin_amounts') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-1">
                    <div class="card-body-1">
                        <form action="{{ route('pinNumber.destroy') }}" method="post">
                            @csrf
                            <div class="table-responsive">
                                <table class="table mb-0 settings_table">

                                    <thead>
                                        <tr>
                                            <th width="80px">
                                                <input class="form-check-input" type="checkbox" id="checkAll">
                                            </th>
                                            <th>{{ __('settings.epin_amount') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pinAmount as $amount)
                                            <tr>
                                                <td>
                                                    <input class="form-check-input" type="checkbox" name="amount[]"
                                                        value="{{ $amount->id }}">
                                                </td>
                                                <td class="fw-bolder">{{ number_format($amount->amount, 2) }}</td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td>
                                                    <div class="nodata_view">
                                                        <img src="{{ asset('assets/images/nodata-icon.png') }}"
                                                            alt="">
                                                        <span>{{ __('common.no_data') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-danger">{{ __('common.delete') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endsection
        @push('scripts')
            <script>
                $('#checkAll').on('click', function() {
                    $('input:checkbox').not(this).prop('checked', this.checked);
                })
            </script>
        @endpush
