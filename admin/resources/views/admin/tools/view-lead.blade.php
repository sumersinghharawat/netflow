@extends('layouts.app')
@section('title', 'Leads')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('CRM.crm') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body filter-report">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" name="filter" class="form-control" id="filter"
                                placeholder="Name, Email, Mobile">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex">
                            <button type="button" class="btn btn-primary"
                                onclick="viewLead()">{{ __('CRM.view_leads') }}</button>
                            <a href="{{ route('leads') }}"><button class="btn btn-danger ms-2 search_clear" type="button"
                                    id="res">{{ __('common.reset') }}</button></a>
                        </div>

                    </div>

                </div>
                <!-- end card body -->
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <p>{{ __('CRM.lead_capture_link') }}: <a
                        href="{{ URL::signedRoute('lcp', ['user' => auth()->user()->username]) }}">{{ URL::signedRoute('lcp', ['user' => auth()->user()->username]) }}</a>
                </p>
                <table id="datatable-view-lead" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <th>{{ __('common.sl_no') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.sponsor') }}</th>
                        <th>{{ __('common.email') }}</th>
                        <th>{{ __('common.mobile_number') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('common.date') }}</th>
                        {{-- <th>{{__('CRM.edit_lead')}}</th> --}}
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            {{-- @include('crm.inc.edit-lead') --}}
        </div>
    @endsection

    @push('scripts')
        <script>
            $(() => {
                viewLead();
            });
            let startDate = moment().subtract(1, 'year').add(1, 'day');
            let endDate = moment().add(1, 'day');
            let followupStartDate = moment().subtract(1, 'year').add(1, 'day');
            let followupEndDate = moment().add(1, 'day');
            let leadStatusStartDate = moment().subtract(1, 'year').add(1, 'day');
            let leadStatusEndDate = moment().add(1, 'day');
            let levelOfInterest = $('#level_of_interest').find(":selected").val();
            let leadStatus = $('#lead_status').find(":selected").val();
            let country = $('#country').find(":selected").val();
            let assignee = $('#assignee').val();
            $('#lead_date_range').daterangepicker({
                startDate,
                endDate,
                ranges: {
                    'All Time': [startDate, endDate],
                    'Last 30 days': [moment().subtract(29, 'days'), moment()],
                    'Last 90 days': [moment().subtract(89, 'days'), moment()],
                    'Last Year': [moment().subtract(1, 'year').add(1, 'day'), moment()],
                },
            }, (start, end, label) => {
                startDate = start;
                endDate = end
            });

            $('#followup_date_range').daterangepicker({
                followupStartDate,
                followupEndDate,
                ranges: {
                    'All Time': [followupStartDate, followupEndDate],
                    'Last 30 days': [moment().subtract(29, 'days'), moment()],
                    'Last 90 days': [moment().subtract(89, 'days'), moment()],
                    'Last Year': [moment().subtract(1, 'year').add(1, 'day'), moment()],
                },
            }, (start, end, label) => {
                followupStartDate = start;
                followupEndDate = end
            });

            $('#status-change-date').daterangepicker({
                leadStatusStartDate,
                leadStatusEndDate,
                ranges: {
                    'All Time': [leadStatusStartDate, leadStatusEndDate],
                    'Last 30 days': [moment().subtract(29, 'days'), moment()],
                    'Last 90 days': [moment().subtract(89, 'days'), moment()],
                    'Last Year': [moment().subtract(1, 'year').add(1, 'day'), moment()],
                },
            }, (start, end, label) => {
                leadStatusStartDate = start;
                leadStatusEndDate = end
            });
            const viewLead = async () => {
                let filter = $('#filter').val();
                let params = {
                    filter: filter,
                }
                var table = $('#datatable-view-lead').DataTable({
                    processing: true,
                    serverSide: true,
                    "sDom": 'Lfrtlip',
                    searching: false,
                    "bDestroy": true,
                    ajax: {
                        type: "GET",
                        url: "{{ route('get.leads') }}",
                        data: params
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            orderable: false,
                        },
                        {
                            data: 'first_name',
                            name: 'first_name',
                            searchable: true
                        },
                        {
                            data: 'sponsor',
                            name: 'sponsor',
                            searchable: true
                        },
                        {
                            data: 'email_id',
                            name: 'email_id',
                            searchable: true,
                        },
                        {
                            data: 'mobile_no',
                            name: 'mobile_no',
                            searchable: true,
                        },

                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'date',
                            name: 'date',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }
        </script>
    @endpush
