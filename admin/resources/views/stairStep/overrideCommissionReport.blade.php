@extends('layouts.app')
@section('title', trans('stairStep.overrideCommissionReport'))
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('stairStep.overrideCommissionReport') }}</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="userName" class="form-label">{{ __('common.username') }}</label>
                                        <select name="" id="userName"
                                            class="form-select select2-search-user"></select>
                                    </div>
                                </div>

                                <div class="col-md-6" id="customRange">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="fromDate" class="form-label">{{ __('common.fromDate') }}</label>
                                            <input type="date" name="fromDate" id="fromDate" class="form-control">
                                        </div>
                                        <div class="col-6">
                                            <label for="toDate" class="form-label">{{ __('common.toDate') }}</label>
                                            <input type="date" name="toDate" id="toDate" class="form-control"
                                                max="{{ now()->format('m.d.Y') }}">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-3 mt-lg-4" id="submitConatiner">
                                    <span class="form-label"></span>
                                    <button type="button" class="btn btn-primary mt-lg-1"
                                        onclick="getOverrideCommissionReport()">{{ __('common.submit') }}</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="container d-none" id="ReportDiv">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <table id="overrideCommission" class="table table-hover">
                    <thead class="table">
                        <th>#</th>
                        <th>{{ __('common.username') }}</th>
                        <th>{{ __('common.fullName') }}</th>
                        <th>{{ __('stairStep.submissionDate') }}</th>
                        <th>{{ __('stairStep.paidStep') }}</th>
                        <th>{{ __('stairStep.pv') }}</th>
                        <th>{{ __('common.totalAmount') }}</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">{{ __('common.no_data') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6">{{ __('common.total') }}</th>
                            <th id="total_order"></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        $(() => {
            getUsers();
        });

        const getOverrideCommissionReport = async () => {
            let url = "{{ route('get.override.report') }}";
            let params = {
                users: $('#userName').val(),
                from: $('#fromDate').val(),
                to: $('#toDate').val(),
            }

            if (params.users != null) {
                var table = $('#overrideCommission').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    "bDestroy": true,

                    ajax: {
                        type: "GET",
                        url: url,
                        data: params
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'index',
                            orderable: false,
                        },

                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'fullname',
                            name: 'fullname',
                            orderable: false
                        },
                        {
                            data: 'date',
                            name: 'created_at',
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'user_level',
                            name: 'user_level',
                        },
                        {
                            data: 'pair_value',
                            name: 'pair_value',
                        },
                        {
                            data: 'amount_payable',
                            name: 'amount_payable',
                            orderable: false,
                        },
                    ],
                    drawCallback: function(settings) {
                        let total = settings.json.total;
                        $('#total_order').html(total);
                    }
                });
                $('#ReportDiv').removeClass('d-none');
            }


        }
    </script>
@endpush
