@extends('layouts.app')
@section('title', trans('reports.rank_performance_report'))
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card d-print-none">
                <div class="card-body filter-report">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>{{ __('reports.rank_performance_report') }}</h4>
                        </div>
                        <div class="col-md-8">

                        </div>
                    </div>
                    <form>
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ __('common.username') }}</label>
                                <div class="form-group">
                                    <select class="form-control form-select select2-search-user" name="username">
                                        @if ($userData)
                                            <option value="{{ $userData->id }}" selected>{{ $userData->username }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <button class="btn btn-primary" type="submit" style="margin-top: 27px;">
                                    {{ __('common.view') }}
                                </button>
                                <a href="{{ route('reports.rank-performance') }}" class="btn btn-danger"
                                    style="margin-top: 27px;">
                                    {{ __('common.reset') }}
                                </a>
                            </div>

                        </div>
                    </form>


                </div>
            </div>


            <div class="card">

                <div class="card-header">

                    <div class="row report_address_row">
                        <div class="col-md-6">
                            <div class="report_address_box">
                                <h4 class="card-title ">{{ $companyProfile->name }}</h4>
                                <p class="text-muted fw-bolder">{{ $companyProfile->address }}</p>
                                <p class="text-muted fw-bolder">{{ __('common.phone') }} : {{ $companyProfile->phone }}
                                </p>
                                <p class="text-muted fw-bolder">{{ __('common.email') }} : {{ $companyProfile->email }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 report_logo">
                            @if ($companyProfile->logo == null)
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" class="img-fluid">
                                </span>
                            @else
                                <span class="logo-sm">
                                    <img src="{{ $companyProfile->logo }}" alt=""
                                        class="img-fluid">
                                </span>
                            @endif


                        </div>

                        <div class="col-md-12 d-print-none">
                            <div style="float: right;">
                                {{--  <button class="btn btn-primary" id="excel">{{ __('common.create_excel') }}</button>
                                <button class="btn btn-primary" id="csv">{{ __('common.create_csv') }}</button> --}}
                                <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light me-1"
                                    id="printButton"><i class="fa fa-print"></i></a>
                            </div>
                        </div>
                    </div>


                </div>
                {{-- @if ($rankUser != null) --}}
                <div class="card-body">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class=" col-md-4">
                                    <div class="card border border-primary">
                                        <div class="card-header bg-transparent border-primary">
                                            <div class="col-md-6">
                                                <h4 class="my-0 text-primary">{{ __('reports.rank_criteria') }}
                                                </h4>
                                            </div>
                                        </div>

                                        <div class="card-body" style="padding-top:0">
                                            <ul>
                                                @foreach ($activeCriteria as $item)
                                                    <li>{{ $item->name }}</li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                                <div class=" col-md-8">
                                    <div class="card border border-primary">
                                        <div class="card-header bg-transparent border-primary">
                                            <div class="col-md-6">
                                                <h4 class="my-0 text-primary">{{ __('reports.current_rank_status') }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="card-body" style="padding-top:0">
                                            <table class="rank_perfom_txt">
                                                <tr>
                                                    <td>{{ __('common.memberName') }} <strong> : </strong></td>
                                                    <td>
                                                        {{ $userData->userDetail->name }}&nbsp;&nbsp;
                                                        {{ $userData->userDetail->second_name }}<br>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        {{ __('reports.current_rank') }}
                                                        <strong> : </strong><br>
                                                    </td>
                                                    <td>{{ $userData->rankDetail ? $userData->rankDetail->name : 'Nill' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        {{ __('reports.next_rank') }}
                                                        <strong> : </strong><br>
                                                    </td>
                                                    @if ($nextRank)
                                                        <td>{{ $nextRank->first()->name }}</td>
                                                    @else
                                                        <td> <i>Nill</i></td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    @if ($rankUser && $activeCriteria->contains('slug', 'joiner-package'))
                                        Current Package : {{ $rankUser->user->package->name }} <br>
                                        {{ $nextRank->first()->rankCriteria ? 'Next Rank Package :'. $nextRank->first()->rankCriteria->name : 'No criteria configured for next rank'}}
                                    @else
                                        @if ($nextRank)
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead>
                                                        @foreach ($nextRank as $item)
                                                            <th>{{ $item->name }}</th>
                                                        @endforeach
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($activeCriteria as $actCriteria)
                                                            <tr>
                                                                @foreach ($nextRank as $k => $rank)
                                                                    <td>
                                                                        <label for="">{{ $actCriteria->name }}</label><br>
                                                                        @if ($actCriteria->slug == 'referral-count' && $criteria['referal_count'])
                                                                            {{ __('reports.current_referral_count') }}
                                                                            :
                                                                            {{ $userData->sponsor_descendant_count }}
                                                                            <br>
                                                                            @if ($rank)
                                                                                {{ __('reports.referral_count_for') }}
                                                                                {{ $rank->name }} :
                                                                                {{ $rank->rankDetails->referral_count }}
                                                                                <br>
                                                                                {{ __('reports.needed_referral_count') }} :
                                                                                {{ $rank->rankDetails->referral_count - $userData->sponsor_descendant_count }}
                                                                            @endif
                                                                        @endif
                                                                        @if ($actCriteria->slug == 'downline-member-count' && $criteria['downline_count'])
                                                                            {{ __('reports.current_downline_member_count') }}
                                                                            {{ $rankUser->rank->rankDetails->downline_count }}
                                                                            <br>
                                                                            @if ($rank)
                                                                                {{ __('reports.downline_member_count_for') }}
                                                                                {{ $rank->name }}
                                                                                {{ $rank->rankDetails->downline_count }}
                                                                                <br>
                                                                                {{ __('reports.needed_downline_count') }}
                                                                                {{ $rank->rankDetails->downline_count - $rankUser->rank->rankDetails->downline_count }}
                                                                            @endif
                                                                        @endif
                                                                        @if ($actCriteria->slug == 'personal-pv' && $criteria['personal_pv'])
                                                                            {{ __('reports.current_personal_pv') }} :
                                                                            {{ $rankUser->rank->rankDetails->personal_pv }}
                                                                            <br>
                                                                            @if ($rank)
                                                                                {{ __('reports.needed_personal_pv') }} :
                                                                                {{ $rank->rankDetails->personal_pv - $rankUser->rank->rankDetails->personal_pv }}
                                                                            @endif
                                                                        @endif
                                                                        @if ($actCriteria->slug == 'group-pv' && $criteria['group_pv'])
                                                                        {{ $rank->name }} <br>
                                                                        {{ $actCriteria->slug  }} <br>
                                                                            {{ __('reports.current_group_pv') }} :
                                                                            {{ $rankUser->rank->rankDetails->group_pv }}
                                                                            <br>
                                                                            @if ($rank)
                                                                                {{ __('reports.needed_group_pv') }} :
                                                                                {{ $rank->rankDetails->group_pv - $rankUser->rank->rankDetails->group_pv }}
                                                                            @endif
                                                                        @endif

                                                                        @if ($actCriteria->slug == 'downline-rank-count' && $criteria['downline_rank'])
                                                                            <label for="">
                                                                                {{ __('reports.current_downline_rank_count') }}
                                                                            </label>
                                                                            @foreach ($downlineRankCount as $item)
                                                                                @if ($item['rank'])
                                                                                    <li>{{ $item['rank']->name }} :
                                                                                        {{ $item['count'] }}</li>
                                                                                @endif
                                                                            @endforeach
                                                                            @if ($rank)
                                                                                <label for="">
                                                                                    {{ __('reports.needed_downline_rank_count') }}
                                                                                    :
                                                                                    @foreach ($rank->downlineRankCount as $rnk)
                                                                                        <li>{{ $rnk->name }} :
                                                                                            {{ $rnk->pivot->count }}</li>
                                                                                    @endforeach <br>
                                                                                </label>
                                                                            @endif
                                                                        @endif

                                                                        @if ($actCriteria->slug == 'downline-package-count' && $criteria['downline_package_count'])
                                                                            <label for="">
                                                                                {{ __('reports.current_dwnline_package_count') }}
                                                                            </label> <br>
                                                                            @foreach ($downlinePackageCount as $item)
                                                                                <li>{{ $item['package']->name }} :
                                                                                    {{ $item['count'] }}</li>
                                                                            @endforeach <br>
                                                                            @if ($rank)
                                                                                <label for="">
                                                                                    {{ __('reports.needed_downline_package_count') }}
                                                                                    :
                                                                                </label>
                                                                                <br>
                                                                                @foreach ($rank->downinePackCount as $pack)
                                                                                    <li>
                                                                                        {{ $pack->name }} :
                                                                                        {{ $pack->pivot->count }}
                                                                                    </li>
                                                                                @endforeach <br>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @empty
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.js-example-basic-multiple').select2();
                getUsers();

            });

            $(document).on('change', '#filter_type', function() {

                if ($('#filter_type').val() == "custom") {
                    $("#customRange").css("display", "block");
                    $("#customRange").css("margin-top", "27px");

                    // $("#filterType").css("display", "none");

                } else {
                    $("#customRange").css("display", "none");
                }
            });


            function validUser(username) {
                var dataString = "username=" + username;

                $.ajax({
                    type: "GET",
                    url: "{{ route('validate.user') }}",
                    data: dataString,
                    success: function(result) {

                        if (result['status'] == "not_exist") {
                            $(error).text(result['message']);
                        }

                    },
                    error: function(passParams) {

                    }
                });
            }

            $('#userData').select2(

                {

                    placeholder: 'Username',
                    ajax: {
                        url: "{{ route('load.users') }}",
                        dataType: 'json',
                        delay: 250,

                        processResults: function(result) {

                            return {
                                results: $.map(result.data, function(item) {
                                    return {
                                        text: item.username,
                                        id: item.id,

                                    }
                                })
                            };

                        },
                        cache: true
                    }

                });
        </script>
    @endpush
