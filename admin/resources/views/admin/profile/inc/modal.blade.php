<div class="modal fade modal_type_two" id="resetPassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('check.password') }}" method="POST" id="admin-password"
                onsubmit="changePassword(this)">
                <div class="modal_header_top">
                   <!-- <svg height="32px" version="1.1" viewBox="0 0 32 32" width="32px" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink"><title/><desc/><defs/><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="#fff" id="icon-114-lock"><path d="M16,21.9146472 L16,24.5089948 C16,24.7801695 16.2319336,25 16.5,25 C16.7761424,25 17,24.7721195 17,24.5089948 L17,21.9146472 C17.5825962,21.708729 18,21.1531095 18,20.5 C18,19.6715728 17.3284272,19 16.5,19 C15.6715728,19 15,19.6715728 15,20.5 C15,21.1531095 15.4174038,21.708729 16,21.9146472 L16,21.9146472 L16,21.9146472 Z M15,22.5001831 L15,24.4983244 C15,25.3276769 15.6657972,26 16.5,26 C17.3284271,26 18,25.3288106 18,24.4983244 L18,22.5001831 C18.6072234,22.04408 19,21.317909 19,20.5 C19,19.1192881 17.8807119,18 16.5,18 C15.1192881,18 14,19.1192881 14,20.5 C14,21.317909 14.3927766,22.04408 15,22.5001831 L15,22.5001831 L15,22.5001831 Z M9,14.0000125 L9,10.499235 C9,6.35670485 12.3578644,3 16.5,3 C20.6337072,3 24,6.35752188 24,10.499235 L24,14.0000125 C25.6591471,14.0047488 27,15.3503174 27,17.0094776 L27,26.9905224 C27,28.6633689 25.6529197,30 23.991212,30 L9.00878799,30 C7.34559019,30 6,28.652611 6,26.9905224 L6,17.0094776 C6,15.339581 7.34233349,14.0047152 9,14.0000125 L9,14.0000125 L9,14.0000125 Z M10,14 L10,10.4934269 C10,6.90817171 12.9101491,4 16.5,4 C20.0825462,4 23,6.90720623 23,10.4934269 L23,14 L22,14 L22,10.5090731 C22,7.46649603 19.5313853,5 16.5,5 C13.4624339,5 11,7.46140289 11,10.5090731 L11,14 L10,14 L10,14 Z M12,14 L12,10.5008537 C12,8.0092478 14.0147186,6 16.5,6 C18.9802243,6 21,8.01510082 21,10.5008537 L21,14 L12,14 L12,14 L12,14 Z M8.99742191,15 C7.89427625,15 7,15.8970601 7,17.0058587 L7,26.9941413 C7,28.1019465 7.89092539,29 8.99742191,29 L24.0025781,29 C25.1057238,29 26,28.1029399 26,26.9941413 L26,17.0058587 C26,15.8980535 25.1090746,15 24.0025781,15 L8.99742191,15 L8.99742191,15 Z" id="lock"/></g></g></svg> -->
                    <img src="{{asset('assets/images/reset-password.png')}}" alt="" class="change-pass">
                   <h5 class="modal-title" id="exampleModalLabel">{{ __('profile.change_password') }}</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        @if ($data['user']->user_type == 'admin')
                            <label for="" class="col-md-12 control-label">{{ __('profile.current_password') }}
                            </label>
                            <div class="col-md-12">
                                <input type="password" class="form-control valid" name="current_password" required=""
                                    autofocus="" aria-invalid="false">
                            </div>
                        @endif
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-md-12 control-label">{{ __('profile.new_password') }}
                        </label>
                        <div class="col-md-12">
                            <input type="password" class="form-control valid" name="new_password" id="new-password"
                                required="" autofocus="" aria-invalid="false">
                            <span id="pswd_error1"></span>
                            <input type="hidden" name="userId" value="{{ $data['user']->id }}" id="userId">

                        </div>

                    </div>
                    <div class="form-group row">
                        <label for="" class="col-md-12 control-label">{{ __('profile.confirm_password') }}
                        </label>
                        <div class="col-md-12">
                            <input type="password" class="form-control valid" name="new_password_confirmation"
                                id="confirm-password" required="" autofocus="" aria-invalid="false">
                            <span id="pswd_error"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button id="password" class="btn btn-primary" type="submit">{{ __('common.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade modal_type_two" id="resetTransactionPassword" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="max-width: 400px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('trans-password.update') }}" method="POST" onsubmit="changeTransPassword(this)">
                <div class="modal_header_top">
                <img src="{{asset('assets/images/reset-password.png')}}" alt="" class="change-pass">
                     <h5 class="modal-title" id="exampleModalLabel">{{ __('profile.change_transaction_password') }}</h5>
                 </div>
                <div class="modal-body">
                    @if ($data['user']->user_type == 'admin')
                        <div class="form-group row">
                            <label for=""
                                class="col-md-12 control-label">{{ __('profile.current_transaction_password') }}
                            </label>

                            <div class="col-md-12">
                                <input type="password" class="form-control valid" name="current_password"
                                    aria-invalid="false">
                            </div>
                        </div>
                    @endif
                    <div class="form-group row">
                        <label for=""
                            class="col-md-12 control-label">{{ __('profile.new_transaction_password') }} </label>
                        <div class="col-md-12">
                            <input type="password" class="form-control valid" name="password" aria-invalid="false">
                            @error('password')
                                <span class="text-danger form-text">{{ $message }}</span>
                            @enderror
                            <input type="hidden" name="userId" value="{{ $data['user']->id }}" id="userId">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for=""
                            class="col-md-12 control-label">{{ __('profile.confirm_transaction_password') }} </label>
                        <div class="col-md-12">
                            <input type="password" class="form-control valid" aria-invalid="false"
                                name="password_confirmation">
                            @error('password_confirmation')
                                <span class="text-danger form-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary text-white">{{ __('common.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade " id="updatePv" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">
                    {{ __('profile.update_pv_user', ['Name' => $data['user']->username]) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" name="user_id" value="{{ $data['user']->id }}" id="userid">
                    <input type="number" name="pv" class="form-control" min="0"
                        placeholder="{{ __('profile.enter_pv') }}" id="pv">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary text-white" onclick="updatePv('add')"
                    id="add_button">{{ __('profile.update_pv') }}</button>
                <button type="button" class="btn btn-danger text-white" onclick="updatePv('deduct')"
                    id="deduct_button">{{ __('profile.deduct_pv') }}</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="cancel_button">{{ __('common.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
