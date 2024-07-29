    @extends('layouts.app')
    @section('title', __('compensation.level_commission'))
    @section('content')
        <div class="container-fluid">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('settings.level_commission') }}</h4>
            </div>
            <div class="card ">
                <div class="card-body">
                    @include('admin.settings.inc.links')
                    <form action="{{ route('levelcommission.updateConfig') }}" method="post" class="mt-3">
                        @csrf
                        @if ($data['moduleStatus']->xup_status)
                            <div class="form-group">
                                <label class="required">{{ __('settings.xup_level') }}</label>
                                <input class="form-control @error('xup_level') is-invalid @enderror" type="text"
                                    name="xup_level" id="xup_level" value="{{ $data['configuration']->xup_level }}"
                                    autocomplete="off">
                                @error('xup_level')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="required control-label">{{ __('settings.type_of_commission') }}</label>
                            <select class="form-select" name="level_commission_type" id="level_commission_type">
                                <option value="percentage"
                                    {{ $data['configuration']->level_commission_type == 'percentage' ? 'selected' : '' }}>
                                    {{ __('common.percentage') }}</option>
                                <option value="flat"
                                    {{ $data['configuration']->level_commission_type == 'flat' ? 'selected' : '' }}>
                                    {{ __('common.flat') }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="required control-label">{{ __('settings.commission_criteria') }}</label>
                            <select class="form-select" name="level_commission_criteria" id="level_commission_criteria">
                                <option value="genealogy"
                                    {{ $data['configuration']->commission_criteria == 'genealogy' ? 'selected' : '' }}>
                                    {{ __('settings.commission_based_on_sponsor_level') }}</option>
                                @if ($data['moduleStatus']->mlm_plan == 'Matrix' ||
                                    $data['moduleStatus']->mlm_plan == 'Unilevel' ||
                                    ($data['moduleStatus']->sponsor_commission_status && $data['moduleStatus']->product_status))
                                    @if (!in_array($data['moduleStatus']->mlm_plan, ['Donation', 'Party']))
                                        <option value="reg_pck"
                                            {{ $data['configuration']->commission_criteria == 'reg_pck' ? 'selected' : '' }}>
                                            {{ __('settings.commission_based_on_reg_pack') }}</option>
                                        <option value="member_pck"
                                            {{ $data['configuration']->commission_criteria == 'member_pck' ? 'selected' : '' }}>
                                            {{ __('settings.commission_based_on_member_pck') }}</option>
                                    @endif
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="required control-label">{{ __('settings.level_commission_upto_level') }}</label>
                            <input type="text" maxlength="5"
                                class="form-control @error('commission_upto_level') is-invalid @enderror "
                                name="commission_upto_level" id="commission_upto_level" min="0"
                                value="{{ $data['configuration']->commission_upto_level }}">
                            @error('commission_upto_level')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary text-white">{{ __('common.update') }}</button>
                        </div>
                    </form>
                    @if (($data['mlm_plan'] == 'Matrix' ||
                        $data['mlm_plan'] == 'Unilevel' ||
                        $data['moduleStatus']->sponsor_commission_status) &&
                        $data['mlm_plan'] != 'Donation' &&
                        $data['configuration']->commission_criteria == 'genealogy')
                        <form action="{{ route('levelcommission.geneologyUpdate') }}" method="post" class="mt-3">
                            @csrf
                            <input type="hidden" name="check_percentage"
                                value="{{ $data['configuration']->level_commission_type }}">
                            <div class="genealogy_view" id="genealogy_view">
                                <legend>
                                    <span class="fieldset-legend">
                                        {{ $data['moduleStatus']->xup_status == 1 ? 'X-UP'. __('settings.commission_based_on_genealogy') : __('settings.commission_based_on_genealogy') }}
                                    </span>
                                </legend>
                                @isset($data['geneologyLevel'])
                                    @for ($i = 1; $i <= $levels; $i++)
                                        <div class="form-group">
                                            <label class="required">
                                                {{ 'level ' . $i . ' commission' }}
                                                <span>{{ $data['configuration']->level_commission_type == 'percentage' ? '%' : '' }}</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i>
                                                        {{ $data['configuration']->level_commission_type == 'percentage' ? '%' : $data['currency'] }}
                                                    </i></span>
                                                <input type="text"
                                                    class="form-control  @error('level_percentage.' . $i) is-invalid @enderror"
                                                    name="level_percentage[{{ $i }}]" min="0"
                                                    @if ($data['configuration']->level_commission_type == 'percentage') value="{{$data['geneologyLevel'][$i - 1]->percentage ?? 0}}">
                                                    @else
                                                    value="{{ formatCurrency(number_format($data['geneologyLevel'][$i - 1]->percentage)) ?? 0 }}"> @endif
                                                    @error('level_percentage.' . $i)
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                        </div>
                                    @endfor

                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary text-white" type="submit">{{ __('common.update') }}</button>
                                </div>
                            @endisset
                        </form>
                    @endif
                    @if ($data['mlm_plan'] != 'Donation' && $data['configuration']->commission_criteria != 'genealogy')
                        @if ((($data['mlm_plan'] == 'Matrix' ||
                            $data['mlm_plan'] == 'Unilevel' ||
                            $data['moduleStatus']->sponsor_commission_status) &&
                            $data['moduleStatus']->product_status) ||
                            ($data['moduleStatus']->ecom_status && $data['configuration']->commission_criteria == 'reg_pck') ||
                            $data['configuration']->commission_criteria == 'member_pck')
                            <form action="{{ route('levelcommission.update') }}" method="post">
                                @csrf
                                <div class="reg_pck_view" id="reg_pck_view">
                                    @if ($data['configuration']->commission_criteria == 'reg_pck')
                                        <legend>
                                            <span class="fieldset-legend">
                                                @if ($data['moduleStatus']->xup_status)
                                                    {{ __('settings.xup_commission_register_package') }}
                                                @else
                                                    {{ __('settings.level_commission_register_package') }}
                                                @endif
                                            </span>
                                        </legend>
                                    @endif
                                    @if ($data['configuration']->commission_criteria == 'member_pck')
                                        <legend>
                                            <span class="fieldset-legend">
                                                @if ($data['moduleStatus']->xup_status)
                                                    {{ __('settings.xup_commission_member_package') }}
                                                @else
                                                    {{ __('settings.level_commission_member_package') }}
                                                @endif

                                            </span>
                                        </legend>
                                    @endif

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('settings.packages') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $k = 0;
                                                @endphp
                                                @foreach ($packages as $package)
                                                    <tr>
                                                        <td>
                                                            {{ $package->name }}
                                                        </td>
                                                        @for ($i = 1; $i <= $levels; $i++)
                                                            <td>
                                                                {{ 'level' . ' ' . $i }}
                                                                <div class="input-group">
                                                                    <input type="hidden"
                                                                        name="levelpack[{{ $k }}][package_id]"
                                                                        value="{{ $package->id }}">
                                                                    <input type="hidden"
                                                                        name="levelpack[{{ $k }}][level]"
                                                                        value="{{ $i }}">
                                                                    <input type="text"
                                                                        name="levelpack[{{ $k }}][commission]"
                                                                        value="{{ $package->levelCommissionRegisterPack->where('level', $i)->first()->commission ?? 0 }}"
                                                                        class="form-control @error('levelpack.' . $k . '.*') is-invalid @enderror">

                                                                </div>
                                                            </td>
                                                            @php
                                                                $k++;
                                                            @endphp
                                                        @endfor
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary text-white" type="submit"
                                            value="update">{{ __('common.update') }}</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    @endif

                    @if ($data['mlm_plan'] == 'Donation' && $data['configuration']->commission_criteria == 'genealogy')
                        @isset($data['donationLevel'])
                            <form action="{{ route('donationLevel.update') }}" method="post">
                                @csrf
                                @method('patch')
                                <div class="reg_pck_view" id="donationView">
                                    <legend>
                                        <span class="fieldset-legend">
                                            {{ __('settings.level_commission') }}
                                        </span>
                                    </legend>
                                    <hr class="bg-primary">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $k = 0;
                                                @endphp
                                                @foreach ($data['donationLevel'] as $rate)
                                                    <tr>
                                                        <td class="fw-bolder">
                                                            {{ $rate->name }}
                                                        </td>
                                                        @for ($i = 1; $i <= $levels; $i++)
                                                            <td>
                                                                {{ 'level' . ' ' . $i }}
                                                                <div class="input-group">
                                                                    <input type="hidden"
                                                                        name="leveldonation[{{ $k }}][donationRate_id]"
                                                                        value="{{ $rate->id }}">
                                                                    <input type="hidden"
                                                                        name="leveldonation[{{ $k }}][level]"
                                                                        value="{{ $i }}">
                                                                    <input type="text"
                                                                        name="leveldonation[{{ $k }}][percentage]"
                                                                        value="{{ $rate->level->where('level_no', $i)->first()->percentage ?? 0 }}"
                                                                        class="form-control @error('leveldonation.' . $k . '*') is-invalid @enderror">

                                                                </div>
                                                            </td>
                                                            @php
                                                                $k++;
                                                            @endphp
                                                        @endfor
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary text-white" type="submit"
                                            value="update">{{ __('common.update') }}</button>
                                    </div>
                                </div>
                            </form>
                        @endisset
                    @endif
                </div>
            </div>
        </div>
    @endsection
