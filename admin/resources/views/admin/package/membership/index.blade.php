@extends('layouts.app')
@section('title', __('package.membership'))
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('package.membership') }}</h4>
            </div>
        </div>
    </div>

    <div class="card p-3">
        <div class="crad-body">

            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('package') }}" class="row row-cols-lg-auto g-3 align-items-center" method="GET">
                        <div class="col-12">
                            <select class="form-select" id="inlineFormSelectPref" name="status">
                                <option value="active" @selected(app('request')->input('status') == 'active')>{{ __('common.active') }}</option>
                                <option value="blocked" @selected(app('request')->input('status') == 'blocked')>{{ __('common.blocked') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-md">{{ __('common.search') }}</button>
                            <a href="{{ route('package') }}" class="btn btn-danger">{{ __('common.reset') }}</a>
                        </div>
                    </form>
                </div>

                <div class="col-md-6">
                    <div class="float-end mob_lft">
                        <a class="btn btn-primary waves-effect waves-light" data-bs-toggle="offcanvas" href="#add-package"
                            role="button" aria-controls="add-package"><i
                                class="bx bx-plus font-size-16 align-middle me-2"></i>{{ __('package.add_package') }}</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive mt-3" id="packageTable">
                <table class="table  table-hover rankTable">
                    <thead>
                        <tr class="th">
                            <th>#</th>
                            <th>{{ __('package.name') }}</th>
                            <th>{{ __('common.amount') }}</th>
                            <th>{{ __('package.image') }}</th>
                            @if ($pvVisible == 'yes')
                                <th>{{ __('package.pv') }}</th>
                            @endif
                            @if ($bvVisible == 'yes')
                                <th>{{ __('package.bv_value') }}</th>
                            @endif
                            @if ($moduleStatus->subscription_status)
                                <th>{{ __('package.validity') }}</th>
                                @if (isset($stripeStatus->membership_renewal) && $stripeStatus->membership_renewal)
                                    <th>{{ __('package.stripeId') }}</th>
                                @endif
                                @if (isset($paypalStatus->membership_renewal) && $paypalStatus->membership_renewal)
                                    <th>{{ __('package.paypalId') }}</th>
                                @endif
                            @endif
                            @if ($mlmPlan == "Monoline")
                                <th>{{ __('package.reentry_limit') }}</th>
                            @endif
                            <th>{{ __('common.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($packages as $package)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $package->name }}</td>
                                <td>{{ $currency . ' ' . formatCurrency($package->price) }}</td>
                                <td>
                                    @if ($package->image == null || !isFileExists($package->image))
                                        <img src="{{ asset('/assets/images/register.png') }}"
                                            class="img-fluid w-25 rounded">
                                    @else
                                        <img src="{{ $package->image }}" class="img-fluid w-25">
                                    @endif
                                </td>
                                @if ($pvVisible == 'yes')
                                    <td>{{ $package->pair_value }}</td>
                                @endif
                                @if ($bvVisible == 'yes')
                                    <td>{{ $package->bv_value }}</td>
                                @endif
                                @if ($moduleStatus->subscription_status)
                                    <td>
                                        {{ $package->validity }}
                                    </td>
                                    @if (isset($stripeStatus->membership_renewal) && $stripeStatus->membership_renewal)
                                        <td>
                                            @if (isset($package->stripe->price_id))
                                                <span>{{ $package->stripe->price_id }}</span>
                                            @else
                                                <a class="btn btn-primary waves-effect waves-light"
                                                    onclick="createSubscriptionProduct('stripe',{{ $package }})"
                                                    role="button"
                                                    id="{{ 'button_stripe' . $package->id }}">{{ __('package.createStripeId') }}</a>
                                                <span id="{{ 'stripe' . $package->id }}" style="display: none"><span>
                                            @endif
                                        </td>
                                    @endif
                                    @if (isset($paypalStatus->membership_renewal) && $paypalStatus->membership_renewal)
                                        <td>
                                            @if (isset($package->paypal->plan_id))
                                                <span>{{ $package->paypal->plan_id }}</span>
                                            @else
                                                <a class="btn btn-primary waves-effect waves-light"
                                                    onclick="createSubscriptionProduct('paypal',{{ $package }})"
                                                    role="button"
                                                    id="{{ 'button_paypal' . $package->id }}">{{ __('package.createPaypalId') }}</a>
                                                <span id="{{ 'paypal' . $package->id }}"></span>
                                            @endif
                                        </td>
                                    @endif
                                @endif
                                @if ($mlmPlan == "Monoline")
                                <td>{{ $package->reentry_limit }}</td>
                                @endif
                                <td>
                                    <form action="{{ route('packageDis', $package->id) }}" method="post">
                                        @csrf
                                        @if ($package->active)
                                            <a href="{{ route('member.package.edit', $package->id) }}"
                                                class="btn btn-primary" onclick="editPackage(this)"><i
                                                    class="bx bx-edit"></i></a>
                                        @endif
                                        <button type="submit"
                                            class="btn ms-3 {{ $package->active ? 'btn-danger' : 'btn-success' }}">
                                            <i
                                                class="{{ $package->active ? 'bx bx-block ' : 'bx bx-check-circle ' }}"></i></button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">
                                    <div class="nodata_view">
                                        <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                        <span>{{ __('common.no_data') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
                <ul class="pagination pagination-rounded justify-content-end m-2">
                    {{ $packages->links() }}
                </ul>
            </div>

        </div>
    </div>

    <div id="PackageCanvas">
    </div>
    @include('admin.package.membership._add')
@endsection

@push('scripts')
    <script>
        const memberPackageStore = async (form) => {
            event.preventDefault()
            var formElements = new FormData(form);

            for (var [key, value] of formElements) {
                form.elements['stepOne'].classList.remove('is-invalid', 'd-block')
            }

            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)

            $.ajax({
                type: 'POST',
                enctype: 'multipart/form-data',
                url,
                data:formElements,
                processData: false,
                contentType: false,
                cache: false,
            }).catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    }else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                }).then((res) => {
                    if (typeof(res) != 'undefined') {
                        notifySuccess(res.message)
                        form.reset()
                        location.reload()
                        // $('#packageTable').html(' ');
                        // $('#packageTable').html(res.data);
                        // $('#add-package').offcanvas('hide')
                    }
                })

        }

        const editPackage = async (href) => {
            event.preventDefault()
            let url = href.href
            const res = await $.post(`${url}`, {
                '_method': 'PUT'
            }).catch((err) => {
                console.log(err)
            }).then((res) => {
                $('#PackageCanvas').html(res.data)
                $('#editpackage').offcanvas('show')
            })
        }

        const memberPackageUpdate = async (form) => {
            event.preventDefault()
            var formElements = new FormData(form);
            console.log(formElements);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }

            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)
            $.ajax({
                type: 'POST',
                enctype: 'multipart/form-data',
                url,
                data:formElements,
                processData: false,
                contentType: false,
                cache: false,
            }).catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    }else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                }).then((res) => {
                    if (typeof(res) != 'undefined') {
                        notifySuccess(res.message);
                        form.reset();
                        location.reload();
                    }
                })

        }

        const packageFilter = async (form) => {
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
                        console.log(err);
                    }else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                }).then((res) => {
                    $('#packageTable').html(' ');
                    $('#packageTable').html(res.data);
                })

        }

        const createSubscriptionProduct = (slug, package) => {
            try {
                $.ajax({
                    type: 'post',
                    url: '{{ URL::to('admin/package/create-payment-id') }}',
                    data: {
                        'slug': slug,
                        'package': package
                    },
                    success: function(res) {
                        if (res.status == true) {
                            $('#' + slug + package.id).html(res.payment_id)
                            $('#button_' + slug + package.id).hide()
                            $('#' + slug + package.id).show()
                            notifySuccess(res.message)
                        } else {
                            notifyError(res.message)
                        }
                    }
                });
            } catch (error) {
                console.log(error)
            }
        }
        addproductImage = (event, id) => {
            let output = document.getElementById(id);
            console.log(output);
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
        }
};
    </script>
@endpush
