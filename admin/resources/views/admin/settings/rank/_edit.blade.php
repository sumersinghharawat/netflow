@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <form action="{{ route('rank.update', $rankDetail->id) }}" method="post" class="mt-3">
            @csrf
            <div class="form-group">
                <label>{{ __('rank.name') }}</label>
                <input type="text" name="rankName" id=""
                    class="form-control @error('rankName') is-invalid @enderror" value="{{ $rankDetail->name }}">
                @error('rankName')
                    <span class="text-danger form-text">{{ $message }}</span>
                @enderror
                <input type="hidden" name="rank_id" value="{{ $rankDetail->id }}">
            </div>
            @if ($data['rankConfig']->joinee_package == 1)
                <div class="form-group">
                    @if (isset($rankDetail->joineeRank[0]))
                        <input type="hidden" name="joineePackId" id="joineePackId"
                            value="{{ $rankDetail->joineeRank[0]->id }}">
                        @error('joineePackId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    @else
                        <input type="hidden" name="packageId" id="packageId" value="0">
                    @endif

                    <select name="package" class="form-select">
                        @foreach ($data['package'] as $package)
                            @if (isset($rankDetail->rankPackage[0]))
                                <option value="{{ $package->id }}"
                                    {{ $rankDetail->rankPackage[0]->id == $package->id ? 'selected' : '' }}>
                                    {{ $package->product_name }}</option>
                            @else
                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @endif
            @if ($data['rankConfig']->referral_count == 1)
                <div class="form-group">
                    <label>{{ __('settings.referral_count') }}</label>
                    <input type="number" name="referralCount"
                        class="form-control @error('referralCount') is-invalid @enderror" min="0"
                        value="{{ $rankDetail->referral_count }}">
                    @error('referralCount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror


                </div>
            @endif
            @if ($data['rankConfig']->personal_pv == 1)
                <div class="form-group">
                    <label>{{ __('settings.personal') }} Pv</label>
                    <input type="number" name="personalPv" class="form-control @error('personalPv') is-invalid @enderror"
                        min="0" value="{{ $rankDetail->personal_pv }}">
                    @error('personalPv')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endif
            @if ($data['rankConfig']->group_pv == 1)
                <div class="form-group">
                    <label>{{ __('settings.group') }} Pv</label>
                    <input type="number" name="groupPv" class="form-control @error('groupPv') is-invalid @enderror"
                        min="0" value="{{ $rankDetail->group_pv }}">
                    @error('groupPv')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endif
            @if ($data['moduleStatus']->mlm_plan == 'Binary' || $data['moduleStatus']->mlm_plan == 'Matrix')
                @if ($data['rankConfig']->downline_member_count == 1)
                    <div class="form-group">
                        <label>{{ __('settings.downline_member_count') }}</label>
                        <input type="number" name="downline_count"
                            class="form-control @error('downline_count') is-invalid @enderror" min="0"
                            value="{{ $rankDetail->downline_count }}">
                        @error('downline_count')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
                @if ($data['rankConfig']->downline_purchase_count == 1)
                    @foreach ($rankDetail->package as $package)
                        <div class="form-group">
                            <label>{{ __('settings.minimum_count_of_downline_members_with_package') }} -
                                {{ $package->name }}</label>
                            <input type="number" name="downline_purchase_count[{{ $package->id }}]"
                                class="form-control @error('downline_purchase_count[{{ $package->id }}]') is-invalid @enderror"
                                min="0" value="{{ $package->pivot->package_count }}">
                            @error('downline_purchase_count[{{ $package->id }}]')
                                <span class="text-danger form-text">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                @endif
                @if ($data['rankConfig']->downline_rank == 1)
                    @foreach ($rankDetail->donwlineRank as $rank)
                        <div class="
                                form-group">
                            <label>{{ __('settings.minimum_count_of_downline_members_with_rank') }}-
                                {{ $rank->rank->name }}</label>
                            <input type="number" name="downline_rank[{{ $rank->id }}]" class="form-control"
                                min="0" value="{{ $rank->count }}">
                        </div>
                    @endforeach
                @endif
            @endif
            @if ($data['moduleStatus']->referral_status && $data['commission_type'] == 'rank')
                <div class="form-group">
                    <label for="">{{ __('commission.referral_commission') }}</label>
                    <input type="text" name="referral_commission" id="" class="form-control">
                    @error('referral_commission')
                        <span class="text-danger form-text">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            <div class="form-group">
                <label>{{ __('settings.rank_achieve_bonus') }}</label>
                <input type="number" name="rank_bonus" class="form-control"
                    value="{{ formatCurrency($rankDetail->bonus) }}">
                @error('rank_bonus')
                    <span class="text-danger form-text">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>{{ __('settings.rank_color') }}</label>
                <input type="color" name="rankColor" id="" class="form-control form-control-color"
                    value="{{ $rankDetail->color }}">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                <a href="{{ route('rank') }}" class="btn btn-danger">{{ __('common.back') }}</a>
            </div>
        </form>
    </div>


@endsection
