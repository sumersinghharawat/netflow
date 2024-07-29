@extends('layouts.app')
@section('title', __('register.bulk_register'))
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('register.bulk_register') }}</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p><strong>{{ __('common.whoops_error') }}</strong></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row">
                <div class="col-6">
                    <form action="{{ route('user.bulkRegister.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <label class="mb-2">{{ __('common.select_file') }}<span class="text-danger">*</span></label>

                        <div class="hstack gap-1">
                            <input class="form-control me-auto @error('file') is-invalid @enderror" type="file"
                                name="file" required>
                            <button type="submit" class="btn btn-primary">{{ __('common.submit') }}</button>
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>

                </div>

                <div class="col-6">
                    <div class="float-end">
                        @if ($moduleStatus['mlm_plan'] == 'Binary')
                            @if ($moduleStatus->ecom_status)
                                <a href="{{ asset('excel_register/sample_excel_binary_ecom.xlsx') }}" type="button"
                                    class="btn btn-primary waves-effect btn-label waves-light mt-lg-4"
                                    download="sample.xlsx"><i class="bx bx-download label-icon"></i>
                                    {{ __('common.download') }}</a>
                            @else
                                <a href="{{ asset('excel_register/sample_binary.xlsx') }}" type="button"
                                    class="btn btn-primary waves-effect btn-label waves-light mt-lg-4"
                                    download="sample.xlsx"><i class="bx bx-download label-icon"></i>
                                    {{ __('common.download') }}</a>
                            @endif
                        @else
                            @if ($moduleStatus->ecom_status)
                                <a href="{{ asset('excel_register/sample_excel_ecom.xlsx') }}" type="button"
                                    class="btn btn-primary waves-effect btn-label waves-light mt-lg-4"
                                    download="sample.xlsx"><i class="bx bx-download label-icon"></i>
                                    {{ __('common.download') }}</a>
                            @else
                                <a href="{{ asset('excel_register/sample.xlsx') }}" type="button"
                                    class="btn btn-primary waves-effect btn-label waves-light mt-lg-4"
                                    download="sample.xlsx"><i class="bx bx-download label-icon"></i>
                                    {{ __('common.download') }}</a>
                            @endif
                        @endif

                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
