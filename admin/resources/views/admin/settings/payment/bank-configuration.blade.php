<div class="offcanvas offcanvas-end" id="bankConfiguration" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('settings.bank_details') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form method="post" action="{{ route('bankdetail.update', $data['bankdetails']->id) }}">
            @csrf
            <div class="p-3">
                <div class="form-group">
                    <label for="message-text" class="col-form-label">{{ __('settings.bank_details') }} <span
                            class="text-danger">*</span></label>
                    <textarea class="form-control h-100" name="accountInfo">{{ $data['bankdetails']->account_info }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="offcanvas">{{ __('common.close') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('common.update') }}</button>
            </div>
        </form>
    </div>
</div>

 {{-- stripe config --}}
<div class="offcanvas offcanvas-end" id="stripeConfiguration" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('settings.stripe_details') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form method="post" onsubmit="storeStripe(this)"
            action="{{ route('stripeDetail.update', $data['paymentConfig']->where('slug', 'stripe')->first()->id) }}">
            @csrf
            <div class="p-3">
                <div class="form-group">
                    <label for="message-text" class="col-form-label">{{ __('settings.mode') }} <span
                            class="text-danger">*</span></label>
                    <select name="mode" class="form-control">
                        <option value='test' @if ($data['paymentConfig']->where('slug', 'stripe')->first()->mode=='test') selected="selected" @endif> Test</option>
                        <option value='live' @if ($data['paymentConfig']->where('slug', 'stripe')->first()->mode=='live') selected="selected" @endif> Live</option>
                    </select>
                </div>
            </div>
            <div class="p-3">
                <div class="form-group"><input type="hidden" name="id"
                        value="{{ $data['paymentConfig']->where('slug', 'stripe')->first()->id }}">
                    <label for="message-text" class="col-form-label">{{ __('settings.stripe_public') }} <span
                            class="text-danger">*</span></label>
                    <textarea class="form-control h-100" name="public_key">{{ $data['paymentConfig']->where('slug', 'stripe')->first()->details ? $data['paymentConfig']->where('slug', 'stripe')->first()->details->public_key : '' }}</textarea>
                </div>
            </div>
            <div class="p-3">
                <div class="form-group">
                    <label for="message-text" class="col-form-label">{{ __('settings.stripe_secret') }}<span
                            class="text-danger">*</span></label>
                    <textarea class="form-control h-100" name="secret_key">{{ $data['paymentConfig']->where('slug', 'stripe')->first()->details ? $data['paymentConfig']->where('slug', 'stripe')->first()->details->secret_key : '' }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="offcanvas">{{ __('common.close') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('common.update') }}</button>
            </div>
        </form>
    </div>
</div>
{{-- Nowpayment config --}}
<div class="offcanvas offcanvas-end" id="nowpaymentConfiguration" aria-labelledby="offcanvasRightLabel">
   <div class="offcanvas-header">
       <h5 id="offcanvasRightLabel">{{ __('settings.nowpayment_details') }}</h5>
       <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
   </div>
   <div class="offcanvas-body">
       <form method="post" onsubmit="storeNowpayment(this)"
           action="{{ route('nowpaymentDetail.update', $data['paymentConfig']->where('slug', 'nowpayment')->first()->id) }}">
           @csrf
           <div class="p-3">
               <div class="form-group">
                   <label for="message-text" class="col-form-label">{{ __('settings.mode') }} <span
                           class="text-danger">*</span></label>
                   <select name="mode" class="form-control">
                       <option value='test' @if ($data['paymentConfig']->where('slug', 'nowpayment')->first()->mode=='test') selected="selected" @endif> Test</option>
                       <option value='live' @if ($data['paymentConfig']->where('slug', 'nowpayment')->first()->mode=='live') selected="selected" @endif> Live</option>
                   </select>
               </div>
           </div>
           <div class="p-3">
               <div class="form-group"><input type="hidden" name="id"
                       value="{{ $data['paymentConfig']->where('slug', 'nowpayment')->first()->id }}">
                   <label for="message-text" class="col-form-label">{{ __('settings.nowpayment_public') }} <span
                           class="text-danger">*</span></label>
                   <textarea class="form-control h-100" name="public_key">{{ $data['paymentConfig']->where('slug', 'nowpayment')->first()->details ? $data['paymentConfig']->where('slug', 'nowpayment')->first()->details->public_key : '' }}</textarea>
               </div>
           </div>
           <div class="p-3">
               <div class="form-group">
                   <label for="message-text" class="col-form-label">{{ __('settings.nowpayment_secret') }}<span
                           class="text-danger">*</span></label>
                   <textarea class="form-control h-100" name="secret_key">{{ $data['paymentConfig']->where('slug', 'nowpayment')->first()->details ? $data['paymentConfig']->where('slug', 'nowpayment')->first()->details->secret_key : '' }}</textarea>
               </div>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary"
                   data-bs-dismiss="offcanvas">{{ __('common.close') }}</button>
               <button type="submit" class="btn btn-danger">{{ __('common.update') }}</button>
           </div>
       </form>
   </div>
</div>
{{-- paypal config --}}
{{-- <div class="offcanvas offcanvas-end" id="paypalConfiguration" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('settings.paypal_details') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form method="post" onsubmit="storePaypal(this)" action="{{ route('paypalDetails.update') }}">
            @csrf
            <div class="p-3">
                <div class="form-group">
                    <label for="message-text" class="col-form-label">{{ __('settings.paypal_mode') }} <span
                            class="text-danger">*</span></label>
                    <!-- <input type="text" name="mode" class="form-control" value="{{ $data['paymentConfig']->where('slug', 'paypal')->first()->mode }}"> -->
                    <!--<select name="mode" class="form-control">
                        <option value="test" @if ($data['paymentConfig']->where('slug', 'paypal')->first()->mode == 'test') selected @endif>
                            {{ __('common.test') }}</option>
                        <option value="live" @if ($data['paymentConfig']->where('slug', 'paypal')->first()->mode == 'live') selected @endif>
                            {{ __('common.live') }} </option>
                    </select>
                </div>
            </div>
            <div class="p-3">
                <div class="form-group"><input type="hidden" name="id"
                        value="{{ $data['paymentConfig']->where('slug', 'paypal')->first()->id }}">
                    <label for="message-text" class="col-form-label">{{ __('settings.paypal_public') }} <span
                            class="text-danger">*</span></label>
                    <textarea class="form-control h-100" name="public_key">{{ $data['paymentConfig']->where('slug', 'paypal')->first()->details ? $data['paymentConfig']->where('slug', 'paypal')->first()->details->public_key : '' }}</textarea>
                </div>
            </div>
            <div class="p-3">
                <div class="form-group">
                    <label for="message-text" class="col-form-label">{{ __('settings.paypal_secret') }}<span
                            class="text-danger">*</span></label>
                    <textarea class="form-control h-100" name="secret_key">{{ $data['paymentConfig']->where('slug', 'paypal')->first()->details ? $data['paymentConfig']->where('slug', 'paypal')->first()->details->secret_key : '' }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="offcanvas">{{ __('common.close') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('common.update') }}</button>
            </div>
        </form>
    </div>
</div>
--}}
