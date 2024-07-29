@extends('layouts.app')
@section('content')
    <div class="container-fluid settings_page">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.inc.links')
                <div class="py-3">
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <span class="fw-bolder text-primary"><i class="dripicons-user-id me-2"></i>{{__('subscription.subscription')}}</span>
                        <p class="text-justify mt-lg-2">
                            {{__('subscription.subscription_text')}}
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('subscription.update', $subscription_config->id) }}" method="post" class="mt-5"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-4">
                        <label for="tree-icon">{{__('subscription.subscription_based_on')}} :</label>

                        <select id="subscription_criteria" name="subscription_criteria" class="form-select">

                            <option value="member_package"
                                {{ $subscription_config->based_on == 'member_package' ? 'selected' : '' }}>{{__('subscription.member_package')}}</option>
                            <option value="amount_based" {{ $subscription_config->based_on == 'amount_based' ? 'selected' : '' }}>
                                {{__('subscription.fixed_amount')}} </option>
                        </select>
                    </div>
                    <div class="package_wise  {{ $subscription_config->based_on == 'member_package' ? 'd-block' : 'd-none' }}"
                        id="packageWise">

                        <div class="panel panel-default table-responsive">
                            <table class="table table-editable table-nowrap align-middle table-edits">
                                <thead>
                                    <tr>
                                        <th>{{__('common.sl_no')}}</th>
                                        <th>{{__('common.package')}}</th>
                                        <th>{{__('common.amount')}}</th>
                                        <th>{{__('common.period_monthly')}}</th>
                                        <th></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @if($membership_packages != null)

                                    @forelse ($membership_packages as $key => $package)
                                        <tr data-id="{{ $package->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $package->name  ?? $package->model}}</td>
                                            <td>{{ round($package->price, 2) }}</td>
                                            <td data-field="subscriptionPeriod">{{ $package->validity }}
                                                <div class="invalid-feedback d-block" id="subscription_{{ $package->id ?? $package->product_id}}">
                                                </div>
                                            </td>
                                            <td class="d-none" data-field="packageId">{{ $package->id ?? $package->product_id }}</td>
                                            <td>

                                                <a class="btn btn-outline-secondary btn-sm edit" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>


                                            </td>

                                        </tr>
                                        @empty
                                        <tr>
                                           {{__('common.no_data')}}
                                        </tr>
                                        @endforelse
                                        @endif
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="amount_wise  {{ $subscription_config->based_on == 'amount_based' ? 'd-block' : 'd-none' }}"
                            id="amount_wise">
                            <div class="form-group col-4">
                                <label>{{__('subscription.subscription_amount')}}</label>
                                <input class="form-control @error('fixed_amount') is-invalid @enderror" name="fixed_amount"
                                    id="fixed_amount" maxlength="5" value="{{ $subscription_config->fixed_amount }}">
                                @error('fixed_amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-4">
                                <label>{{__('subscription.subscription_period')}}</label>
                                <input type="number" class="form-control @error('fixed_subscription') is-invalid @enderror"
                                    name="fixed_subscription" id="fixed_subscription" maxlength="5" min="1"
                                    value="{{ $subscription_config->subscription_period }}">
                                @error('fixed_subscription')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="form-group col-xs-12">
                            <div class="checkbox">
                                <label class="i-checks">
                                    <input type="checkbox" value="1" name="registration"
                                        {{ $subscription_config->reg_status == 1 ? 'checked' : '' }} class="form-check-input">
                                    {{__('subscription.disable_registration_text')}}

                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label class="i-checks">
                                <input type="checkbox" value="1" name="payout"
                                    {{ $subscription_config->payout_status == 1 ? 'checked' : '' }} class="form-check-input">
                                {{__('subscription.disable_payout_request')}}
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label class="i-checks">
                                <input type="checkbox" value="1" name="commission"
                                    {{ $subscription_config->commission_status == 1 ? 'checked' : ' ' }}
                                    class="form-check-input">
                                {{__('subscription.disable_commission_request')}}
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{__('common.update')}}</button>
                </form>
            </div>
        </div>




    </div>
@endsection
@push('scripts')
    <script>
        $('#subscription_criteria').on('change', function() {
            let value = $(this).val()
            if (value == 'amount_based') {
                $('#packageWise').removeClass('d-block')
                $('#packageWise').addClass('d-none')
                $('#amount_wise').removeClass('d-none')
                $('#amount_wise').addClass('d-block')
            } else if (value == 'member_package') {
                $('#amount_wise').removeClass('d-block')
                $('#amount_wise').addClass('d-none')
                $('#packageWise').removeClass('d-none')
                $('#packageWise').addClass('d-block')
            }
        });

        const updateSubscriptionPeriod = async (url, data) => {
            const res = await $.post(`${url}`, data)
            notifySuccess(res.message)
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
                    let id = t.packageId;
                    let url = "{{ route('subscriptionPackage.update', ':id') }}";
                    url = url.replace(':id', id);
                    updateSubscriptionPeriod(url, t)
                        .catch((err) => {
                            if (err.status === 422) {
                                let error = err.responseJSON.errors['subscriptionPeriod'][0]
                                notifyError(error)
                            }
                        })
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
    </script>
@endpush
