@extends('layouts.app')
@section('title', __('settings.repurchase_sales_commission'))
@section('content')
    <div class="container-fluid">
        
    <div class="card">
        <div class="card-body">
        @include('admin.settings.inc.links')
        @php
            $salesType = $data['configuration']->sales_type;
            $salesLevel = $data['configuration']->sales_level;
        @endphp
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">{{ __('settings.repurchase_sales_commission') }}</h4>
                </div>
            </div>
        </div>

            <form action="{{ route('update.salesConfig', $data['configuration']->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="commission"> {{ __('settings.sales_commission_criteria') }} <span
                            class="text-danger">*</span></label>
                    <select name="commission_criteria" id="commission" class="form-select">
                        <option value="cv" {{ $data['configuration']->sales_criteria == 'cv' ? 'selected' : '' }}>
                            {{ __('settings.sales_commission_based_on_sales_volume') }}</option>
                        <option value="sp" {{ $data['configuration']->sales_criteria == 'sp' ? 'selected' : '' }}>
                            {{ __('settings.sales_commission_based_on_sales_price') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="salesCommission">{{ __('settings.sales_commission_distribution') }} <span
                            class="text-danger">*</span></label>
                    <select name="sales_type" id="salesCommission" class="form-select">
                        <option value="genealogy" {{ $salesType == 'genealogy' ? 'selected' : '' }}>
                            {{ __('settings.distribution_basedon_genealogy_level') }}</option>
                        <option value="rank" {{ $salesType == 'rank' ? 'selected' : '' }}>
                            {{ __('settings.distribution_based_on_rank') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="commission_upto_level"> {{ __('settings.distribution_up_to_level') }} <span
                            class="text-danger">*</span></label>
                    <input type="number" name="sales_level" id="commission_upto_level"
                        class="form-control
                    @error('sales_level') is-invalid @enderror" required
                        min="0" value="{{ $salesLevel }}">
                    @error('sales_level')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                </div>
            </form>

        <hr class="bg-primary">

            @if ($salesType == 'genealogy')
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">{{ __('settings.distribution_basedon_genealogy_level') }}</h4>
                        </div>
                    </div>
                </div>
                @isset($data['salesLevel'])
                    <form action="{{ route('sales.update.geneology') }}" method="post">
                        @csrf
                        @method('put')
                        @foreach ($data['salesLevel'] as $level)
                            <div class="form-group">
                                <label>
                                    {{ 'Level' . ' ' . $level->level . ' ' . '%' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="salesLevel[{{ $level->level }}]" min="0"
                                    class="form-control @error('salesLevel.' . $level->level) is-invalid @enderror"
                                    value="{{ $level->percentage }}">
                                @error('salesLevel.' . $level->level)
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @endforeach

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                        </div>
                    </form>
                @endisset
            @endif

            @if ($data['moduleStatus']->rank_status && $salesType == 'rank')
                @isset($data['rank'])
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18"> {{ __('settings.distribution_based_on_rank') }} </h4>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('sales.update.rank') }}" method="post">
                        @csrf
                        @method('put')


                        @for ($i = 1; $i <= $salesLevel; $i++)
                            <div class="form-group">
                                <label>Level {{ $i }}</label>
                                <hr class="bg-primary">

                                @forelse ($data['rank'] as $rank)
                                    <div class="form-group">
                                        <label for="">{{ $rank->name . ' ' . '(%)' }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="{{ $rank->name }}[{{ $i }}]"
                                            class="form-control @error($rank->name . '.' . $i) is-invalid @enderror"
                                            min="0"
                                            value="{{ $rank->salesRank->where('level', $i)->first()->sales ?? 0 }}">
                                        @error($rank->name . '.' . $i)
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                @empty
                                    <div class="form-group">
                                        {{ __('common.no_data') }}
                                    </div>
                                @endforelse
                            </div>
                        @endfor

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                        </div>

                    </form>
                @endisset
            @endif

        </div>
    </div>
    </div>
    </div>
@endsection
