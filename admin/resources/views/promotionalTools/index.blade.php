@extends('layouts.app')
@section('title', __('tools.promotional_tools'))

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('tools.promotional_tools') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card business-card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#text-invites-tab" role="tab"
                                onclick="checkEmptyStatus('text')">
                                <span class="d-none d-sm-block">
                                    {{ __('tools.text_invites') }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#banner-invites-tab" role="tab"
                                onclick="checkEmptyStatus('banner')">
                                <span class="d-none d-sm-block">
                                    {{ __('tools.banner_invites') }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#social-invites-tab" role="tab"
                                onclick="checkEmptyStatus('social')">
                                <span class="d-none d-sm-block">
                                    {{ __('tools.social_invites') }}
                                </span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content text-muted">
                        <div class="tab-pane active" id="text-invites-tab" role="tabpanel">
                            <div class="col-md-12">
                                <div class="float-end">
                                    <a class="btn  btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                                        href="#addTextInvite" role="button" aria-controls="addTextInvite"
                                        style="margin-top:10px;z-index:999;">
                                        <i
                                            class="bx bx-plus  font-size-16 align-middle me-2"></i>{{ __('tools.add_textInvite') }}
                                    </a>
                                </div>
                            </div>

                            @if (isset($invites_data) && $invites_data)
                                @foreach ($invites_data as $value)
                                    @if ($value->type == 'text')
                                        <div class="subject-text-bg-box">
                                            <div class="subject-head-text">
                                                <h3>{{ $value->subject }}</h3>
                                            </div>
                                            <hr>
                                            <div class="promotional-date-text">
                                                <h6>{{ $value->created_at->format('m/d/Y') }}</h6>
                                            </div>
                                            <textarea id="{{ 'copy_content' . $value->id }}" class="subject-sub-text" readonly>{{ "<a href='" . config('mlm.user_replica_url') . "'>" . $value->content . '</a>' }}</textarea>
                                            <div class="promotional-btn-action-sec">
                                                <div class="promotional-btn-sec">
                                                    <button class="btn btn-primary" type="button"
                                                        onclick="copyClipBoard('{{ 'copy_content' . $value->id }}')"
                                                        title="{{ __('common.click_copy_to_clipboard') }}">{{ __('dashboard.copy') }}</button>
                                                </div>

                                                <div class="delete-edit-section">
                                                    <div class="edit-circle-section">
                                                        <a data-bs-toggle="offcanvas" href="#editPromotionalTool"
                                                            role="button" onclick="loadEditmodal({{ $value }})"
                                                            aria-controls="editPromotionalTool">
                                                            <i class="far fa-edit"></i>
                                                        </a>
                                                    </div>
                                                    <div class="delete-circle-section">
                                                        <a onclick="deleteInvite({{ $value->id }})">
                                                            <i class="mdi mdi-trash-can"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="nodata_view" id="text_nodata_view" style="display: none">
                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                    <span>{{ __('common.no_data') }}</span>
                                </div>
                            @else
                                <div class="nodata_view">
                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                    <span>{{ __('common.no_data') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane" role="tabpanel" id="banner-invites-tab">
                            <div class="col-md-12">
                                <div class="float-end">
                                    <a class="btn  btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                                        href="#addBannerInvite" role="button" aria-controls="addBannerInvite" style="z-index:999;">
                                        <i
                                            class="bx bx-plus  font-size-16 align-middle me-2"></i>{{ __('tools.add_bannerInvite') }}
                                    </a>
                                </div>
                            </div>

                            @if (isset($invites_data) && $invites_data)
                                @foreach ($invites_data as $value)
                                    @if ($value->type == 'banner')
                                        <div class="banner-invite-section">
                                            <div class="banner-invite-bg-box">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="banner-invite-img-sec">
                                                            <img src="{{ $value->content }}" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="subject-text-bg-box">
                                                            <div class="subject-head-text">
                                                                <h3>{{ $value->subject }}</h3>
                                                            </div>
                                                            <hr>
                                                            <div class="promotional-date-text">
                                                                <h6>{{ $value->created_at->format('m/d/Y') }}</h6>
                                                            </div>
                                                            <div class="subject-sub-text">
                                                                <textarea id="{{ 'copy_content' . $value->id }}" class="subject-sub-text" readonly>{{ "<a href='" . $value->target_url . "'><img src='" . $value->content . "'> </a>" }}</textarea>
                                                            </div>
                                                            <div class="promotional-btn-action-sec">
                                                                <div class="promotional-btn-sec">
                                                                    <button class="btn btn-primary" type="button"
                                                                        onclick="copyClipBoard('{{ 'copy_content' . $value->id }}')"
                                                                        title="{{ __('common.click_copy_to_clipboard') }}">{{ __('dashboard.copy') }}</button>
                                                                </div>

                                                                <div class="delete-circle-section">
                                                                    <a onclick="deleteInvite({{ $value->id }})">
                                                                        <i class="mdi mdi-trash-can"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endif
                                @endforeach
                                <div class="nodata_view" id="banner_nodata_view" style="display: none">
                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                    <span>{{ __('common.no_data') }}</span>
                                </div>
                            @else
                                <div class="nodata_view">
                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                    <span>{{ __('common.no_data') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane" id="social-invites-tab" role="tabpanel">
                            <div class="col-md-12">
                                <div class="float-end">
                                    <div class="btn-group" role="group" style="margin-top: 10px">
                                        <span class="btn btn-primary">{{ __('tools.add_socialInvite') }}</span>
                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            id="btnGroupVerticalDrop1" style="z-index:999;">
                                            <i class="mdi mdi-chevron-down"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop1">
                                            <a class="dropdown-item" href="#addSocialInvite" data-bs-toggle="offcanvas"
                                                role="button" aria-controls="addSocialInvite"
                                                onclick="changeTitle('{{ __('tools.social_email') }}','social_email')">{{ __('tools.social_email') }}</a>
                                            <a class="dropdown-item" href="#addSocialInvite" data-bs-toggle="offcanvas"
                                                role="button" aria-controls="addSocialInvite"
                                                onclick="changeTitle('{{ __('tools.social_facebook') }}','social_facebook')">{{ __('tools.social_facebook') }}</a>
                                            <a class="dropdown-item" href="#addSocialInvite" data-bs-toggle="offcanvas"
                                                role="button" aria-controls="addSocialInvite"
                                                onclick="changeTitle('{{ __('tools.social_twitter') }}','social_twitter')">{{ __('tools.social_twitter') }}</a>
                                            <a class="dropdown-item" href="#addSocialInvite" data-bs-toggle="offcanvas"
                                                role="button" aria-controls="addSocialInvite"
                                                onclick="changeTitle('{{ __('tools.social_instagram') }}','social_instagram')">{{ __('tools.social_instagram') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (isset($invites_data) && $invites_data)
                                @foreach ($invites_data as $value)
                                    @if ($value->type != 'text' && $value->type != 'banner')
                                        <div class="social-invite-section">
                                            <div class="social-invite-bg-box">

                                                <div class="social-subject-sub-sec">
                                                    <div class="social-media-icon-bg-circle">
                                                        <img src="{{ asset('/assets/images/social-media/' . $value->type . '.png') }}"
                                                            alt="">
                                                    </div>

                                                    <div class="subject-head-text">
                                                        <h3>{{ $value->subject }}</h3>
                                                        <p>{{ __('tools.' . $value->type) }}</p>
                                                    </div>
                                                </div>

                                                <div class="subject-sub-text">
                                                    {!! $value->content !!}
                                                </div>

                                                <div class="delete-edit-section">
                                                    <div class="edit-circle-section">
                                                        <a data-bs-toggle="offcanvas" href="#editPromotionalTool"
                                                            role="button" onclick="loadEditmodal({{ $value }})"
                                                            aria-controls="editPromotionalTool">
                                                            <i class="far fa-edit"></i>
                                                        </a>
                                                    </div>
                                                    <div class="delete-circle-section">
                                                        <a onclick="deleteInvite({{ $value->id }})">
                                                            <i class="mdi mdi-trash-can"></i>
                                                        </a>
                                                    </div>
                                                    @if ($value->type != 'social_email')
                                                        <div class="edit-circle-section" style="margin-left: 10px">
                                                            <a
                                                                onclick="shareUrl('{{ $value->type }}','{{ urlencode($value->content) }}')">
                                                                <i class="mdi mdi-share"></i>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="nodata_view" id="social_nodata_view" style="display: none">
                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                    <span>{{ __('common.no_data') }}</span>
                                </div>
                            @else
                                <div class="nodata_view">
                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                    <span>{{ __('common.no_data') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('promotionalTools.modal')
@endsection
@push('scripts')
    <script>
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            let activeTab = $(e.target).attr('href');
            localStorage.setItem('activeTab', activeTab);
        });
        $( () => {
            let activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('.nav-link[href="' + activeTab + '"]').tab('show');
            }
        });
        const changeTitle = (title, type) => {
            try {
                $('#addBaner').html(title)
                document.getElementById("socialType").value = type
            } catch (error) {
                console.log(error);
            }
        }
        const addInvites = async (form) => {
            event.preventDefault()
            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()
            let url = form.action
            let data = new FormData(form)
            $.ajax({
                type: 'POST',
                enctype: 'multipart/form-data',
                url,
                data,
                processData: false,
                contentType: false,
                cache: false,
            }).catch((err) => {
                if (err.status === 422) {
                    formvalidationError(form, err)
                    if (err.responseJSON.message == 'The content field is required.')
                        notifyError('The message field is required.')
                }
            }).then((res) => {
                if (typeof(res) != "undefined") {
                    var activeTab = $('.nav-link.active').attr('href');
                    localStorage.setItem('activeTab', activeTab);
                    location.reload();
                    notifySuccess(res.message)
                }
            })
        }
        const loadEditmodal = (data) => {
            try {
                document.getElementById("subject").value = data.subject;
                document.getElementById("inviteType").value = data.type;
                document.getElementById("inviteId").value = data.id;
                $('#content').summernote('code', data.content);
            } catch (error) {
                console.log(error);
            }
        }
        const editInvites = async (form) => {
            event.preventDefault()
            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()
            let url = form.action
            let data = new FormData(form)
            $.ajax({
                type: 'POST',
                enctype: 'multipart/form-data',
                url,
                data,
                processData: false,
                contentType: false,
                cache: false,
            }).catch((err) => {
                if (err.status === 422) {
                    formvalidationError(form, err)
                    if (err.responseJSON.message == 'The content field is required.')
                        notifyError('The Message field is required.')
                }
            }).then((res) => {
                if (typeof(res) != "undefined") {
                    var activeTab = $('.nav-link.active').attr('href');
                    localStorage.setItem('activeTab', activeTab);
                    location.reload();
                    notifySuccess(res.message)
                }
            })
        }

        const deleteInvite = async (id) => {
            $.ajax({
                type: 'post',
                url: '{{ URL::to('admin/delete-invites') }}',
                data: {
                    'id': id
                },
                success: function(res) {
                    var activeTab = $('.nav-link.active').attr('href');
                    localStorage.setItem('activeTab', activeTab);
                    if (res.status) {
                        notifySuccess(res.message)
                        location.reload()
                    } else {
                        notifyError(res.message)
                    }
                }
            });
        }

        const checkEmptyStatus = async (invite = 'text') => {
            var emptyView = invite + '_nodata_view'
            $.ajax({
                type: 'post',
                url: '{{ URL::to('admin/invites-emptyStatus') }}',
                data: {
                    'type': invite
                },
                success: function(res) {
                    if (res.status == 'false') {
                        document.getElementById(emptyView).style.display = "block";
                    } else {
                        document.getElementById(emptyView).style.display = "none";
                    }
                }
            });
        }
        $(() => {
            checkEmptyStatus('text')
        });

        const shareUrl = (type, content) => {
            try {
                if (type == 'social_facebook') {
                    var url = "http://www.facebook.com/sharer/sharer.php?s=100&p[url]=" + encodeURI(content)
                } else if (type == 'social_instagram') {
                    var url = "http://instagram.com/home?status=" + encodeURI(content);
                } else if (type == 'social_twitter') {
                    var url = "http://twitter.com/share?text=" + encodeURI(content);
                }
                window.location.replace(url);
            } catch (error) {
                console.log(error)
            }
        }
    </script>
@endpush
