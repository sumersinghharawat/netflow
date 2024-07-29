<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="invoice-title">
                    <h4 class="float-end font-size-16">Order # {{ getPayoutInvoiceNo($model->id) }}</h4>
                    <div class="mb-4">
                        @if ($companyProfile->logo == null)
                            <span class="logo-sm">
                                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" class="img-fluid w-25">
                            </span>
                        @else
                            <span class="logo-sm">
                                <img src="{{ asset('storage/uploads/logos/' . $companyProfile->logo) }}" alt=""
                                    class="img-fluid w-25">
                            </span>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <address>
                            {{ $companyProfile->name }}<br>
                            {{ $companyProfile->email }}<br>
                            {{ $companyProfile->phone }}<br>
                            {{ $companyProfile->address }}
                        </address>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <address class="mt-2 mt-sm-0">
                            <strong>{{ __('common.user_details') }}:</strong><br>
                            {{ $model->user->userDetail->name }}<br>
                            {{ $model->user->userDetail->second_name }}<br>
                            {{ $model->user->userDetail->mobile }}<br>
                        </address>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 70px;">No.</th>
                                <th>{{ __('common.item') }}</th>
                                <th>{{ __('common.paid_date') }}</th>
                                <th class="text-end">{{ __('common.price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>001</td>
                                <td>{{ __('payout.payout_released') }}</td>
                                <td>{{ Carbon\Carbon::parse($model->date)->format("D M,Y") }}</td>
                                <td class="text-end">{{ $currency }} {{ formatCurrency($model->amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-print-none">
                    <div class="float-end">
                        <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1"><i class="fa fa-print"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
