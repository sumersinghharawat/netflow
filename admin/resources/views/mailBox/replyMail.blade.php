@extends('layouts.app')
@section('title', __('mail.mailbox'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="email-leftbar card">
                    <a href="{{ route('mail.compose') }}" class="btn btn-primary" style="padding-bottom: 13px;"><i
                            style="font-size:18px;position: relative;top: 3px;" class="mdi mdi-email-open me-2"></i>
                        {{ __('mail.compose_mail') }}</a>
                    @include('mailBox.sidebar')
                </div>

                <div class="email-rightbar mb-3">
                    <div class="card" id="composemailCard">
                        <div class="card-header">
                            <h4>
                                {{ __('mail.compose_mail') }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('store.composemail') }}" enctype="multipart-formdata">
                                @csrf
                                <input type="hidden" name="reply" value="{{ $mail->id }}">
                                <div>
                                    <div class="row">
                                        <div class="form control">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <select class="form-control" name="send_status" id="send_status"
                                                        onchange="select_user(this.value)">
                                                        <option value="single_user" selected>{{ __('mail.single_user') }}</option>
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
                                                    <input type="text" class="form-control" name="" readonly value="{{ $mail->fromUser->username }}">
                                                    <input type="hidden" class="form-control" name="user_id" readonly value="{{ $mail->fromUser->id }}">

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
                                                        value="Re: {{ old('subject', $mail->subject) }}">
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
                                                    <textarea class="form-control" rows="10" id="compose-mail" name="message">{{ old('message') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <br>

                                </div>
                                <button type="submit" class="btn btn-primary">{{ __('mail.Send') }} <i
                                        class="fab fa-telegram-plane ms-1"></i></button>
                            </form>
                        </div>

                    </div>
                </div> <!-- end Col-9 -->
            </div>
        </div><!-- End row -->

    </div>
@endsection
@push('scripts')
    <script>
        $(() => {
            $('.js-example-basic-multiple').select2();
            $('#compose-mail').summernote({
                placeholder: 'Content here',
                height: 200
            });
            getUsers();

        });

        function select_user(a) {
            if (a == "single_user") {
                document.getElementById('user').style.display = "block";
            } else {
                document.getElementById('user').style.display = "none";
            }
        }
        $(document).ready(function() {
            if (document.getElementById('send_status').value == "single_user") {
                document.getElementById('user').style.display = "block";
            } else {
                document.getElementById('user').style.display = "none";
            }
        });

        const deleteMail = async (form) => {
            event.preventDefault();
            let confirm = await confirmSwal()
            if (confirm.isConfirmed == true) {
                form.submit();
            }
        }
    </script>
@endpush
