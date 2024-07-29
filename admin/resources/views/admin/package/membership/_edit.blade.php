<div class="offcanvas offcanvas-end" tabindex="-1" id="editpackage" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">{{ __('common.edit') . ' ' . $package->name }} </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <form action="{{ route('member.package.store', $package->id) }}" method="POST" class="mt-3"
                enctype="multipart/form-data" onsubmit="memberPackageUpdate(this)">
                {{-- @csrf --}}
                {{-- <noscript>
                    @method('patch')
                    @csrf
                </noscript> --}}
                <div id="stepOne" class="">
                    <div class="form-group">
                        <input type="hidden" name="stepone" value="1" id="stepOne">
                        <label>{{ __('package.id') }}<span class="text-danger">*</span></label>
                        <input type="text" name="product_id" class="form-control"
                            value="{{ old('name', $package->product_id) }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('common.name') }}<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" min="0"
                            value="{{ old('name', $package->name) }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('package.amount') }}<span class="text-danger">*</span></label>
                    </div>
                    <div class="input-group">
                        <div class="input-group-text">{{ $currency }}</div>
                        <input type="number" name="price" class="form-control" min="0"
                            value="{{ old('amount', formatCurrency($package->price)) }}">
                    </div>
                    @if ($pvVisible == 'yes')
                        <div class="form-group">
                            @if ($mlmPlan == 'Stair_step')
                                <label>{{ __('package.pv_value') }} <span class="text-danger">*</span></label>
                            @else
                                <label>{{ __('package.product_pv') }} <span class="text-danger">*</span></label>
                            @endif
                            <input type="number" name="pairValue" class="form-control" min="0"
                                value="{{ old('pairValue', $package->pair_value) }}">
                        </div>
                    @endif

                    @if ($bvVisible == 'yes')
                        <div class="form-group">
                            <label>{{ __('package.bv_value') }}<span class="text-danger">*</span></label>
                            <input type="number" name="bvValue" class="form-control"
                                value="{{ old('bv', $package->bv_value) }}">
                        </div>
                    @endif

                    @if ($moduleStatus->subscription_status)
                        <div class="form-group">
                            <label>{{ __('package.validity') }}<span class="text-danger">*</span></label>
                            <input type="number" name="validity" class="form-control"
                                value="{{ old('validity', $package->validity) }}">
                        </div>
                    @endif
                </div>
                @if ($mlmPlan == 'Binary' && $compensationStatus->plan_commission)
                    <div id="stepTwo">
                        <div class="form-group">
                            <h6>
                                {{ __('package.advanced_config_binary') }}
                            </h6>
                            <hr class="bg-primary">
                            <label for="">{{ __('common.pair_price') }} <span class="text-danger">*</span></label>
                        </div>
                        <div class="input-group">
                            <div class="input-group-text">{{ $currency }}</div>
                            <input type="number" name="pairPrice" class="form-control"
                                value="{{ old('pairPrice', formatCurrency($package->pair_price)) }}">
                        </div>
                    </div>
                @endif

                {{-- @if ($compensationStatus->sponser_commission = 1 && $moduleStatus->sponsor_commission_status == 'yes')
                    @php
                        $level = $configuration['commission_upto_level'];
                    @endphp
                    <h6>{{ __('common.advanced_configuration') }} : {{ __('package.level_commission') }}</h6>
                    @for ($i = 1; $i <= $level; $i++)
                        <div class="form-group">
                            <label>Level - {{ $i }} (%)</label>
                            <input type="number" name="levelCommission[{{ $i }}]" id=""
                                class="form-control">
                        </div>
                    @endfor
                    <div class="form-group">
                        <button type="button" class="btn btn-primary">{{ __('common.next') }}</button>
                    </div>
                @endif --}}

                @if ($moduleStatus->referral_status && $compensationStatus->referral_commission)
                    @if ($commissionType == 'sponsor_package' || $commissionType == 'joinee_package')
                        <h6>
                            <h6>{{ __('package.advanced_config_referral') }}</h6>
                        </h6>
                        <hr class="bg-primary">
                        <div class="form-group">
                            <label for="">{{ __('package.referral_commission') }} <span
                                    class="text-danger">*</span></label>

                        </div>
                        <div class="input-group">
                            <div class="input-group-text">{{ $currency }}</div>
                            <input type="number" name="referralCommission" id="" class="form-control"
                                value="{{ old('referralCommission', formatCurrency($package->referral_commission)) }}">
                        </div>
                    @endif

                @endif
                @if ($mlmPlan == "Monoline")
                    <hr class="bg-primary">
                    <div class="form-group">
                        <label>{{ __('package.reentry_limit') }} <span class="text-danger">*</span></label>
                        <input type="number" name="reentry_limit" class="form-control"
                            value="{{ old('reentry_limit', $package->reentry_limit) }}">
                    </div>
                @endif
                @if ($moduleStatus->roi_status && $compensationStatus->roi_commission)
                    <h6>{{ __('package.advanced_config_roi') }}</h6>
                    <hr class="bg-primary">
                    <div class="form-group">
                        <label>Hyip <span class="text-danger">*</span></label>
                        <input type="number" name="roi" class="form-control"
                            value="{{ old('roi', $package->roi) }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('common.days') }} <span class="text-danger">*</span></label>
                        <input type="number" name="days" id="" class="form-control"
                            value="{{ old('days', $package->days) }}">
                    </div>
                @endif

                <div class="form-group">
                    <label>{{ __('common.description') }}<span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ $package->description }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>{{ __('package.select_product_image') }}</label>
                    <div class="p-3 border bg-light row h-100 gap-1">

                        <div class="col-6 mt-3">
                            <input type="file" name="image" id="packageImage"
                                class="form-control-file @error('image') is-invalid @enderror"
                                onchange="addproductImage(event,'editproductImage')">
                        </div>

                        <div class="col-6 mt-3">
                            @if ($package->image == null || !isFileExists($package->image))
                                <img src="{{ asset('/assets/images/register.png') }}" class="img-fluid rounded"
                                    id="editproductImage">
                            @else
                                <img src="{{ $package->image }}" class="img-fluid" id="editproductImage">
                            @endif
                        </div>
                    </div>
                </div>
                @error('image')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                </div>
            </form>
        </div>

    </div>
</div>
