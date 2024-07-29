@extends('layouts.app')
@section('title', 'Re-entry')

@section('content')
<div class="row">
    <div class="col-12 genealogy_page_head_top">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('tree.re_entry') }}</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="card search_box_tree_view">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <label for="">Reentry count:</label>
                    <span>{{ $user->reentries_count }}</span>
                </div>
                <div class="col-lg-6 col-md-12">
                    <form action="{{ route('network.reentry.table') }}">
                        <input type="hidden" name="search" value="true">
                        <div class="tree_view_right_srch_sec">
                            <span>
                                <select name="user" class="form-control treeview_frm_input select2-search-user">
                                    @isset($user)
                                        <option selected value="{{ $user->id }}">{{ $user->username }}</option>
                                    @endisset
                                </select>
                            </span>
                            <span>
                                <div class="form-group m-b-n-xs">
                                    <button
                                        class="btn btn-sm btn-primary treeview_srch_btn">{{ __('common.search') }}</button>
                                    <a class="btn btn-sm btn-info treeview_rst_btn"
                                        href="{{ route('network.reentry.table') }}">{{ __('common.reset') }} </a>
                                </div>
                            </span>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <table id="datatable-view-referral" class="table nowrap w-100">
                    <thead>
                        <th> {{ __('common.name') }}</th>
                        <th>{{ __('common.from') }}</th>
                        <th>{{ __('common.entry_date') }} </th>
                    </thead>
                    <tbody>
                        @forelse ($user->reentries as $item)
                            <tr>
                                <td>{{ $item->username }}</td>
                                <td>{{ $item->reentryParent->parentDetail->username }}</td>
                                <td>{{ Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                            </tr>
                        @empty

                        @endforelse
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
            getUsers();
        });
    </script>
@endpush
