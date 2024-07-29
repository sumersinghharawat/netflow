@extends('layouts.app')
@section('title', 'User-Dashboard')

@section('content')
    <div class="container-fluid settings_page profile_page">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.advancedSettings.inc.links')
                <div class="panel-default">
                    <div class="panel-body">
                        <div class="alert alert-info alert-dismissible mt-3">
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                            <h4><i class="fas fa-desktop"></i>{{ __('settings.user_dashboard') }}</h4>
                            <p>Here you can choose which sections to be shown in the user dashboard.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('userdashboard.update') }}" role="form" class="" method="post"
                    name="signup_form" id="profile_form" accept-charset="utf-8" onsubmit="updateUserdash(this)">
                    <noscript>
                        @csrf
                        @method('put')
                    </noscript>

                    @forelse($dashboarditems as $key => $item)
                        @if ($moduleStatus->mlm_plan == 'Donation' && $item->slug == 'donation')
                            <div class="checkbox checkbox-parent userDashboard" id="userDashboard">
                                <label class="i-checks">
                                    <input class="checkParent" data-checkbox="icheckbox_square-blue"
                                        {{ $item->status == 1 ? 'checked' : '' }} type="checkbox" id="{{ $item->id }}"
                                        value="{{ $item->id }}" name="parent[{{ $key }}]">
                                    {{ $item->name }}
                                </label>
                                @forelse ($item->children as $child)
                                    <div class="form-group">
                                        <ul>
                                            <label class="i-checks">
                                                <input class="checkParent-{{ $item->id }}"
                                                    data-checkbox="icheckbox_square-blue"
                                                    {{ $child->status == 1 ? 'checked' : '' }} type="checkbox"
                                                    value="{{ $child->id }}" name="child[{{ $child->id }}]"
                                                    id="{{ $child->parent_id }}">

                                                {{ $child->name }}
                                            </label>
                                        </ul>
                                    </div>
                                @empty
                                @endforelse

                            </div>
                        @elseif($item->slug != 'donation')
                            <div class="checkbox checkbox-parent userDashboard" id="userDashboard">
                                <label class="i-checks">
                                    @if ($moduleStatus->rank_status && $item->slug == 'rank')
                                        <input class="checkParent" data-checkbox="icheckbox_square-blue"
                                            {{ $item->status == 1 ? 'checked' : '' }} type="checkbox"
                                            id="{{ $item->id }}" value="{{ $item->id }}"
                                            name="parent[{{ $key }}]">
                                        {{ $item->name }}
                                    @elseif($item->slug != 'rank')
                                        <input class="checkParent" data-checkbox="icheckbox_square-blue"
                                            {{ $item->status == 1 ? 'checked' : '' }} type="checkbox"
                                            id="{{ $item->id }}" value="{{ $item->id }}"
                                            name="parent[{{ $key }}]" @disabled($item->slug == 'e-wallet' ||
                                                    $item->slug == 'commission-earned' ||
                                                    $item->slug == 'payout-released' ||
                                                    $item->slug == 'payout-pending' ||
                                                    $item->slug == 'profile-membership-replica-lcp' ||
                                                    $item->slug == 'sponsor-pv-carry')>
                                        {{ $item->name }}
                                        @if ($item->slug == 'e-wallet' ||
                                            $item->slug == 'commission-earned' ||
                                            $item->slug == 'payout-released' ||
                                            $item->slug == 'payout-pending' ||
                                            $item->slug == 'profile-membership-replica-lcp' ||
                                            $item->slug == 'sponsor-pv-carry')
                                            <input class="checkParent" data-checkbox="icheckbox_square-blue" type="hidden"
                                                id="{{ $item->id }}" value="{{ $item->id }}"
                                                name="parent[{{ $key }}]">
                                        @endif
                                    @endif

                                </label>
                                @forelse ($item->children as $child)
                                    <div class="form-group">
                                        <ul>
                                            @if ($moduleStatus->rank_status && $child->slug == 'rank-overview')
                                                <label class="i-checks">
                                                    <input class="checkParent-{{ $item->id }}"
                                                        data-checkbox="icheckbox_square-blue"
                                                        {{ $child->status == 1 ? 'checked' : '' }} type="checkbox"
                                                        value="{{ $child->id }}" name="child[{{ $child->id }}]"
                                                        id="{{ $child->parent_id }}">

                                                    {{ $child->name }}
                                                </label>
                                            @elseif($child->slug != 'rank-overview')
                                                <label class="i-checks">
                                                    <input class="checkParent-{{ $item->id }}"
                                                        data-checkbox="icheckbox_square-blue"
                                                        {{ $child->status == 1 ? 'checked' : '' }} type="checkbox"
                                                        value="{{ $child->id }}" name="child[{{ $child->id }}]"
                                                        id="{{ $child->parent_id }}">

                                                    {{ $child->name }}
                                                </label>
                                            @endif
                                        </ul>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        @endif
                    @empty
                        <div class="nodata_view">
                            <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                            <span>{{ __('common.no_data') }}</span>
                        </div>
                    @endforelse

                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">{{ __('common.update') }}</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function updateUserdash(form) {
            event.preventDefault()
            let data = getForm(form)
            data._method = "put";

            let url = form.action
            console.log(data);
            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        formvalidationError(form, err)
                        // notifyError(err.message)
                    }
                })
            notifySuccess(res.message)
            // console.log(res)
            // console.log(formData);
        }

        var checkboxHandlerObj = {
            init: function() {
                $('.userDashboard input:checkbox[class="checkParent"]').click(checkboxHandlerObj.parentClicked);
                $('.userDashboard input:checkbox[class^="checkParent-"]').click(checkboxHandlerObj.childClicked)
            },
            parentClicked: function() {
                var parentCheck = this.checked;
                $('.userDashboard input:checkbox[class="checkParent-' + $(this).attr('id') + '"]').each(function() {
                    this.checked = parentCheck
                });
            },
            childClicked: function() {
                var temp = $(this).attr('class').split('-');
                var parentId = temp[1];
                $('#' + parentId)[0].checked = $('.userDashboard input:checkbox[class="' + $(this).attr('class') +
                    '"]:checked').length !== 0;
            }
        }
        checkboxHandlerObj.init();
    </script>
@endpush
