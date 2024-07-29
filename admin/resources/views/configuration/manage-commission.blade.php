@extends('layouts.app')
@section('title', 'commission status')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>{{ __('common.commission_status') }}</h4>
            </div>
        </div>
    </div><br>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <form class="row  g-3 align-items-center">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="formrow-inputState" class="form-label">{{ __('package.select_order_type') }}</label>
                            <select name="type" class="form-select">
                                <option value="overall" @selected(request()->input('type') == 'overall')>{{ __('commission.overall') }}</option>
                                <option value="initialised" @selected(request()->input('type') == 'initialised')>{{ __('commission.initialized') }}
                                </option>
                                <option value="processing" @selected(request()->input('type') == 'processing')>{{ __('commission.processing') }}
                                </option>
                                <option value="success" @selected(request()->input('type') == 'success')>{{ __('commission.success') }}</option>
                                <option value="failed" @selected(request()->input('type') == 'failed')>{{ __('commission.failed') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 d-flex">
                        <button type="submit" class="btn btn-primary w-md mt-2">{{ __('common.submit') }}</button>
                        <a href="{{ route('manage.commission') }}"
                            class="btn btn-danger w-md ms-2 mt-2">{{ __('common.reset') }}</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body" id="epinTable">
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-check" id="epinlist">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th class="align-middle">{{ __('commission.commission') }}</th>
                                    <th class="align-middle">{{ __('common.username') }}</th>
                                    <th class="align-middle">{{ __('common.date') }}</th>
                                    <th class="align-middle">{{ __('common.status') }}</th>
                                    <th class="align-middle">{{ __('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($commissions as $commission)
                                    <tr>
                                        <td>{{ $loop->index + $commissions->firstItem() }}</td>
                                        <td>{{ trans('commission.' . $commission->commission) }}</td>
                                        <td>{{ $commission->user ? $commission->user->username : 'N/A' }}</td>
                                        <td>{{ $commission->date }}</td>
                                        <td>{{ trans('commission.' . $commission->status) }}</td>
                                        <td>
                                            @if ($commission->status == 'failed')
                                               <button class="btn-danger"><i class="mdi mdi-restart"></i></button>
                                            @endif
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
                        <div class="pagination_new"> {{ $commissions->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
