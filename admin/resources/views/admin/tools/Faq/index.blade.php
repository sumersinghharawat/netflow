@extends('layouts.app')
@section('title', 'FAQ')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">FAQ</h4>
            </div>
        </div>
    </div><br>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body filter-report">
                <form action="{{ route('faq.create') }}" method="post" class="custom-validation">
                    @csrf
                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('tools.sort_order') }}</label>
                                <input data-parsley-type="number" type="number" min="0" class="form-control"
                                    required placeholder="Enter only numbers" name="sort_order" id="sortOrder"
                                    oninput="validity.valid||(value='');" max=99999>
                                @error('sort_order')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('tools.quetion') }}</label>
                                <input type="text" name="question" class="form-control" value="{{ old('question') }}"
                                    required>
                                @error('question')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>{{ __('common.answer') }}</label>
                                <input type="text" name="answer" class="form-control" value="{{ old('answer') }}"
                                    required>
                                @error('answer')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-1" style="margin-top:27px">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"
                                    id="submitButton">{{ __('common.add') }}</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
    <div class="checkout-tabs">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="v-pills-tabContent">

                            @forelse ($faq as $item)
                                <div class="faq-box d-flex mb-4">
                                    <div class="flex-shrink-0 me-3 faq-icon">
                                        <i class="bx bx-help-circle font-size-20 text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">

                                        <a href="{{ route('faq.delete', ['id' => $item->id]) }}"
                                            class="btn btn-sm btn-outline-danger float-end" value="{{ $item->id }}"><i
                                            class="fa fa-trash" aria-hidden="true"></i></a>
                                            <h5 class="font-size-15"> <small>
                                                {{ $item->sort_order }}
                                            </small>. &nbsp;{{ $item->question . '?' }}</h5>
                                            {{-- <a href="" ><small>Edit</small></a> --}}
                                            <p class="text-muted">{{ $item->answer }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="nodata_view">
                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                    <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ $faq->links() }}
    </div>
@endsection
@push('scripts')
    <script>
        $('#sortOrder').focusout(function() {
            let data = {
                'sort_order': $(this).val()
            }
            let id = $(this).attr('id')

            checkSortOrder(data, id)
        });
        const checkSortOrder = async (data, id) => {
            $('.invalid-feedback').remove()
            $(`input[id="${id}"]`).removeClass("is-invalid")
            $('#submitButton').removeClass('disabled');
            const res = await $.post(`check/sort-order/`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        $('#submitButton').addClass('disabled');
                        $(this).removeClass('is-valid');
                        $('#submitButton').addClass('disabled')
                        inputvalidationError(id, err)
                    }
                })
            $('#sortOrder').addClass('is-valid')


        }
        // const deleteFAQ = () => {
        //     try {
        //         let data = {
        //             id: $('#username').val(),
        //         }

        //         var url = "{{ route('export.purchaseReport.csv') }}?" + $.param(data)
        //         var a = document.createElement("a");
        //         a.href = url;
        //         a.download = "purchase_report.csv";
        //         document.body.appendChild(a);
        //         a.click();

        //     } catch (error) {
        //         console.log(error);
        //     }
        // }
    </script>
@endpush
