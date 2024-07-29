@extends('layouts.app')
@section('title', __('common.activity'))
@section('content')
    <div class="row d-print-none">
        <h4>{{ __('common.activity') }}</h4>
        <div class="card-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form method="get" id="userform" action="{{ route('user.activity') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">

                                <div class="ajax-select mt-3 mt-lg-0">
                                    <label class="form-label">{{ __('common.select_user') }}</label>
                                    <select name="username" id="username"
                                        class="form-control select2-ajax select2-multiple select2-search-user"></select>
                                </div>

                                <span id="error" style="color: red;">
                                </span>
                            </div>
                            <div class="col-md-2" style="margin-top:3px">
                                <button type="submit" class="btn btn-primary mob_mrg_0"
                                    style="margin-top: 26px;">{{ __('common.view') }}</button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>
                <div class="table-responsive">
                    <table class="table  m-b-none">
                        <thead>
                            <tr class="th">

                                <th>#</th>
                                <th>{{ __('common.user') }}</th>
                                <th>{{__('common.ip')}}
                                <th>{{ __('common.activity') }}</th>
                                <th>{{ __('common.usertype') }}</th>
                                <th>{{ __('common.description') }}</th>
                                <th>{{ __('common.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activity as $user)
                                <tr>
                                    <td>
                                        {{ $activity->firstItem() + $loop->index}}
                                    </td>
                                    <td>
                                        {{ $user->user->username }}
                                    </td>
                                    <td>
                                        {{ $user->ip }}
                                    </td>
                                    <td>
                                        {{ $user->activity }}
                                    </td>
                                    <td>
                                        {{ $user->user_type}}
                                    </td>
                                    <td>
                                        {{ $user->description}}
                                    </td>
                                    <td>
                                        {{ $user->created_at}}
                                    </td>

                                </tr>


                            @empty
                                <tr>
                                    <td colspan="100%">
                                        <div class="nodata_view">
                                            <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                            <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <span class="pagination_new d-print-none">{{ $activity->links() }}</span>
                </div>

            </div>
        </div>
        <div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
            aria-hidden="true" id="salesInvoice">
            <div class="modal-dialog modal-lg">

                <div class="modal-content">
                    <div class="modal-body">
                        <div id="invoice"></div>

                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
    <script>
        $(() => {
            getUsers();
        })
    </script>
    @endpush
