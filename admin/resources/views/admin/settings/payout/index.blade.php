@extends('layouts.app')
@section('title', __('payoutSettings.payout_settings'))
@section('content')
    <div class="container-fluid settings_page">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.inc.links')
                <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                    <h4>
                        <h4>
                            <i class="fas fa-chart-line"></i>
                            {{ __('payoutSettings.payout_settings') }}
                        </h4>
                    </h4>
                    <p class="text-justify mt-lg-2">
                        {{ __('payoutSettings.description') }}
                    </p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('payout.update') }}" method="post" class="mt-3">
                    @csrf
                    <div class="row">
                        <div class="form-group col-12 col-md-4">
                            <label>{{ __('payoutSettings.min_amount') }}</label>
                            <div class="input-group">
                                <div class="input-group-text">{{ $currency }}</div>
                                <input type="number" name="min_payout"
                                    class="form-control @error('min_payout') is-invalid @enderror"
                                    value="{{ formatCurrency($configuration['min_payout']) }}" min="0">
                            </div>
                            <span class="text-danger form-text"></span>
                            @error('min_payout')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label>{{ __('payoutSettings.max_amount') }}</label>
                            <div class="input-group">
                                <div class="input-group-text">{{ $currency }}</div>
                                <input type="number" name="max_payout"
                                    class="form-control @error('max_payout') is-invalid @enderror"
                                    value="{{ formatCurrency($configuration['max_payout']) }}" min="0">
                            </div>
                            @error('max_payout')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label>{{ __('payoutSettings.fee_mode') }}</label>
                            <select class="form-select @error('fee_mode') is-invalid @enderror" name="fee_mode" onchange="changePayoutFee(this)">
                                <option {{ $configuration['fee_mode'] == 'percentage' ? 'Selected' : '' }}
                                    value="percentage">
                                    {{ __('common.percentage') }}</option>
                                <option {{ $configuration['fee_mode'] == 'flat' ? 'Selected' : '' }} value="flat">
                                    {{ __('common.flat') }}
                                </option>
                            </select>
                            @error('fee_mode')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12 col-md-4">
                            <label>{{ __('payoutSettings.fee_amount') }}</label>
                            <div class="input-group">
                                <div class="input-group-text">{{ ($configuration['fee_mode'] == 'flat') ? $currency : "%" }}</div>
                                <input type="text" name="fee_amount"
                                    class="form-control @error('fee_amount') is-invalid @enderror"
                                    value={{ formatCurrency($configuration['fee_amount']) }}>
                            </div>
                            @error('fee_amount')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-md-4 d-none">
                            <label>{{ __('payoutSettings.validity') }}</label>
                            <input type="number" value={{ $configuration['request_validity'] }} name="request_validity"
                                class="form-control @error('request_validity') is-invalid @enderror" value=""
                                min="0">
                            @error('request_validity')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label>{{ __('payoutSettings.method') }}</label>
                            <select class="form-select @error('release_type') is-invalid @enderror" name="release_type"
                                id="payout_status">
                                <option value="from_ewallet"
                                    {{ $configuration['release_type'] == 'from_ewallet' ? 'selected' : '' }}>
                                    {{ __('payoutSettings.by_admin') }}
                                </option>
                                <option value="ewallet_request"
                                    {{ $configuration['release_type'] == 'ewallet_request' ? 'selected' : '' }}>
                                    {{ __('payoutSettings.by_user') }}
                                </option>
                                <option value="both" {{ $configuration['release_type'] == 'both' ? 'selected' : '' }}>
                                    {{ __('payoutSettings.by_both') }}</option>
                            </select>
                            @error('release_type')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="checkbox" @if ($configuration['mail_status']) checked @endif name="mail_status"
                            value="1">
                        <label>{{ __('payoutSettings.mail_status') }}</label>
                        <span class="text-danger form-text"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                    </div>
                </form>
                <div class="py-3">
                    <div class="alert alert-info fade show border-start" role="alert">
                        <span class="fw-bolder text-primary">
                            <h4>{{ __('payoutSettings.gateway_config') }}</h4>
                        </span>
                        <p class="text-justify mt-lg-2">
                            {{ __('payoutSettings.gateway_description') }}
                        </p>
                    </div>
                </div>
                <div class="row">
                    <form action="{{ route('paymentmethod.update') }}" method="post">
                        @csrf
                        <table class="table  settings_table">
                            <tr>
                                <th>{{ __('common.payment_method') }}</th>
                                <th>{{ __('common.status') }}</th>
                            </tr>
                            @foreach ($payment_gateway as $key => $value)
                                <tr>
                                    <td>{{ __('common.' . $value->slug) }}</td>
                                    <td>
                                        <input class="form-check-input" type="checkbox"
                                            @if ($value['payout_status'] == 1) checked @endif role="switch"
                                            name="method[]" value="{{ $value['id'] }}">
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <br>
                        @if (count($payment_gateway))
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                        </div>
                        @endif
                    </form>
                </div>
                @if ($moduleStatus->kyc_status)
                    <div class="row" id='kyc'>
                        <div class="py-3">
                            <div class="alert alert-info fade show border-start" role="alert">
                                <span class="fw-bolder text-primary">
                                    <h4>{{ __('payoutSettings.kyc_config') }}</h4>
                                </span>
                                <p class="text-justify mt-lg-2">
                                    {{ __('payoutSettings.kyc_description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive settings_table">
                            <span class="d-flex justify-content-between align-items-center">
                                <h5>{{ __('payout.kyc_configuration') }}</h5>
                                <button style="margin-bottom:10px" class="btn btn-success ms-2 float-end"
                                    data-bs-toggle="offcanvas" data-bs-target="#addCategory" type="button"
                                    data-bs-whatever="Bank Configuration" aria-controls="offcanvasRight"><i
                                        class="fa fa-plus"></i>{{ __('common.add_category') }}
                                </button>
                            </span>
                            <table class="table table-editable table-nowrap align-middle table-edits mt-3"
                                id="payouKycCategory">
                                <thead>
                                    <tr>
                                        <th>{{ __('common.category') }}</th>
                                        <th></th>
                                        <th>{{ __('common.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kyc_categories as $category)
                                        <tr data-id="{{ $category->id }}" class="categories"
                                            id="category_{{ $category->id }}">
                                            <input type="hidden" value="{{ $category->id }}" data-field="id">
                                            <td data-field="Catogery">{{ $category->category }}
                                                @error('category')
                                                    <span class="text-danger">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </td>
                                            <td>
                                                <span class="text-danger" id="kyc_cat_err-{{ $category->id }}"></span>
                                            </td>
                                            <td class="d-none" data-field="id">{{ $category->id }}</td>
                                            <td style="width: 100px">
                                                <div class="d-flex">
                                                    <a class="btn btn-outline-secondary btn-sm edit" title="Edit">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <form action="{{ route('kyc.category.delete', $category->id) }}"
                                                        method="post" id="DeleteForm">
                                                        <noscript>
                                                            @csrf
                                                            @method('delete')
                                                        </noscript>
                                                        <button type="submit" class="btn btn-outline-danger btn-sm ms-2"
                                                            id="sa-warning" onclick="deleteCategory({{ $category->id }})">
                                                            <i class="bx bx-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>
                                                {{ __('payoutSettings.no_category') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>

    @include('admin.settings.payout.inc.category')
@endsection

@push('scripts')
    <script>
        const deleteCategory = async (categoryId) => {
            event.preventDefault()
            let confirm = await confirmSwal()
            if (confirm.isConfirmed == true) {
                const res = await $.post(`kyc-category-delete/${categoryId}`, {
                    '_method': "delete",
                })
                await $(`#category_${categoryId}`).remove()
                notifySuccess(res.message)
            }
        };

        const updateKycCategory = async (url, category) => await $.post(`${url}`, {
            category
        })


        const addKycCategory = async (form) => {
            event.preventDefault()

            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)

            const res = await $.post(`${url}`, data)
            .catch((err) => {
                if (err.status === 422) {
                    formvalidationError(form, err)
                }
            });
            if (typeof res != 'undefined') {
                $('#addCategory').offcanvas('hide')
                $('#payouKycCategory tr:last').after(res.data)
                notifySuccess(res.message)
            }
        }

        $(function() {
            var e = {};
            $(".table-edits tr").editable({
                edit: function(t) {
                    $(".edit i", this)
                        .removeClass("fa-pencil-alt")
                        .addClass("fa-save")
                        .attr("title", "Save");
                },
                save: function(t) {
                    let id = t.id;
                    let url = "{{ route('kyc_category_update', ':id') }}";
                    url = url.replace(':id', id);
                    const res = updateKycCategory(url, t.Catogery)
                        .catch((err) => {
                            if (err.status === 422) {
                                $(`#kyc_cat_err-${id}`).html('');
                                let error = err.responseJSON.errors
                                $(`#kyc_cat_err-${id}`).html(error.category[0])
                            }
                        })
                    if (typeof res != 'undefined') {
                        $(`#kyc_cat_err-${id}`).html('');
                    }
                    $(".edit i", this)
                        .removeClass("fa-save")
                        .addClass("fa-pencil-alt")
                        .attr("title", "Edit"),
                        this in e && (e[this].destroy(), delete e[this]);
                },
                cancel: function(t) {
                    $(".edit i", this)
                        .removeClass("fa-save")
                        .addClass("fa-pencil-alt")
                        .attr("title", "Edit"),
                        this in e && (e[this].destroy(), delete e[this]);
                },
            });

        });

        const changePayoutFee = (el) => {
            console.log(el);
        }
    </script>
@endpush
