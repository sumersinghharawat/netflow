@extends('layouts.app')
@section('title', 'Downlines')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('tree.downline_members') }}</h4>
            </div>
            <div class="card top-filter-card-bx download_member_box_sec" style="margin-bottom: 6px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="row d-flex justify-content-between">
                                <div class="col-xl-5 col-lg-12">
                                    <div class="grid_downline_mmbr">
                                        <div class="col-md-12">
                                            <div class="card bg-soft">
                                                <div class="card-header">
                                                    {{ __('tree.total_downlines') }}
                                                </div>
                                                <div class="card-body">
                                                    <strong class="text-success">
                                                        {{ $data->closure_children_count - 1 }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card bg-soft">
                                                <div class="card-header">
                                                    {{ __('tree.total_levels') }}
                                                </div>
                                                <div class="card-body">
                                                    <strong class="textdwln_clr">
                                                        {{ $data->descendants_max_user_level }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-5 col-lg-12">
                                    <div class="row downline_right_srch_sec justify-content-end">
                                        <div class="col-md-12">
                                            <label>{{ __('common.username') }}</label>
                                            <div class="form-group">
                                                <select class="select2-search-user d-none" id="users"
                                                    name="user"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label>{{ __('common.level') }}</label>
                                            <div class="form-group">
                                                <select class="form-control" name="levelValue" id="levelValue">
                                                    <option value="all">All</option>
                                                    @for ($i = 1; $i <= $data->descendants_max_user_level; $i++)
                                                        <option value="{{ $i }}">
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <a href="javascript:void(0)" onclick="loadDownlineMembers(this)"
                                                class="btn btn-primary"
                                                style="margin-top: 27px;">{{ __('common.view') }}</a>
                                            <a href="{{ route('network.downlineMembers') }}" class="btn btn-primary btn-sec"
                                                style="margin-top: 27px;"> {{ __('common.reset') }} </a>
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <table id="datatable-view-downline" class="table nowrap w-100">
                            <thead>
                                <th> {{ __('common.member') }}</th>
                                @if ($moduleStatus->mlm_plan == 'Binary' || $moduleStatus->mlm_plan == 'Matrix')
                                    <th>{{ __('common.placement') }}</th>
                                @endif
                                <th>{{ __('common.sponsor') }}</th>
                                <th>{{ __('common.level') }} </th>
                                <th>{{ __('common.action') }}</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(() => {
            loadDownlineMembers();
            getUsers();
        });

        const loadDownlineMembers = () => {
            let params = {
                level: $('#levelValue').val(),
                user: $('#users').val(),
            }
            var table = $('#datatable-view-downline').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                "bDestroy": true,
                "sDom": 'Lfrtlip',
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                ajax: {
                    type: "GET",
                    url: "{{ route('network.downlineMembers') }}",
                    data: params
                },
                columns: getDownlineMembersColumn()
            });
        }
        function getDownlineMembersColumn() {
            let plan = `{{ $moduleStatus->mlm_plan }}`;
            var columns = [];
            if (plan == "Binary" || plan == "Matrix") {
                columns.push({
                    data: "member"
                })
                columns.push({
                    data: "placement"
                })
                columns.push({
                    data: "sponsor"
                })
                columns.push({
                    data: "level"
                })
                columns.push({
                    data: "action"
                })
            } else {
                columns.push({
                    data: "member"
                })
                columns.push({
                    data: "sponsor"
                })
                columns.push({
                    data: "level"
                })
                columns.push({
                    data: "action"
                })
            }
            return columns;
        }
    </script>
@endpush
