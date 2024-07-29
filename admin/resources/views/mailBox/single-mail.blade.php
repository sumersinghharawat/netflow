@extends('layouts.app')
@section('title', 'MailBox')
@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">{{ __('mail.Read_email') }}</h4>

                    <div class="page-title-right">

                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <!-- Left sidebar -->
                <div class="email-leftbar card">
                    <a href="{{ route('mail.compose') }}" class="btn btn-primary" style="padding-bottom: 13px;"><i
                            style="font-size:18px;position: relative;top: 3px;" class="mdi mdi-email-open me-2"></i>
                        {{ __('mail.compose_mail') }}</a>
                    @include('mailBox.sidebar')

                </div>


                <div class="single_mail email-rightbar mb-3">

                </div>

                <div class="single_mail email-rightbar mb-3">

                    <div class="card">
                        <div class="col-md-12" style="background-color:#f3f3f3">
                            <h6 style="margin-left: 20px;margin-top: 10px;padding-bottom: 10px;position: relative;">
                                <a href="{{ url()->previous() }}"><i class="refresh_btn mdi mdi-arrow-left"></i></a>
                                <span class="mailbx_tp_txt">
                                    {{ __('common.back') }}</span>

                                @if ($mail['type'] == 'admin_message')
                                    @if ($replyStatus)
                                        <a href="{{ route('replyMail', $mail->id) }}"
                                            class="btn btn-secondary waves-effect reply_sngle_right"><i
                                                class="mdi mdi-reply"></i>
                                            {{ __('mail.reply') }}</a>
                                    @endif
                                @endif

                            </h6>
                        </div>

                        <div class="card-body mail_contant_scrl">
                            @if (!$page && $mail['type'] == 'admin_message')

                                @forelse ($mail->replys->sortByDesc('id') as $reply)
                                    <div
                                        class="d-flex mail_box_cnt_row mb-2 @if ($reply->fromUser->username == auth()->user()->username) sent-iem @endif">

                                        <div class="flex-shrink-0 me-3">
                                            <img class="rounded-circle avatar-sm"
                                                src="{{ asset('/assets/images/users/avatar-1.jpg') }}"
                                                alt="Generic placeholder image">
                                        </div>

                                        <div class="mail_contant">
                                            <div class="flex-grow-1 reply_mail_open">
                                                <h5 class="single_mail_head mt-1">
                                                    {{ $reply->fromUser->username == auth()->user()->username ? 'Me' : $reply->fromUser->username }}
                                                </h5>
                                                <h4 class="subject_mail_cnt font-size-16">{{ $reply->subject }} <small
                                                        class="text-muted mail_date">{{ $reply->date }}</small></h4>

                                            </div>
                                            <div class="reply_mail_content">
                                                @if ($reply->to_user_id == null)
                                                    <p>{{ __('mail.This_mail_are_send_to_all_users') }}</p>
                                                @else
                                                    <p>{{ __('mail.dear') }} {{ $reply->toUser->username }},</p>
                                                    <p>

                                                        {!! html_entity_decode($reply->message) !!}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>


                                    </div>
                                @empty
                                @endforelse
                            @endif
                            @if ($mail->reply)
                                <div class="d-flex mail_box_cnt_row mb-2">
                                    <div class="flex-shrink-0 me-3">
                                        <img class="rounded-circle avatar-sm"
                                            src="{{ asset('/assets/images/users/avatar-1.jpg') }}"
                                            alt="Generic placeholder image">
                                    </div>

                                    <div class="mail_contant">
                                        <div class="flex-grow-1 reply_mail_open">
                                            <h4 class="single_mail_head mt-1">
                                                {{ $mail->reply->fromUser->username == auth()->user()->username ? 'Me' : $mail->reply->fromUser->username }}
                                            </h4>
                                            <h4 class="subject_mail_cnt font-size-16">{{ $mail->reply->subject }} <small
                                                    class="text-muted mail_date">{{ $mail->reply->date }}</small></h4>
                                        </div>
                                        <div class="reply_mail_content">
                                            @if ($mail->reply->to_user_id == null)
                                                <p>{{ __('mail.This_mail_are_send_to_all_users') }}</p>
                                            @else
                                                <p>{{ __('mail.dear') }} {{ $mail->reply->toUser->username }},</p>
                                                <p>
                                                    {!! html_entity_decode($mail->reply->message) !!}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex mail_box_cnt_row mb-4 ms-5">
                                <div class="flex-shrink-0 me-3">
                                    <img class="rounded-circle avatar-sm"
                                        src="{{ asset('/assets/images/users/avatar-1.jpg') }}"
                                        alt="Generic placeholder image">
                                </div>
                                <div class="mail_contant">
                                    <div class="flex-grow-1 reply_mail_open is-open" id="inbox-mail">
                                        @if ($mail['type'] == 'replica_message')
                                            <h5 class="single_mail_head mt-1">{{ $mail->name }}</h5>
                                            <h4 class="subject_mail_cnt font-size-16">{{ $mail->subject }} <small
                                                    class="text-muted mail_date">{{ $mail->mail_added_date }}</small></h4>
                                        @else
                                            <h5 class="single_mail_head mt-1">{{ $mail->fromUser->username }}</h5>
                                            <h4 class="subject_mail_cnt font-size-16">{{ $mail->subject }} <small
                                                    class="text-muted mail_date">{{ $mail->date }}</small></h4>
                                        @endif
                                    </div>

                                    <div class="reply_mail_content">
                                        @if ($mail['type'] == 'admin_message')
                                            @if ($mail->to_user_id == null)
                                                <p>{{ __('mail.This_mail_are_send_to_all_users') }}</p>
                                            @else
                                                <p>{{ __('mail.dear') }} {{ $mail->toUser->username }},</p>
                                            @endif
                                            <p>
                                                {!! html_entity_decode($mail->message) !!}
                                            </p>
                                        @else
                                            <h6> {{ __('mail.email') }}: {{ $mail->email }}</h6>
                                            <h6> {{ __('mail.address') }}: {{ $mail->address }}</h6>
                                            <h6> {{ __('mail.phone') }}: {{ $mail->phone }}</h6><br>

                                            <p>
                                                {!! html_entity_decode($mail->message) !!}
                                            </p>
                                        @endif


                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>


            </div>


        </div>

    </div>

@endsection
@push('scripts')
    <script>
        function select_user(a) {
            if (a == "single_user") {
                document.getElementById('user').style.display = "block";
            } else {
                document.getElementById('user').style.display = "none";
            }
        }
        $(document).ready(function() {
            let mail = document.getElementById('inbox-mail');
            mail.classList.toggle("is-open");
            let content = mail.nextElementSibling;

            if (content.style.maxHeight) {
                //this is if the accordion is open
                content.style.maxHeight = null;
            } else {
                //if the accordion is currently closed
                content.style.maxHeight = content.scrollHeight + "px";
            }
            console.log(mail);
        });


        const accordionBtns = document.querySelectorAll(".reply_mail_open");
            accordionBtns.forEach((accordion) => {
                accordion.onclick = function() {
                    this.classList.toggle("is-open");

                    let content = this.nextElementSibling;

                    if (content.style.maxHeight) {
                        //this is if the accordion is open
                        content.style.maxHeight = null;
                    } else {
                        //if the accordion is currently closed
                        content.style.maxHeight = content.scrollHeight + "px";
                    }
                };
        });
    </script>
@endpush
