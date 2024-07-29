<div class="mail-list mt-4">
    <a href="{{ route('mailBox') }}" class="{{ Route::currentRouteName() == 'mailBox' ? ' active' : '' }}"><i
            class="mdi mdi-email-outline me-2"></i> {{ __('mail.inbox') }} <span class="ms-1 float-end">
                {{ $toalUnRead ?? '' }}
        </span></a>
    <a href="{{ route('mail.sent') }}" class="{{ Route::currentRouteName() == 'mail.sent' ? ' active' : '' }}"><i
            class="mdi mdi-email-check-outline me-2"></i>{{ __('mail.sent_mail') }}</a>
</div>
