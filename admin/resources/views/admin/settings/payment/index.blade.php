@extends('layouts.app')
@section('title', __('settings.payment_methods'))
@section('content')
    <div class="container-fluid settings_page">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.inc.links')
                <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                    <h4><i class="bx bx-credit-card  me-2"></i>{{ __('settings.payment_methods') }}</h4>
                    <p class="text-justify mt-lg-2">
                        {{ __('settings.payment_description') }}
                    </p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <form action="{{ route('payment.update') }}" method="post">
                        @csrf
                        <table class="table table-hover">

                            <thead>
                                                                <tr>
                                    <th>{{ __('common.logo') }}</th>
                                    <th>{{ __('common.payment_method') }}</th>
                                    <th>{{ __('common.action') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.registration') }}</th>
                                    @if ($data['moduleStatus']->repurchase_status)
                                        <th>{{ __('common.repurchase') }}</th>
                                    @endif
                                    @if ($data['moduleStatus']->subscription_status)
                                        <th>{{ __('common.membership_renewal') }}</th>
                                    @endif
                                    @if ($data['moduleStatus']->package_upgrade)
                                        <th>{{ __('common.upgradation') }}</th>
                                    @endif
                                    <th>{{ __('common.admin_only') }}</th>
                                </tr>
                            </thead>
                            @csrf
                            <tbody>
                                @foreach ($data['paymentConfig'] as $payment)
                                    <tr>
                                        <td>
                                            @if ($payment->logo != '')
                                                <img src="{{ asset('assets/images/logos/' . $payment->logo) }}"
                                                    alt="{{ $payment->logo }}" class="" width="50px">
                                            @endif
                                        </td>
                                        <td>{{ __('common.' . $payment->slug) }}
                                            @if ($payment->name != 'E-pin' &&
                                                $payment->name != 'E-wallet' &&
                                                $payment->name != 'Free Joining' &&
                                                $payment->name != 'Bank Transfer')
                                                <span
                                                    class="fw-bolder">{{ '[' . __('common.' . $payment->mode) . ']' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($payment->name != 'E-pin' && $payment->name != 'E-wallet' && $payment->name != 'Free Joining')
                                                @if ($payment->slug == 'bank-transfer')
                                                    <a href="#" class="bx bx-cog" data-bs-toggle="offcanvas"
                                                        data-bs-target="#bankConfiguration"
                                                        data-bs-whatever="Bank Configuration"
                                                        aria-controls="offcanvasRight"></a>
                                                @elseif($payment->slug == 'stripe')
                                                    <a href="#" class="bx bx-cog" data-bs-toggle="offcanvas"
                                                        data-bs-target="#stripeConfiguration"
                                                        data-bs-whatever="Stripe Configuration"
                                                        aria-controls="offcanvasRight"></a>
                                                @elseif($payment->slug == 'nowpayment')
                                                    <a href="#" class="bx bx-cog" data-bs-toggle="offcanvas"
                                                        data-bs-target="#nowpaymentConfiguration"
                                                        data-bs-whatever="Nowpayment Configuration"
                                                        aria-controls="offcanvasRight"></a>
                                               @elseif($payment->slug == 'paypal')
                                                    <a href="#" class="bx bx-cog" data-bs-toggle="offcanvas"
                                                        data-bs-target="#paypalConfiguration"
                                                        data-bs-whatever="Paypal Configuration"
                                                        aria-controls="offcanvasRight"></a>

                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check form-switch ">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    {{ $payment->status == 1 ? 'checked' : '' }}
                                                    name="status_{{ $payment->id }}" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch ">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    {{ $payment->registration == 1 ? 'checked' : '' }}
                                                    name="registartion_{{ $payment->id }}" value="1">
                                            </div>
                                        </td>
                                        @if ($data['moduleStatus']->repurchase_status)
                                            <td>
                                                <div class="form-check form-switch ">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        {{ $payment->repurchase == 1 ? 'checked' : '' }}
                                                        name="repurchase_{{ $payment->id }}" value="1">
                                                </div>
                                            </td>
                                        @endif
                                        @if ($data['moduleStatus']->subscription_status)
                                            <td>
                                                <div class="form-check form-switch ">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        {{ $payment->membership_renewal == 1 ? 'checked' : '' }}
                                                        name="renewal_{{ $payment->id }}" value="1">
                                                </div>
                                            </td>
                                        @endif
                                        @if ($data['moduleStatus']->package_upgrade)
                                            <td>
                                                <div class="form-check form-switch ">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        {{ $payment->upgradation == 1 ? 'checked' : '' }}
                                                        name="upgradation_{{ $payment->id }}" value="1">
                                                </div>
                                            </td>
                                        @endif
                                        <td>
                                            <div class="form-check form-switch ">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    {{ $payment->admin_only == 1 ? 'checked' : '' }}
                                                    name="admin_{{ $payment->id }}" value="1">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('admin.settings.payment.bank-configuration')
@endsection


@push('scripts')
    <script>
        const storeStripe = async (form) => {
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
                url,
                data,
                processData: false,
                contentType: false,
                cache: false,
            }).catch((err) => {
                if (err.status === 422) {
                    formvalidationError(form, err)
                }else if (err.status === 401) {
                    let errors = err.responseJSON.errors;
                    notifyError(errors)
                }
            }).then((res) => {
                if (typeof(res) != "undefined") {
                    form.reset()
                    $('#stripeConfiguration').offcanvas('hide')
                    notifySuccess(res.message)
                }
                location.reload();
            })
        }


        const storeNowpayment = async (form) => {
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
                url,
                data,
                processData: false,
                contentType: false,
                cache: false,
            }).catch((err) => {
                if (err.status === 422) {
                    formvalidationError(form, err)
                }else if (err.status === 401) {
                    let errors = err.responseJSON.errors;
                    notifyError(errors)
                }
            }).then((res) => {
                if (typeof(res) != "undefined") {
                    form.reset()
                    $('#nowpaymentConfiguration').offcanvas('hide')
                    notifySuccess(res.message)
                }
                location.reload();
            })
        }

        // const storePaypal = async (form) => {
        //     event.preventDefault()
        //     var formElements = new FormData(form);
        //     for (var [key, value] of formElements) {
        //         form.elements[key].classList.remove('is-invalid', 'd-block')
        //     }
        //     $('.invalid-feedback').remove()

        //     let url = form.action
        //     let data = new FormData(form)
        //     $.ajax({
        //         type: 'POST',
        //         url,
        //         data,
        //         processData: false,
        //         contentType: false,
        //         cache: false,
        //     }).catch((err) => {
        //         if (err.status === 422) {
        //             formvalidationError(form, err)
        //         }else if (err.status === 401) {
        //             let errors = err.responseJSON.errors;
        //             notifyError(errors)
        //         }
        //     }).then((res) => {
        //         if (typeof(res) != "undefined") {
        //             form.reset()
        //             $('#paypalConfiguration').offcanvas('hide')
        //             notifySuccess(res.message)
        //         }
        //         location.reload();
        //     })
        // }
    </script>
@endpush
