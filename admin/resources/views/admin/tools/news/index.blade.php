@extends('layouts.app')
@section('title', 'News')

@section('content')

<style>
    .tooltip {
  position: relative !important;
  display: inline-block !important;
  opacity: 1;
  top: 0;
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  padding: 5px 0;
  border-radius: 6px;
    left: 0;
    right: 0;
    top: -50px;
    margin: auto;
  /* Position the tooltip text - see examples below! */
  position: absolute;
  z-index: 1;
}
.tooltip .tooltiptext::before{
    content: '';
    border-width: 0.5rem 0.7rem 0;
    border-color: transparent;
    border-top-color: #000;
    /* border-color: transparent; */
    border-style: solid;
    position: absolute;
    left: 0;
    right: 0;
    margin: auto;
    bottom: -7px;
    width: 20px;
}
/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}
</style>
    <div class="row">
        <div class="col-md-6">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('tools.news') }}</h4>
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <div class="text-right">
                <a href="{{ route('news.add') }}" class="btn btn-primary">{{ __('tools.add_news') }}</a>
            </div>
        </div>
    </div><br>

    <div class="row">
        @forelse ($news as $item)
            <div class="col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-6">
                                <div class="text-lg-center">
                                    <div class="me-3 mb-3 mt-1">
                                        <div class="avatar-title">
                                            <img src="{{ $item->image }}" alt="404" class="img-fluid">

                                        </div>

                                    </div>

                                    <li class="list-inline-item mt-1">
                                        <h5 class="font-size-14">
                                            <a class="btn btn-outline-secondary" title="edit"
                                                href="{{ route('news.edit', $item->id) }}">
                                                <i class="bx bxs-pencil"></i></a>

                                            <a class="btn btn-outline-danger deleteButton" title="delete"
                                                id="{{ $item->id }}" onclick="formSubmit({{ $item->id }})">
                                                <i class="fas fa-trash"></i></a>
                                            <form action="{{ route('news.delete', $item->id) }}" method="post"
                                                class="d-none" id="deleteForm{{ $item->id }}">
                                                @csrf
                                            </form>

                                        </h5>
                                    </li>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-8 col-sm-6">
                                <div>
                                    <span
                                        class="d-block text-primary text-decoration-none mb-2 fw-bolder">{{ $item->title }}</span>
                                    <p class="text-justify mb-4 mb-lg-5">{{ $item->description }}</p>
                                    <ul class="list-inline mb-0">

                                        <h5 class="font-size-14 mt-2 tooltip" data-bs-toggle="tooltip1" data-bs-placement="top"
                                            title="Created Date" style="z-index: auto;"><i class="bx bx-calendar me-1 text-muted"></i>
                                            {{ $item->created_at->format('Y-m-d') }}
                                            <span class="tooltiptext">Created Date</span>
                                        </h5>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="nodata_view card-body">
                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                    <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
                </div>
            </div>
        @endforelse
    </div>
@endsection
@push('scripts')
    <script>
        function formSubmit(id) {

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, delete it!",
            }).then(function(t) {
                if (t.isConfirmed == true) {
                    t.value &&
                        Swal.fire(
                            "Deleted!",
                            "Your file has been deleted.",
                            "success"
                        );
                    let form = document.getElementById("deleteForm" + id);
                    form.submit();
                }
            });
        }
    </script>
@endpush
