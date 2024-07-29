

            <!-- start page title -->
            <div class="row">
                <div class="col-md-6">

                        <h4 class="mb-sm-0 font-size-18">{{ __('common.detail') }}</h4>

                        <div class="page-title-right">
                          Invoice # : {{"PR000" .$payoutDetails->id  }}

                        </div>


                </div>
                <div class="col-md-6">
                    <div style="float: right;">
                    <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1" id="printButton"><i class="fa fa-print"></i></a>
                    </div>

                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <address>
                                        <strong>{{ __('common.company_details') }}:</strong><br>
                                        {{ __('common.logo') }}<br>
                                        {{ __('common.phone') }}<br>
                                        {{ __('common.email') }}<br>
                                        {{ __('common.details') }}
                                    </address>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <address class="mt-2 mt-sm-0">
                                        <strong>User details :</strong><br>
                                        {{ $payoutDetails->user->userDetails->name }}&nbsp;&nbsp;{{ $payoutDetails->user->userDetails->second_name }}<br>
                                        {{ $payoutDetails->user->userDetails->phone }}<br>
                                        {{ $payoutDetails->user->email }}<br>

                                    </address>
                                </div>
                            </div>
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-nowrap">
                                        <thead>
                                            <tr>
                                                <th style="width: 70px;">{{ __('common.item') }}</th>
                                                <th>{{ __('common.date') }}</th>
                                                <th class="text-end">{{ __('common.total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td>
                                                    @if($payoutDetails->type == "released")
                                                     {{ __('reports.payout_released') }}
                                                    @else
                                                     {{ __('reports.pending') }}
                                                     @endif
                                                </td>
                                                <td>{{  Carbon\Carbon::parse($payoutDetails->date)->format('d-M-Y  g:i:s A' ) }}</td>
                                                <td class="text-end">{{ $payoutDetails->amount }}</td>
                                            </tr>


                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
