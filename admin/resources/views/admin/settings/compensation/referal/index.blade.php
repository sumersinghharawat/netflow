@extends('layouts.app')
@section('title', __('compensation.referral_commission'))
@section('content')
    <div class="container-fluid ">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('settings.referral_commission') }}</h4>
        </div>

        <div class="card mt-1">
            <div class="card-body">
                @include('admin.settings.inc.links')
                <form action="{{ route('referralcommission.update') }}" method="post" class="mt-3">
                    @csrf
                    @if ($data['module_status']->referral_status)
                        @if ($data['module_status']->rank_status || $data['module_status']->product_status)
                            <div class="form-group">
                                <label class="required control-label">{{ __('compensation.type_of_commission') }}</label>
                                <select class="form-select" name="referral_commission_type" id="referral_commission_type"
                                    onchange="percentFlat(this)">
                                    @if ($data['configuration']->sponsor_commission_type != 'rank')
                                        <option value="percentage"
                                            {{ $data['configuration']->referral_commission_type == 'percentage' ? 'selected' : '' }}>
                                            {{ __('common.percentage') }}</option>
                                    @endif
                                    <option value="flat"
                                        {{ $data['configuration']->referral_commission_type == 'flat' ? 'selected' : '' }}>
                                        {{ __('common.flat') }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="required">{{ __('settings.referral_commission_criteria') }}</label>
                                <select class="form-select" name="sponsor_commission_type" id="sponsor_commission_type">
                                    @if ($data['module_status']->product_status || $data['module_status']->ecom_status)
                                        <option value="joinee_package"
                                            {{ $data['configuration']->sponsor_commission_type == 'joinee_package' ? 'selected' : '' }}>
                                            {{ __('settings.based_on_join_pack') }}</option>
                                        <option value="sponsor_package"
                                            {{ $data['configuration']->sponsor_commission_type == 'sponsor_package' ? 'selected' : '' }}>
                                            {{ __('settings.based_on_sponsor_pack') }}</option>
                                    @endif
                                    @if ($data['module_status']->rank_status && $data['configuration']->referral_commission_type == 'flat')
                                        <option value="rank"
                                            {{ $data['configuration']->sponsor_commission_type == 'rank' ? 'selected' : '' }}>
                                            {{ __('settings.based_on_sponsor_rank') }}</option>
                                    @endif
                                </select>
                            </div>
                            <div id="referral_rank_div">
                            </div>
                        @else
                            <div class="form-group">
                                <label class="required">
                                    {{ __('settings.referral_commission') . ' %' }}
                                </label>
                                <div class="input-group">
                                    <input type="text" maxlength="5"
                                        class="form-control @error('referral_amount') is-invalid @enderror"
                                        name="referral_amount" id="referral_amount" min="0"
                                        value="{{ $configuration->referral_amount }}">
                                    @error('referral_amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">{{ __('common.update') }}</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>



@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('ajax.referral') }}",
                type: 'get',
                status: true,
                success: function(response) {
                    $("#referral_rank_div").html("");
                    $("#referral_rank_div").html(response.data);
                },
                error(err) {
                    console.log(`error${err}`)
                }
            });
        })
        $(document).on('change', '#sponsor_commission_type', function() {
            let url;
            let commissionCriteria = $('#sponsor_commission_type').val();

            if (commissionCriteria == "rank") {
                url = "{{ route('ajax.referralRank') }}";
            } else if (commissionCriteria == "joinee_package" || commissionCriteria == "sponsor_package") {
                url = "{{ route('ajax.levelcommission') }}";
            } else {
                return;
            }

            getCritriaData(url, commissionCriteria);

        });
        getCritriaData = (url, commissionCriteria) => {
            $.ajax({
                url: url,
                type: 'get',
                status: true,
                success: function(response) {
                    $("#referral_rank_div").html("");
                    $("#referral_rank_div").html(response.data);
                    if (commissionCriteria == "rank") {
                        $('#referral_commission_type').html(`<option value="flat">Flat</option>`);
                    }
                },
                error(err) {
                    console.log(`error${err}`)
                }
            });
        }
        const percentFlat = (option) => {
            let val = option.value;
            if (val == 'flat') {
                $('.percent_label').addClass('d-none');
            } else {
                $('.percent_label').removeClass('d-none');
            }
        }
    </script>
@endpush
