@extends('layouts.app')
@section('title', __('profile.kyc_details'))
@section('content')
    <div class="tab-pane active" id="kycDetails" role="tabpanel">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('profile.kyc_details') }}</h4>
        </div>

        <div class="card">
            <div class="card-body">

                <div class="row ">
                    <div class="col-md-2">
                        <label>{{ __('common.select_username') }}</label>
                        <select class="form-control select2-ajax select2-search-user select2-multiple" multiple="multiple"
                            id="KycUser" name="username"></select>
                    </div>

                    <div class="col-md-2">
                        <label>{{ __('common.status') }}</label>
                        <select name="status" class="form-select" id="kycStatus">
                            <option value="any" selected>{{ __('common.any') }}</option>
                            <option value="pending">{{ __('common.pending') }}</option>
                            <option value="approved">{{ __('common.approved') }}</option>
                            <option value="rejected">{{ __('common.rejected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>{{ __('common.select_category') }}</label>
                        <select class="form-select" name="type" id='category'>
                            <option value=''>{{ __('common.any') }}</option>
                            @foreach ($kyc_catg as $v)
                                <option value="{{ $v->id }}" @if (app('request')->input('type') == $v->id) selected @endif>
                                    {{ $v->category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3" style="margin-top:24px">
                        <a href="#" class="btn btn-primary"
                            onclick="filterSummaryReports()">{{ __('common.view') }}</a>
                        <a href="{{ route('profile.kycDetails') }}" type="submit"
                            class="btn btn-danger">{{ __('common.reset') }}</a>
                    </div>

                    <div class="col-md-4">
                        <p style="margin:0"> <u><a href="{{ route('payout') }}#kyc"
                                    class="text-info">{{ __('profile.manage_kyc_configuration') }}?</a> </u></p>
                    </div>
                </div>

            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <table id="kycTable" class="table  table-hover">
                    <thead class="table">
                    <tr>
                        <th><input type="checkbox" name="" id="checkAll"
                            class="form-check-input">
                        </th>
                        <th>{{ __('common.member_name') }}</th>
                        <th>{{ __('common.category') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('common.view') }}</th>
                        <th style="width: 0px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">{{ __('common.no_data') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal fade" id="terms" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-justify">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const kycImg = async (id) => {
            event.preventDefault()
            try {
                let url = "{{ route('kyc.image', 'id:') }}";
                url = url.replace('id:', id);
                const res = await $.get(`${url}`);
                console.log(res);
                let imagesHTML = ''; // Accumulate the HTML content of all images

                res.forEach(element => {
                console.log(element);
                imagesHTML += `<div class="modal-header" ><img id="borderimg1" class="img-fluid w-100" src="${element}" scrolling="no"></div>`;
                });

                $(".modal-body").html('');
                console.log(imagesHTML); // Clear the existing content
                $(".modal-body").append(imagesHTML);
            } catch (err) {
                console.log(err);
            }

        }

        $(() => {
            getUsers();
            getSummaryReports();
        });


        const getSummaryReports = () => {
            let url = "{{ route('kyc.details') }}";
            let params = {
                users: $('#KycUser').val(),
                status: $('#kycStatus').val(),
                category: $('#category').val(),
            }

            let columnNames = [
                {
                    data: 'checkbox',
                    name: 'checkbox',
                },
                {
                    data: 'member_name',
                    name: 'member_name',
                    title: 'Member Name',
                    visible: true,
                },
                {
                    data: 'category',
                    name: 'category',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'view',
                    name: 'view',
                },
                {
                    data: 'action',
                    name: 'action',
                }
            ];
            let newFilterColumnName = columnNames.map((value, index, array) => {
                var alter = {
                    title: value.title,
                    visible: value.visible,
                };
                return ({
                    ...value,
                    ...alter
                });
            });
            var table = $('#kycTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                "sDom": 'Lfrtlip',
                ajax: {
                    type: "GET",
                    url: url,
                    data: params,
                },
                columns: newFilterColumnName,

            });
        }

        const filterSummaryReports = () => {
            event.preventDefault();
            $('#kycTable').DataTable().clear().destroy();
            getSummaryReports();
        }

        const approvekyc = async (id) => {

            event.preventDefault();
            let checkedItems = [];
            let items_selected = $(".checked-box:checked");
            for await(item of items_selected) {
                checkedItems.push(item.value);
            }
            console.log(checkedItems);
            let confirm = await confirmApprove()
            if (confirm.isConfirmed == true) {
                let url = "{{ route('approve.kyc', ['checkedItems' => ':checkedItems']) }}";
                url = url.replace(':checkedItems', checkedItems);
                const res = await $.post(url)
                console.log(res);
                notifySuccess(res.message)
                location.reload();
            }
        };

        const rejectKyc = async (id) => {

            event.preventDefault();
            let checkedItems = [];
            let items_selected = $(".checked-box:checked");
            for await(item of items_selected) {
                checkedItems.push(item.value);
            }
            console.log(checkedItems);
            let confirm = await confirmReject()
            if (confirm.isConfirmed == true) {
                let url = "{{ route('reject.kyc', ['checkedItems' => ':checkedItems']) }}";
                url = url.replace(':checkedItems', checkedItems);
                const res = await $.post(url, {

                })
                notifySuccess(res.message)
                location.reload();
            }
        };

        $('#checkAll').on('click', function(e) {
            if ($('.checked-box').not(':disabled').length) {
                $('.checked-box').not(this).not(':disabled').prop('checked', this.checked);
                showActiveActionPopup();
                e.stopImmediatePropagation();
            }
        })

        function showActiveActionPopup() {
            let checkedItems = [];
            let items_selected = $(".checked-box:checked");
            for(item of items_selected) {
                checkedItems.push(item.value);
            }
            if ($(".checked-box:checked").length > 0) {
                // console.log(checkedItems);
                $('#active_items_selected_span').text(`${checkedItems.length} items selected`);
                $('#reg_approval_action_popup').removeClass('d-none');
            } else { // none is checked
                $('.checked-box').prop('checked', false);
                $('#approval_table #checkAll').prop('checked', false);
                $('#reg_approval_action_popup').addClass('d-none');
            }
        }

        $(document).on('click', '.checked-box', function(e){
            showActiveActionPopup();
        });
    </script>
@endpush
