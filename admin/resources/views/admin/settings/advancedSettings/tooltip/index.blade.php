@extends('layouts.app')
@section('title', 'Tree-Icon')
@section('content')
    <div class="container-fluid settings_page profile_page">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.advancedSettings.inc.links')
                <div class="alert alert-info alert-dismissible mt-3">
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>

                    <h4><i class="fas fa-network-wired"></i> {{ __('settings.tree_icon') }}</h4>
                    <p class="text-justify">

                        Here you can set what icon to show in the tree. You can show icons based on the profile picture,
                        based on
                        member status (active/inactive), or based on membership package or rank.

                        You can also configure what to show in the tree tooltip.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-5">
                    <div class="col-md-4">
                        <label for="tree-icon">{{ __('settings.tree_icon_based_on') }}:</label>

                        <select id="tree_criteria" name="tree_criteria" onchange="updateConfig(this.value)"
                            class="form-select">
                            <option value="profile_image" @if ($tree_icon_based_on == 'profile_image') selected @endif>
                                {{ __('settings.profile_image') }}</option>
                            <option value="member_status" @if ($tree_icon_based_on == 'member_status') selected @endif>
                                {{ __('settings.member_status') }}</option>

                            <option value="member_pack" @if ($tree_icon_based_on == 'member_pack') selected @endif>
                                {{ __('settings.membership_pack') }}</option>
                            @if ($module_status->rank_status)
                                <option value="rank" @if ($tree_icon_based_on == 'rank') selected @endif>
                                    {{ __('settings.rank') }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row" id="tree_based">

                </div>
                @if ($module_status->mlm_plan == 'Monoline')
                    <div class="form-group file_upload_section package_div row" id="reentry_icon">
                        <label class="fw-bolder">{{ __('settings.reentry_tree_icon') }}</label>
                        <div class="form-group">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <input type="file" id="reentry_tree_icon" name="reentry_tree_icon" value=""
                                            class="reentry_tree_icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="panel panel-default">
                        <div class="card-1">
                            <div class="card-header-1">
                                <strong>{{ __('settings.tooltip_details') }}</strong>
                            </div>
                            <form action="{{ route('tooltip.update') }}" role="form" class="" method="post"
                                name="tooltip_form" id="tooltip_form" accept-charset="utf-8" enctype="multipart/form-data">
                                @csrf

                                <div class="card-body">
                                    @forelse ($tool_tip_items as $key => $item)
                                        @if ($item->slug == 'left' || $item->slug == 'right' || $item->slug == 'left-carry' || $item->slug == 'right-carry')
                                            @if ($module_status->mlm_plan == 'Binary')
                                                <div class="checkbox checkbox-parent">
                                                    <label class="i-checks">
                                                        <input class="checkParent form-check-input"
                                                            data-checkbox="icheckbox_square-blue"
                                                            {{ $item->status == 1 ? 'checked' : '' }} type="checkbox"
                                                            id="{{ $item->id }}" value="{{ $item->id }}"
                                                            name="tooltip[{{ $key + 1 }}]">
                                                        {{ $item->name }}


                                                    </label>
                                                </div>
                                            @endif
                                        @elseif($module_status->mlm_plan == 'Donation' && $item->slug == 'donation-level')
                                            <div class="checkbox checkbox-parent">
                                                <label class="i-checks">
                                                    <input class="checkParent form-check-input"
                                                        data-checkbox="icheckbox_square-blue"
                                                        {{ $item->status == 1 ? 'checked' : '' }} type="checkbox"
                                                        id="{{ $item->id }}" value="{{ $item->id }}"
                                                        name="tooltip[{{ $key + 1 }}]">
                                                    {{ $item->name }}
                                                </label>
                                            </div>
                                        @elseif($module_status->rank_status && $item->slug == 'rank-status')
                                            <div class="checkbox checkbox-parent ">
                                                <label class="i-checks">
                                                    <input class="checkParent form-check-input"
                                                        data-checkbox="icheckbox_square-blue"
                                                        {{ $item->status == 1 ? 'checked' : '' }} type="checkbox"
                                                        id="{{ $item->id }}" value="{{ $item->id }}"
                                                        name="tooltip[{{ $key + 1 }}]">
                                                    {{ $item->name }}
                                                </label>
                                            </div>
                                        @elseif($item->slug != 'donation-level')
                                            <div class="checkbox checkbox-parent">
                                                <label class="i-checks">
                                                    <input class="checkParent form-check-input"
                                                        data-checkbox="icheckbox_square-blue"
                                                        {{ $item->status == 1 ? 'checked' : '' }} type="checkbox"
                                                        id="{{ $item->id }}" value="{{ $item->id }}"
                                                        name="tooltip[{{ $key + 1 }}]">
                                                    {{ $item->name }}
                                                </label>
                                            </div>
                                        @endif
                                    @empty
                                    @endforelse
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary" type="button"
                                        onclick="this.form.submit()">{{ __('common.update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="panel panel-default">
                        <div class="card-1">
                            <div class="card-header-1">
                                <strong>{{ __('settings.tree_size') }}</strong>
                            </div>
                            <form action="{{ route('tree.size.upate') }}" role="form" class="" method="post"
                                name="tooltip_form" id="tooltip_form" accept-charset="utf-8" enctype="multipart/form-data">
                                @method('put')
                                @csrf
                                <div class="card-body row">
                                    <div class="form-group col-lg-2 col-md-2 col-sm-4 col-sx-4" id="ageLimit">
                                        <label class="required">{{ __('settings.tree_depth') }} <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="number" min="4" max="7" class="form-control"
                                            value="{{ $member_status->tree_depth }}" readonly name="depth">
                                        @error('depth')
                                            <span class="text-danger form-text">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-2 col-md-2 col-sm-4 col-sx-4" id="ageLimit">
                                        <label class="required">{{ __('settings.tree_width') }} <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="number" min="3" max="8" class="form-control"
                                            value="{{ $member_status->tree_width }}" name="width">
                                        @error('width')
                                            <span class="text-danger form-text">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-2 col-md-2 col-sm-4 col-sx-4">
                                        <button class="btn btn-primary mt-4"
                                            type="submit">{{ __('common.update') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        $(() => {
            loadTreeIconConfig();
            let plan = "{{ $module_status->mlm_plan }}";
            if (plan === 'Monoline') {
                renderMonolineTreeIcon();
            }
        });
        const renderMonolineTreeIcon = () => {
            try {
                let currentIcon = "{{ $reentry_icon }}";
                FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFilePoster);
                const reentryIcon = document.querySelector('#reentry_tree_icon');
                let url = "{{ route('monoline.treeIcon.update') }}";
                const createPond = FilePond.create(reentryIcon, {
                    allowFileEncode: true,
                    allowImagePreview: true,
                    imagePreviewHeight: 150,
                    credits: false,
                    server: {
                        process: {
                            url: `${url}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                            }
                        },
                    },
                });
                createPond.files = [{
                    source: currentIcon,
                    options: {
                        type: 'local',
                        metadata: {
                            poster: currentIcon
                        },
                    }
                }];

            } catch (error) {
                console.log(error);
            }
        }

        const updateConfig = async (str) => {
            const res = await $.get("{{ route('update.config') }}", {
                'config': `${str}`
            });
            if (typeof res != 'undefined') {
                $("#tree_based").html(' ');
                $("#tree_based").html(res.data.view);
                loadFilePond(res.data.criteria, res.data.image);
            }
        }

        function uploadtype(a) {
            if (a == 'member_pack') {
                $("#membership_package").css("display", "block");
                $("#member_status").css("display", "none");
                $("#profile_image").css("display", "none");
                $("#rank_details").css("display", "none");

            } else if (a == 'rank') {

                $("#membership_package").css("display", "none");
                $("#member_status").css("display", "none");
                $("#profile_image").css("display", "none");
                $("#rank_details").css("display", "block");

            } else if (a == 'member_status') {
                $("#membership_package").css("display", "none");
                $("#member_status").css("display", "block");
                $("#profile_image").css("display", "none");
                $("#rank_details").css("display", "none");

            } else if (a == 'profile_image') {
                $("#membership_package").css("display", "none");
                $("#member_status").css("display", "none");
                $("#profile_image").css("display", "block");
                $("#rank_details").css("display", "none");
            } else {

                $("#membership_package").css("display", "none");
                $("#member_status").css("display", "block");
                $("#profile_image").css("display", "none");
                $("#rank_details").css("display", "none");
            }
        }

        const loadTreeIconConfig = async () => {
            let criteria = document.getElementById('tree_criteria').value;
            let url = `{{ route('update.config') }}`;
            const res = await $.get(`${url}`, {
                'config': `${criteria}`
            });
            if (typeof res != 'undefined') {
                $("#tree_based").html(' ');
                $("#tree_based").html(res.data.view);
                loadFilePond(res.data.criteria, res.data.image);
            }
        }
        const loadFilePond = (criteria, image) => {
            FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFilePoster);
            switch (criteria) {
                case 'member_status':
                    filepondMemberStatus(image);
                    break;
                case 'member_pack':
                    filepondMemberPack(image);
                    break;
                case 'rank':
                    filepondRank(image);
                    break;
                default:
                    break;
            }
        }
        const filepondMemberStatus = (image) => {
            const inActiveUser = document.querySelector('#tree_icon_inactive');
            const activeUser = document.querySelector('#tree_icon_active');
            const pondActive = FilePond.create(activeUser, {
                allowFileEncode: true,
                allowImagePreview: true,
                imagePreviewHeight: 150,
                credits: false,
                server: {
                    process: {
                        url: "{{ route('image.store') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                        }
                    },
                }
            });
            pondActive.files = [{
                source: image.active,
                options: {
                    type: 'local',
                    metadata: {
                        poster: image.active
                    }
                }
            }];
            const pondInactive = FilePond.create(inActiveUser, {
                allowFileEncode: true,
                allowImagePreview: true,
                imagePreviewHeight: 150,
                credits: false,
                server: {
                    process: {
                        url: "{{ route('image.store') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                        }
                    },
                }
            });
            pondInactive.files = [{
                source: image.active,
                options: {
                    type: 'local',
                    metadata: {
                        poster: image.inactive
                    }
                }
            }];
        }

        const filepondMemberPack = (packages) => {
            packages.forEach(package => {
                var uploadUrl = "{{ route('membership-package-image.store', ':packageId') }}";
                uploadUrl = uploadUrl.replace(':packageId', package.id);
                const pond = FilePond.create(document.querySelector(`#membership-pack-image-${package.id}`), {
                    allowFileEncode: true,
                    allowImagePreview: true,
                    imagePreviewHeight: 150,
                    credits: false,
                    server: {
                        process: {
                            url: `${uploadUrl}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                            }
                        },
                    }
                });
                pond.files = [{
                    source: package.tree_icon,
                    options: {
                        type: 'local',
                        metadata: {
                            poster: package.tree_icon
                        }
                    }
                }];
            });

        }
        const filepondRank = (ranks) => {
            ranks.forEach(rank => {
                var uploadUrl = "{{ route('rank.details.update', ':rankId') }}";
                uploadUrl = uploadUrl.replace(':rankId', rank.id);
                const pond = FilePond.create(document.querySelector(`#rank-pic-${rank.id}`), {
                    allowFileEncode: true,
                    allowImagePreview: true,
                    imagePreviewHeight: 150,
                    credits: false,
                    server: {
                        process: {
                            url: `${uploadUrl}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                            }
                        },
                    }
                });
                if(rank.tree_icon) {
                    pond.files = [
                        {
                            source: rank.tree_icon,
                            options: {
                                type: 'local',
                                metadata: {
                                    poster: rank.tree_icon
                                }
                            }
                        }
                    ];
                }
            });

        }
    </script>
@endpush
