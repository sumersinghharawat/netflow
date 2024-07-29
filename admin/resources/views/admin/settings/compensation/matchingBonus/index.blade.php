@extends('layouts.app')
@section('title', __('compensation.matching_bonus'))
@section('content')
    <div class="container-flluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('settings.matching_bonus') }}</h4>
        </div>

        <div class="card">
            <div class="card-body">
        @include('admin.settings.inc.links')

        <form action="{{ route('matchingbonus.config.update') }}" method="post"
            class="mt-2">
            @csrf
            <div class="form-group">
                <label class="required control-label">{{ __('compensation.matching_bonus_criteria') }}</label>
                <select class="form-select" name="matching_criteria" id="commission_criteria">
                    <option value="genealogy"
                        {{ $configuration->matching_criteria == 'genealogy' ? 'selected' : '' }}>
                        {{__('compensation.matiching_bonus_based_on_genealogy')}}</option>
                    @if ($moduleStatus->product_status)
                        <option {{ $configuration->matching_criteria == 'member_pck' ? 'selected' : '' }}
                            value="member_pck">{{__('compensation.matiching_bonus_based_on_member_pck')}}</option>
                    @endif
                </select>

            </div>
            <div class="form-group">
                <label class="required control-label">{{ __('compensation.matching_bonus_upto_level') }}</label>
                <input type="number" class="form-control @error('matching_upto_level') is-invalid @enderror"
                min="0" name="matching_upto_level" id="commission_upto_level"
                    value={{ $configuration->matching_upto_level }}>
                @error('matching_upto_level')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" value="Update" name="matching_commission_common"
                    id="matching_commission">{{ __('common.update') }}</button>
            </div>
        </form>

        @if ($configuration->matching_criteria == 'genealogy')
            <form action="{{ route('matching.bonus.commission.update') }}" method="post" class="mt-5">
                @csrf
                <legend>
                    <span class="fieldset-legend">
                        {{ __('compensation.matiching_bonus_based_on_genealogy') }}
                    </span>
                </legend>
                @for ($i = 1; $i <= $configuration->matching_upto_level; $i++)
                    @php
                        $commission = $matchingGenealogyCommission->where('level_no', $i)->first();
                    @endphp
                    <div class="form-group">
                        <label class="required">
                            {{ __('compensation.level') }} {{ $i }} {{__('settings.bonus')}}
                            <span class="span_level_commission">%</span>
                        </label>
                        <div class="form-group">
                            <input type="number" maxlength="5" class="level_percentage form-control"
                                name="level_percentage[{{ $i }}]" min="0"
                                value="{{ ($commission) ? $commission->level_percentage : 0 }} ">
                            @error('level_percentage.' . $i)
                                <span class="text-danger form-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endfor
                <div class="form-group">
                    <button class="btn  btn-primary" type="submit" value="Update"
                        id="matching_commission">{{ __('common.update') }}</button>
                </div>

            </form>
        @endif

        @if ($configuration->matching_criteria == 'member_pck')
            <form action="{{ route('matching.bonus.commission.update') }}" method="post">
                @csrf
                <legend>
                    <span class="fieldset-legend">
                        {{ __('compensation.matiching_bonus_based_on_member_pck') }}
                    </span>
                </legend>

                @if ($moduleStatus->product_status || ($moduleStatus->ecom_status && $moduleStatus->ecom_demo_status && $configuration->matching_criteria == 'member_pck'))
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('compensation.level') }}</th>
                                    @foreach ($packages as $package)
                                        <th>{{ $package->name . ' ' . '%' }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= $configuration->matching_upto_level; $i++)
                                    <tr>
                                        <td>
                                            {{ __('compensation.level').' '. $i }}
                                        </td>
                                        @forelse ($packages as $pck)
                                            <td>
                                                @php
                                                    $commission  = $matchingPackageCommission->where('level', $i)->where('package_id', $pck->id)->first();
                                                @endphp
                                                <input type="number"
                                                        name="commission[{{ $pck->id }}][{{ $i }}]"
                                                        class="form-control"
                                                        value="{{ ($commission) ? $commission->cmsn_member_pck : 0 }}"
                                                >
                                            </td>
                                        @empty
                                            <td colspan="">
                                                {{__('common.no_package')}}
                                            </td>
                                        @endforelse
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                    </div>
                @endif
            </form>
        @endif
    </div>
    </div>
    </div>

@endsection
