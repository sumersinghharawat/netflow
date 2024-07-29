@extends('layouts.app')
@section('title', trans('common.rank'))
@section('content')
    <div class="container-fluid settings_page">
        <div class="card">
            <div class="card-header ">
                @include('admin.settings.inc.links')
                <div class="">
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        <span class="fw-bolder text-primary">
                            <h4><i class="dripicons-trophy me-2"></i>{{ __('rank.rank_settings') }}</h4>
                        </span>
                        <p class="text-justify mt-lg-2">
                            {{ __('rank.rank_description') }}
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('rank.updateConfig') }}" method="post" class="mt-3" onsubmit="updateConfig(this)">
                    <noscript>
                        @csrf
                        @method('put')
                    </noscript>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">{{ __('rank.rank_calculation_period') }}</label>
                                <select class="form-select" name="calculation" id="rank_expiry">
                                    <option value="daily"
                                        {{ $configuration->rank_calculation == 'daily' ? 'selected' : '' }}>
                                        {{ __('common.daily') }}
                                    </option>
                                    <option value="weekly"
                                        {{ $configuration->rank_calculation == 'weekly' ? 'selected' : '' }}>
                                        {{ __('common.weekly') }}
                                    </option>
                                    <option value="monthly"
                                        {{ $configuration->rank_calculation == 'monthly' ? 'selected' : '' }}>
                                        {{ __('common.monthly') }}</option>
                                    <option value="yearly"
                                        {{ $configuration->rank_calculation == 'yearly' ? 'selected' : '' }}>
                                        {{ __('common.yearly') }}
                                    </option>
                                    <option value="instant"
                                        {{ $configuration->rank_calculation == 'instant' ? 'selected' : '' }}>
                                        {{ __('common.instant') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="text-danger">{{ __('rank.rank_criteria') }}</label>
                                <div class="row">
                                    @forelse($rankConfig as $index => $config)
                                        @if (in_array($config->slug, ['downline-member-count', 'downline-package-count', 'downline-rank']))
                                            @if ($moduleStatus->mlm_plan == 'Binary' || $moduleStatus == 'Matrix')
                                                <div
                                                    class="col-12 col-md-4 @if (in_array($config->slug, [
                                                            'referral-count',
                                                            'personal-pv',
                                                            'group-pv',
                                                            'downline-member-count',
                                                            'downline-package-count',
                                                            'downline-rank-count',
                                                        ])) rank_criteria @endif">
                                                    <div class="checkbox"> <label class="i-checks">
                                                            <input type="checkbox" name="rankconfig[{{ $index }}]"
                                                                class="form-check-input @if ($config->slug != 'joiner-package') disable_input @endif"
                                                                @if ($config->slug == 'joiner-package') id="joinee_pck" @endif
                                                                value="{{ $config->id }}"
                                                                {{ $config->status == 1 ? 'checked' : '' }}><i>
                                                            </i>{{ __('rank.' . $config->slug) }}</label>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <div
                                                class="col-12 col-md-4 @if (in_array($config->slug, [
                                                        'referral-count',
                                                        'personal-pv',
                                                        'group-pv',
                                                        'downline-member-count',
                                                        'downline-package-count',
                                                        'downline-rank-count',
                                                    ])) rank_criteria @endif">
                                                <div class="checkbox"> <label class="i-checks">
                                                        <input type="checkbox" name="rankconfig[{{ $index }}]"
                                                            class="form-check-input @if ($config->slug != 'joiner-package') disable_input @endif"
                                                            @if ($config->slug == 'joiner-package') id="joinee_pck" @endif
                                                            value="{{ $config->id }}"
                                                            {{ $config->status == 1 ? 'checked' : '' }}><i>
                                                        </i>{{ __('rank.' . $config->slug) }}</label>
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                        <p>{{ __('common.no_data') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
                <div class="mt-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex align-items-center">
                                <div class="col-md-6">
                                    <h4 class="card-title mb-0">{{ __('rank.rank_details') }}</h4>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-end">
                                        <a class="btn  btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                                            href="#addNewRank" role="button" aria-controls="addNewRank"><i
                                                class="bx bx-plus  font-size-16 align-middle me-2"></i>{{ __('rank.add_rank') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="table-responsive mt-1">
                            <table class="table rankTable" id="rankTable">
                                <thead>
                                    <th>#</th>
                                    <th>{{ __('rank.name') }}</th>
                                    @if ($activeConfig->contains('slug', 'joiner-package'))
                                        <th>{{ __('rank.package_name') }}</th>
                                    @else
                                        @if ($activeConfig->contains('slug', 'referral-count'))
                                            <th>{{ __('rank.referral-count') }}</th>
                                        @endif
                                        @if ($activeConfig->contains('slug', 'personal-pv'))
                                            <th>{{ __('rank.personal-pv') }}</th>
                                        @endif
                                        @if ($activeConfig->contains('slug', 'group-pv'))
                                            <th>{{ __('rank.group-pv') }}</th>
                                        @endif
                                        @if ($activeConfig->contains('slug', 'downline-member-count'))
                                            <th>{{ __('rank.downline-member-count') }}</th>
                                        @endif
                                        @if ($activeConfig->contains('slug', 'downline-package-count'))
                                            <th>{{ __('rank.downline-package-count') }}</th>
                                        @endif
                                        @if ($activeConfig->contains('slug', 'downline-rank-count'))
                                            <th>{{ __('rank.downline-rank-count') }}</th>
                                        @endif
                                    @endif
                                    <th>{{ __('rank.commission') }}</th>
                                    <th>{{ __('rank.color') }}</th>
                                    <th>{{ __('rank.badge') }}</th>
                                    <th>{{ __('common.action') }}</th>
                                </thead>
                                <tbody>
                                    @foreach ($ranks as $rank)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $rank->name }}</td>
                                            @if ($activeConfig->contains('slug', 'joiner-package'))
                                                <td>
                                                    {{ $rank->rankCriteria->name ?? ($rank->rankCriteria->model ?? 'NA') }}
                                                </td>
                                            @else
                                                @if ($activeConfig->contains('slug', 'referral-count'))
                                                    <td>
                                                        {{ $rank->rankCriteria->referral_count ?? '' }}
                                                    </td>
                                                @endif
                                                @if ($activeConfig->contains('slug', 'personal-pv'))
                                                    <td>
                                                        {{ $rank->rankCriteria->personal_pv ?? '' }}
                                                    </td>
                                                @endif
                                                @if ($activeConfig->contains('slug', 'group-pv'))
                                                    <td>
                                                        {{ $rank->rankCriteria->group_pv ?? '' }}
                                                    </td>
                                                @endif
                                                @if ($activeConfig->contains('slug', 'downline-member-count'))
                                                    <td>
                                                        {{ $rank->rankCriteria->downline_count ?? '' }}
                                                    </td>
                                                @endif
                                                @if ($activeConfig->contains('slug', 'downline-package-count'))
                                                    <td>
                                                        @forelse($rank->downinePackCount as $downlinePack)
                                                            <li>
                                                                {{ $downlinePack->name }} :
                                                                {{ $downlinePack->pivot->count }}
                                                            </li>
                                                        @empty
                                                            <small>{{ __('common.no_data') }}</small>
                                                        @endforelse
                                                    </td>
                                                @endif
                                                @if ($activeConfig->contains('slug', 'downline-rank-count'))
                                                    <td>
                                                        @forelse($rank->downlineRankCount as $downlineRank)
                                                            <li>
                                                                {{ $downlineRank->name }} :
                                                                {{ $downlineRank->pivot->count }}
                                                            </li>

                                                        @empty
                                                            <small>{{ __('common.no_data') }}</small>
                                                        @endforelse
                                                    </td>
                                                @endif
                                            @endif

                                            <td>
                                                {{ $currency . ' ' . formatCurrency($rank->commission) }}
                                            </td>
                                            <td><button type="button" style="background-color: {{ $rank->color }}"
                                                    class="img-thumbnail rounded-circle avatar-sm"></button>
                                            </td>
                                            <td>
                                                @if ($rank->image && isFileExists($rank->image))
                                                    <img src="{{ $rank->image }}" class="img-thumbnail rounded-circle avatar-sm">
                                                @else
                                                    <img src="{{ asset('assets/images/rank/default-rank.png') }}" class="img-thumbnail rounded-circle avatar-sm">
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $rank->id }}" data-id="{{ $rank->id }}"
                                                        id="SwitchCheckSizemd"
                                                        @if ($rank->status == 'Active') checked @endif
                                                        onchange="toggleRank(this)">
                                                    @if ($rank->status == 'Active')
                                                        <a class="btn btn-outline-primary btn-sm edit"
                                                            id="edit-btn-{{ $rank->id }}" title="Edit"
                                                            href="{{ route('rank.edit', $rank->id) }}"
                                                            onclick="editRank(this)">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                    @endif
                                                </div>

                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
    @include('admin.settings.rank.inc.modal', [
        $rankConfig,
        $activeConfig,
        'packages' => $package,
        $moduleStatus,
        $ranks,
    ])
    <div id="editRankCanvas">
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            if ($('#joinee_pck').prop('checked') == true) {
                $('.disable_input').prop('checked', false)
                $(".rank_criteria").hide()
            }

            $('#joinee_pck').on('change', function() {
                var check = $('#joinee_pck').val();
                if (this.checked) {
                    $('.disable_input').prop('checked', false)
                    $(".rank_criteria").hide()
                } else {
                    $(".rank_criteria").show()
                }
            });

            let url = "{{ route('rank') }}"
            if (window.location.href == url) {
                $("#compensation").removeClass('active');
                $("#rank").addClass('active');
                $("#commission").removeClass('active');
                $("#payout").removeClass('active');
                $("#payment").removeClass('active');
                $("#signup").removeClass('active');
                $("#mail").removeClass('active');
                $("#api").removeClass('active');
                $("#subscription").removeClass('active');
            }
        });

        const createRank = async (form) => {
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
                } else if (err.status === 401) {
                    let errors = err.responseJSON.errors;
                    notifyError(errors)
                }
            }).then((res) => {
                if (typeof(res) != "undefined") {
                    form.reset()
                    $('#rankTable tr:last').after(res.data)
                    let totalTr = $('#rankTable tr').length
                    $('#rankTable tr:last').children("td:first").html(totalTr - 1)
                    $('#addNewRank').offcanvas('hide')
                    notifySuccess(res.message)
                }
            })
        }

        const toggleRank = async (checkbox) => {
            let rankId = checkbox.value
            let url = `{{ route('rankStatus.update') }}/${rankId}`
            let id = checkbox.dataset.id
            let status = (checkbox.checked) ? 'Active' : 'Disabled';
            (status == 'Active') ?
            $(`#edit-btn-${id}`).removeClass('d-none'): $(`#edit-btn-${id}`).addClass('d-none')

            const res = await $.post(`${url}`, {
                _method: 'PATCH',
            }).catch((err) => {
                if (err.status == 422) {
                    alert(422)
                } else if (err.status === 401) {
                    let errors = err.responseJSON.errors;
                    notifyError(errors)
                }
            })
            notifySuccess(res.message)
        }

        const editRank = async (href) => {
            event.preventDefault()
            let url = href.href
            const res = await $.post(`${url}`, {
                '_method': 'PUT'
            }).catch((err) => {
                console.log(err)
            })
            $('#editRankCanvas').html(res.data)
            $('#editRank').offcanvas('show')
        }

        const updateRank = async (form) => {
            event.preventDefault()
            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = new FormData(form);
            $.ajax({
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    url,
                    data,
                    processData: false,
                    contentType: false,
                    cache: false,
                })
                .catch((err) => {
                    if (err.status === 422) {
                        formvalidationError(form, err)
                    } else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                }).then((res) => {
                    if (typeof(res) != "undefined") {
                        form.reset()
                        $('#rankTable tbody').html(res.data)
                        $('#editRank').offcanvas('hide')
                        notifySuccess(res.message)
                    }
                })
        }
        const updateConfig = async (form) => {
            event.preventDefault()
            let url = form.action
            let data = getForm(form)
            data._method = 'put'
            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    console.log(err);
                    if (err.status === 422) {
                        notifyError(err.responseJSON.message)
                    } else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                })
            if (typeof(res) != "undefined") {
                $('#rankTable').html(res.data)
                notifySuccess(res.message)
            }
        }
        const getPrev = (file) => {
            let fileReader = new FileReader()
            fileReader.onload = (e) => {
                $('.img-prev').attr('src', e.target.result).removeClass('invisible')
                $('.rnk-img').addClass('d-none')
            }
            fileReader.readAsDataURL(file.files[0]);
        }

        const unlinkFile = async (rank) => {
            let url = `/admin/rank-unlink-file/${rank}`
            console.log(url)
            const res = await $.post(`${url}`, {
                    '_method': 'delete'
                })
                .catch((err) => {
                    if (err.status === 422) {
                        notifySuccess(res.message)
                    } else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                })
            if (typeof(res) != "undefined") {
                $('#rankTable tbody').html(res.data)
                $('.rnk-img').addClass('d-none')
                $('.icon-delete').addClass('d-none')

                notifySuccess(res.message)
            }
        };
    </script>
@endpush
