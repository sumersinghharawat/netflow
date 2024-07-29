@foreach ($notifications as $notification)
@php
    $notify = $notification->data;
@endphp
<a href="javascript: void(0);" onclick="readNotification('{{ $notification->id }}')"
    class="text-reset notification-item">
    <div class="d-flex">
        <div class="avatar-xs me-3">
            <span class="avatar-title bg-primary rounded-circle font-size-16">
                {!! $notify['icon'] !!}
            </span>
        </div>

        <div class="flex-grow-1">
            <h6 class="mb-1" key="t-your-order">
                {{ __('notification.' . $notify['title']) }}</h6>
            <div class="font-size-12 text-muted">
                <p class="mb-1" key="t-grammer">{{ $notify['username'] }}
                    {{ __('notification.send') }}
                    {{ __('notification.' . $notify['type']) }}</p>
                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span
                        key="t-min-ago">{{ $notification->created_at->diffForHumans() }}
                    </span></p>
            </div>
        </div>
    </div>
</a>
@endforeach
