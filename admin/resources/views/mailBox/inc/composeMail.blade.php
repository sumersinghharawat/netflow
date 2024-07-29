<div class="offcanvas offcanvas-end" id="composeMail" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('mail.compose_mail') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form method="post" action="{{ route('store.composemail') }}" enctype="multipart-formdata">
            @csrf
            <div class="modal-body">
                <div>

                    <br>
                    <div class="row">
                        <div class="form control">
                            <div class="row">


                                <div class="col-md-12">
                                    <select class="form-control" name="send_status" id="send_status"
                                        onchange="select_user(this.value)">
                                        <option value="single_user">{{__('mail.single_user')}}</option>
                                        <option value="all">{{__('mail.all_users')}}</option>

                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row" id="user" style="display: none;">
                        <div class="form control">
                            <div class="row">
                                <label>
                                    {{ __('mail.user') }}
                                </label>

                                <div class="col-md-12">

                                    <select class="form-control select2-search-user-canvas" name="user_id"
                                        id="select2-canvas-compose">
                                    </select>
                                    @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form control">
                            <div class="row">
                                <label>
                                    {{ __('mail.Subject') }}
                                </label>

                                <div class="col-md-12">
                                    <input class="form-control" type="text" name="subject"
                                        value="{{ old('subject') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form control">
                            <div class="row">
                                <label>
                                    {{ __('mail.Content') }}
                                </label>

                                <div class="col-md-12">
                                    <textarea id="summernote" name="message" class="form-control" required>

                                        {{ old('message') }}

                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="offcanvas">{{ __('common.close') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('mail.Send') }} <i
                        class="fab fa-telegram-plane ms-1"></i></button>
            </div>
        </form>
    </div>
</div>
