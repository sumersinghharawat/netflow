@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <h3>{{ __('settings.add_new_rank') }}</h3>
        <a href="{{ route('rank') }}" class="btn btn-danger">{{ __('common.back') }}</a>
        <form action="{{ route('rank.store') }}" method="post" class="mt-3">
            @csrf
            <div class="form-group">
                <label>{{ __('settings.rank_name') }}</label>
                <input type="text" name="rankName" id="" class="form-control">
            </div>
            @if ($data['rankConfig']->joinee_package == 1)
                <div class="form-group">
                    <select class="form-control" name="package">
                        @foreach ($data['package'] as $package)
                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                        @endforeach
                    </select>
                    {{-- TODO Load all packages in dropdown --}}
                </div>
            @endif
            @if ($data['rankConfig']->referral_count == 1)
                <div class="form-group">
                    <label>{{ __('settings.referral_count') }}</label>
                    <input type="number" name="referralCount" class="form-control" min="0">
                </div>
            @endif
            @if ($data['rankConfig']->personal_pv == 1)
                <div class="form-group">
                    <label>{{ __('settings.personal') }} Pv</label>
                    <input type="number" name="personalPv" class="form-control" min="0">
                </div>
            @endif
            @if ($data['rankConfig']->group_pv == 1)
                <div class="form-group">
                    <label>{{ __('settings.group') }} Pv</label>
                    <input type="number" name="groupPv" class="form-control" min="0">
                </div>
            @endif
            @if ($data['moduleStatus']->mlm_plan == 'Binary' || $data['moduleStatus']->mlm_plan == 'Matrix')
                @if ($data['rankConfig']->downline_member_count == 1)
                    <div class="form-group">
                        <label>{{ __('settings.downline_member_count') }}</label>
                        <input type="number" name="downline_count" class="form-control" min="0">
                    </div>
                @endif
                @if ($data['rankConfig']->downline_purchase_count == 1)
                    @foreach ($data['package'] as $package)
                        <div class="form-group">
                            <label>{{ __('settings.minimum_count_of_downline_member_with_package') }} - {{ $package->name }}</label>
                            <input type="hidden" value="{{ $package->id }}" name="package_{{ $package->id }}">
                            <input type="number" name="package_count_{{ $package->id }}" class="form-control" min="0">
                        </div>
                    @endforeach
                @endif
                @if ($data['rankConfig']->downline_rank == 1)
                    @foreach ($data['rank'] as $rank)
                        <div class="form-group">
                            <label>{{ __('settings.minimum_count_of_downline_member_with_rank') }} - {{ $rank->rank_name }}</label>
                            <input type="number" name="downline_rank_{{ $rank->id }}" class="form-control" min="0">
                        </div>
                    @endforeach
                @endif
            @endif
            @if ($data['moduleStatus']->referral_status == 'yes' && $data['commission_type'] == 'rank')
                <div class="form-group">
                    <label for="">{{ __('commission.referral_commission') }}</label>
                    <input type="text" name="referral_commission" id="" class="form-control">
                </div>
            @endif

            <div class="form-group">
                <label>{{ __('settings.rank_achieve_bonus') }}</label>
                <input type="number" name="rank_bonus" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ __('settings.rank_color') }}</label>
                <input type="color" name="rankColor" id="" class="form-control form-control-color">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
            </div>
        </form>
    </div>


@endsection
