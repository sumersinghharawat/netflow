@extends('layouts.app')
@section('title', 'Referrals')

@section('content')
    <div class="row">
        <div class="col-md-12 p-0">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('tree.referral_members') }}</h4>
        </div>
            <div class="card top-filter-card-bx">
                <div class="card-body">
                    <div class="row">
                    <div class="col-lg-4 col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-success bg-soft">
                                        <div class="card-header">
                                            {{ __('tree.total_referrals') }}
                                        </div>
                                        <div class="card-body">
                                            <strong>  {{ $data->sponsor_descendant_count - 1 }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-info bg-soft">
                                        <div class="card-header">
                                            {{ __('tree.total_levels') }}
                                        </div>
                                        <div class="card-body">
                                            <strong>  {{ $data->sponsor_descendant_max_user_level }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row downline_right_srch_sec justify-content-end">
                                        <div class="col-md-12">
                                            <label>{{ __('common.username') }}</label>
                                            <div class="form-group">
                                                <select class="form-control select2-search-user"  id="users" name="user"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label>{{ __('common.level') }}</label>
                                            <div class="form-group">
                                                <select class="form-control" name="level" id="level">
                                                    <option value="all">All</option>
                                                    @for($i=1; $i <= $data->sponsor_descendant_max_user_level; $i++)
                                                        <option value="{{ $i }}">
                                                           {{ $i}}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                    <a href="javascript:void(0)" onclick="loadReferralMembers(this)" class="btn btn-primary" style="margin-top: 27px;">{{ __('common.view') }}</a>
                                    <a href="{{ route('network.referralMembers') }}" class="btn btn-danger btn-sec" style="margin-top: 27px;"> {{ __('common.reset') }} </a>
                                </div>
                                    </div>
                                </div>

                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card referal_mem_table">
            <div class="card-body">
                <div class="row">
                    <table id="datatable-view-referral" class="table nowrap w-100">
                        <thead>
                            <th> {{ __('common.member') }}</th>
                            <th>{{ __('common.sponsor') }}</th>
                            <th>{{ __('common.level') }} </th>
                            <th>{{ __('common.joining_date') }} </th>
                            <th>{{ __('common.action') }}</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>

        $(() => {
            loadReferralMembers();
            getUsers();
        });

        let username = $('#users').val();

        const loadReferralMembers = () => {
            let params    = {
                    level   : $('#level').val(),
                    user    : $('#users').val(),
                }
            var table = $('#datatable-view-referral').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                "sDom": 'Lfrtlip',
                "bDestroy": true,
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{asset('assets/images/nodata-icon.png')}}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                ajax: {
                    type: "GET",
                    url: "{{ route('network.referralMembers') }}",
                    data: params
                },
                columns: [
                    {
                        data: 'member',
                        name: 'member',
                        searchable: true
                    },
                    {
                        data: 'sponsor',
                        name: 'sponsor',
                        searchable: true,
                    },
                    {
                        data: 'level',
                        name: 'level',
                        searchable: true,
                    },
                    {
                        data: 'joining_date',
                        name: 'joining_date',
                        searchable: true,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: true,
                    },
                ]
            });
        }

    </script>
    @endpush
