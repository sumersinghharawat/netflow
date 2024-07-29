@extends('layouts.app')
@section('title', __('mail.mailbox'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">{{ __('mail.mailbox') }}</h4>
                    <div class="page-title-right" id="inbox" style="display: block;">
                        {{ __('mail.email') }} / {{ Route::currentRouteName() == 'mailBox' ? __('mail.inbox') : __('mail.sent_mail') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">

                <div class="email-leftbar card">
                    <a href="{{ route('mail.compose') }}" class="btn btn-primary" style="padding-bottom: 13px;"><i
                            style="font-size:18px;position: relative;top: 3px;" class="mdi mdi-email-open me-2"></i>
                        {{ __('mail.compose_mail') }}</a>
                    @include('mailBox.sidebar')
                </div>

                <div class="email-rightbar mb-3">
                    <div class="card" id="inboxCard" style="overflow:hidden">
                        <div class="row" style="background-color:#f3f3f3">
                            <h6 style="margin-left: 20px;margin-top: 10px;">
                                <a href=""><i class="refresh_btn mdi mdi-refresh"></i></a>
                                <span class="mailbx_tp_txt">
                                    {{ Route::currentRouteName() == 'mailBox' ? __('mail.inbox') : __('mail.sent_mail') }}</span>
                            </h6>
                        </div>

                        <div class="col-md-12">
                            <ul class="message-list">

                                @forelse($messages as $value)
                                    <div class="row">
                                        <div class="col">
                                            <li>
                                                <a href="{{ route('read.mail', [$value, 'admin_message']) }}">

                                                    <div class="col-mail col-mail-1 col">
                                                        <div class="mail">
                                                            <form onsubmit="deleteMail(this)"
                                                                action="{{ route('mail.delete', [$value['id'], 'admin_message']) }}"
                                                                method="post" style="margin-left: 15px;">
                                                                @csrf
                                                                @if (Request::url() == url('admin/sent-mail'))
                                                                    <input type="hidden" name="mail_type"
                                                                        value="sent_mail">
                                                                @else
                                                                    <input type="hidden" name="mail_type"
                                                                        value="inbox_mail">
                                                                @endif
                                                                <button type="submit"
                                                                    class="btn btn-danger mail_delete_btn"><i
                                                                        class="fa fa-trash"></i></button>
                                                                <div class="mailer_user_photo_bx">
                                                                    {{ ($value->to_all) ? 'A' : Str::of($value->toUser->username)->substr(0, 1)->upper(); }}
                                                                </div>
                                                            </form>
                                                        </div>

                                                        @if (!$value->to_all)
                                                            <a href="{{ route('read.mail', [$value->id, 'admin_message', 'sent-mail']) }}" class="title">{{ $value->toUser->username }}</a>
                                                        @else
                                                            <a href="{{ route('read.mail', [$value->id, 'admin_message', 'sent-mail']) }}" class="title">{{ "All Users" }}</a>
                                                        @endif

                                                    </div>
                                                    <div class="col-mail col-mail-2">
                                                        <a href="{{ route('read.mail', [$value['id'], 'admin_message']) }}"
                                                            class="subject">
                                                            @if ('admin_message' == 'admin_message')
                                                                <span class="teaser">{{ $value['subject'] }}</span>
                                                            @else
                                                                <span class="teaser">{{ $value['subject'] }}.
                                                                    {{ __('mail.Contacted_you') }}</span>
                                                            @endif
                                                        </a>
                                                        <div class="date">
                                                            {{ \Carbon\Carbon::parse($value['date'])->format('D M Y') }}
                                                        </div>
                                                    </div>

                                                </a>
                                            </li>
                                        </div>
                                    </div>

                                @empty
                                <div class="nodata_view mt-5">
                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                    <span>{{ __('common.no_data') }}</span>
                                </div>
                                @endforelse


                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="col-md-12" style="padding: 0 15px !important;">
                                {{ $messages->onEachSide(0)->links() }}
                                </div>
                            </div>

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
            $('#compose-mail').summernote();
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
