@extends('layouts.app')
@section('title', trans('settings.compensation_settings'))
@section('content')
    <div class="container-fluid settings_page ">
        <div class="card">
            <div class="card-header ">
                @include('admin.settings.inc.links')
                <div class="">
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">

                        <h4><i class="bx bx-calculator  me-2"></i>{{ __('settings.compensation_settings') }}</h4>
                        <p class="text-justify mt-lg-2">
                            {{ __('settings.compensation_description') }}
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                </div>
            </div>
            <div class="card-body settings_cnt_dv">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table  mb-0">
                                <thead>
                                    <tr>

                                        <th>{{ __('settings.types_of_compensations') }}</th>
                                        <th> {{ __('common.status') }} </th>
                                        <th> {{ __('common.configuration') }} </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @if ($moduleStatus->mlm_plan == 'Binary')
                                        <tr>
                                            <td>
                                                {{ __('settings.binary_commission') }}
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox" id="SwitchCheckSizelg"
                                                        name="plan_commission"
                                                        {{ $compensation->plan_commission ? 'checked' : '' }} value="1"
                                                        onchange="submitSingle(this)">
                                                </div>
                                            </td>
                                            <td>
                                                @if ($compensation->plan_commission)
                                                    <a href="{{ route('binaryConfig') }}" class="plan_commission"
                                                        title="click to check config"><i class="fa fa-cog"
                                                            aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @elseif($moduleStatus->mlm_plan == 'Board' && $moduleStatus->table_status)
                                        <tr>
                                            <td>
                                                {{ __('settings.table_commission') }}
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox" id="SwitchCheckSizelg"
                                                        name="plan_commission"
                                                        {{ $compensation->plan_commission ? 'checked' : '' }} value="1"
                                                        onchange="submitSingle(this)">
                                                </div>
                                            </td>
                                            <td>
                                                @if ($compensation['plan_commission'])
                                                    <a href="{{ route('binaryConfig') }}" id="plan_commission"
                                                        title="click to check config"><i class="fa fa-cog"
                                                            aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @elseif($moduleStatus->mlm_plan == 'Board' && !$moduleStatus->table_status)
                                        <tr>
                                            <td>
                                                {{ __('srttings.board_commission') }}
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox" id="SwitchCheckSizelg"
                                                        name="plan_commission"
                                                        {{ $compensation->plan_commission ? 'checked' : '' }}
                                                        value="1" onchange="submitSingle(this)">
                                                </div>
                                            </td>
                                            <td>
                                                @if ($compensation->plan_commission)
                                                    <a href="{{ route('binaryConfig') }}" class="plan_commission"
                                                        title="click to check config"><i class="fa fa-cog"
                                                            aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif

                                    @if (($moduleStatus->mlm_plan == 'Party' && $moduleStatus->sponser_commission_status) ||
                                        ($moduleStatus->mlm_plan != 'Monoline' && $moduleStatus->mlm_plan != 'Party'))
                                        <tr>

                                            <td>
                                                @if ($moduleStatus->xup_status)
                                                    {{ __('settings.xup_commission') }}
                                                @else
                                                    {{ __('settings.level_commission') }}
                                                @endif

                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox" id="SwitchCheckSizelg"
                                                        name="sponsor_commission"
                                                        {{ $compensation->sponsor_commission ? 'checked' : '' }}
                                                        onchange="submitSingle(this)">
                                                </div>

                                            </td>
                                            <td>
                                                @if ($compensation->sponsor_commission)
                                                    <a href="{{ route('levelcommission') }}" class="sponsor_commission"
                                                        title="click here to check level commision"> <i class="fa fa-cog"
                                                            aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif

                                    @if ($addonCommissions->contains('rank-commission') && $moduleStatus->rank_status)
                                        <tr>
                                            <td>
                                                {{ __('settings.rank_commission') }}
                                                @if ($moduleStatus->basic_demo_status)
                                                    <span
                                                        class="badge bg-danger float-end">{{ __('common.addon_module') }}</span>
                                                @endif

                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox" id="SwitchCheckSizelg"
                                                        name="rank_commission"
                                                        {{ $compensation->rank_commission ? 'checked' : '' }}
                                                        onchange="submitSingle(this)">

                                                </div>

                                            </td>

                                            <td>
                                                @if ($compensation->rank_commission)
                                                    <a href="{{ route('rank') }}" class="rank_commission"
                                                        title="click here to check rank commision"> <i class="fa fa-cog"
                                                            aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif


                                    @if ($moduleStatus->roi_status)
                                        <tr>
                                            <td>
                                                {{ __('settings.roi_commission') }}
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox" id="SwitchCheckSizelg"
                                                        name="roi_commission"
                                                        {{ $compensation->roi_commission ? 'checked' : '' }}
                                                        onchange="submitSingle(this)">

                                                </div>

                                            </td>
                                            <td>
                                                @if ($compensation->roi_commission)
                                                    <a href="{{ route('roicommission') }}" class="roi_commission">
                                                        <i class="fa fa-cog" aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>{{ __('settings.referral_commission') }}</td>
                                        <td>
                                            <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                <input class="form-check-input" type="checkbox" id="SwitchCheckSizelg"
                                                    name="referral_commission"
                                                    {{ $compensation->referral_commission ? 'checked' : '' }}
                                                    onchange="submitSingle(this)">

                                            </div>

                                        </td>
                                        <td>
                                            @if ($compensation->referral_commission)
                                                <a href="{{ route('referralcommission') }}" class="referral_commission">
                                                    <i class="fa fa-cog" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>

                                    @if ($addonCommissions->contains('matching-bonus'))
                                        <tr>
                                            <td>
                                                {{ __('settings.matching_bonus') }}
                                                @if ($moduleStatus->basic_demo_status)
                                                    <span
                                                        class="badge bg-danger float-end">{{ __('common.addon_module') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="SwitchCheckSizelg" value="1" name="matching_bonus"
                                                        {{ $compensation->matching_bonus ? 'checked' : '' }}
                                                        onchange="submitSingle(this)">

                                                </div>

                                            </td>
                                            <td>
                                                @if ($compensation->matching_bonus)
                                                    <a href="{{ route('matching_bonus') }}" class="matching_bonus"
                                                        title="click to check Matching Bonus">
                                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($addonCommissions->contains('pool-bonus') && $moduleStatus->rank_status)
                                        <tr>
                                            <td>
                                                {{ __('settings.pool_bonus') }}
                                                @if ($moduleStatus->basic_demo_status)
                                                    <span
                                                        class="badge bg-danger float-end">{{ __('common.addon_module') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="SwitchCheckSizelg" value="1" name="pool_bonus"
                                                        {{ $compensation->pool_bonus ? 'checked' : '' }}
                                                        onchange="submitSingle(this)">

                                                </div>

                                            </td>
                                            <td>
                                                @if ($compensation->pool_bonus)
                                                    <a href="{{ route('poolbonus') }}" class="pool_bonus">
                                                        <i class="fa fa-cog" aria-hidden="true"></i></a>
                                                @else
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($addonCommissions->contains('fast-start-bonus'))
                                        <tr>
                                            <td>
                                                {{ __('settings.fast_start_bonus') }}
                                                @if ($moduleStatus->basic_demo_status)
                                                    <span
                                                        class="badge bg-danger float-end">{{ __('common.addon_module') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="SwitchCheckSizelg" value="1" name="fast_start_bonus"
                                                        {{ $compensation->fast_start_bonus ? 'checked' : '' }}
                                                        onchange="submitSingle(this)">
                                                </div>
                                            </td>
                                            <td>
                                                @if ($compensation->fast_start_bonus)
                                                    <a href="{{ route('faststartbonus') }}" class="fast_start_bonus">
                                                        <i class="fa fa-cog" aria-hidden="true"></i></a>
                                                @else
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($addonCommissions->contains('performance-bonus'))
                                        <tr>

                                            <td>
                                                {{ __('settings.performance_bonus') }}
                                                @if ($moduleStatus->basic_demo_status)
                                                    <span
                                                        class="badge bg-danger float-end">{{ __('common.addon_module') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="SwitchCheckSizelg" value="1" name="performance_bonus"
                                                        {{ $compensation->performance_bonus == '1' ? 'checked' : '' }}
                                                        onchange="submitSingle(this)">

                                                </div>

                                            </td>
                                            <td>
                                                @if ($compensation->performance_bonus)
                                                    <a href="{{ route('performance_bonus') }}" class="performance_bonus"
                                                        title="click to check Performance Bonus"> <i class="fa fa-cog"
                                                            aria-hidden="true"></i></a>
                                                @else
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($addonCommissions->contains('repurchase-sales-commission') &&
                                        ($moduleStatus->repurchase_status || $moduleStatus->ecom_status))
                                        <tr>
                                            <td>
                                                {{ __('settings.repurchase_sales_commission') }}
                                                @if ($moduleStatus->basic_demo_status)
                                                    <span
                                                        class="badge bg-danger float-end">{{ __('common.addon_module') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox" id="sales_Commission"
                                                        value="1" name="sales_Commission"
                                                        {{ $compensation->sales_Commission ? 'checked' : '' }}
                                                        onchange="submitSingle(this)">
                                                </div>

                                            </td>
                                            <td>
                                                @if ($compensation->sales_Commission)
                                                    <a href="{{ route('salescommission') }}" class="sales_Commission"
                                                        title="click to check Performance Bonus"> <i class="fa fa-cog"
                                                            aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($addonCommissions->contains('reentry-bonus') && $moduleStatus->mlm_plan == 'Monoline')
                                        <tr>
                                            <td>
                                                {{ __('settings.reentry_commission') }}
                                                @if ($moduleStatus->basic_demo_status)
                                                    <span
                                                        class="badge bg-danger float-end">{{ __('common.addon_module') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox" id="plan_Commission"
                                                        value="1" name="plan_Commission"
                                                        {{ $compensation->plan_commission ? 'checked' : '' }}
                                                        onchange="submitSingle(this)">
                                                </div>

                                            </td>
                                            <td>
                                                @if ($compensation->plan_commission)
                                                    <a href="{{ route('monoline.index') }}" class="plan_Commission"
                                                        title="click to check Reentry commission"> <i class="fa fa-cog"
                                                            aria-hidden="true"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>




    </div>
@endsection
@push('scripts')
    <script>
        const submitSingle = async (evnt) => {
            let name = $(evnt).attr('name');
            let status = 0;
            if (evnt.checked) {
                status = 1;
            }
            let url = "{{ route('compensation.update') }}"
            let data = {
                'value': status,
                name
            }
            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    }
                })
            if (typeof(res) != 'undefined') {
                if (res.value == 1) {
                    $('.' + name).removeClass('d-none');
                } else {
                    $('.' + name).addClass('d-none');
                }

                notifySuccess(res.data)
            }
        }
    </script>
@endpush
