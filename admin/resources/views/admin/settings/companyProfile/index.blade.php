@extends('layouts.app')
@push('page-style')
    <style>
        .tooltip{left: inherit !important;right: inherit !important;}
    </style>
@endpush
@section('title', 'Company Profile')
@section('content')
    <div class="container-fluid mt-1">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('company.company_profile') }}</h4>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-4">

                        <form action="{{ route('companyProfile.update', $profile->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <span class="form-text size-15">{{ __('company.site_info') }}</span>
                            <div class="form-group mt-3">
                                <label class="fw-bolder">{{ __('company.company_name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ $profile->name }}"
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="fw-bolder">{{ __('company.company_address') }}<span
                                        class="text-danger">*</span></label>
                                <textarea name="address" id="" class="form-control h-75 @error('address') is-invalid @enderror">{{ $profile->address }}</textarea>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="fw-bolder">{{ __('company.email') }}<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text mdi mdi-email" id="email"></span>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        aria-label="email" aria-describedby="email" name="email" value="{{ $profile->email }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="form-group">

                                <label class="fw-bolder">{{ __('company.phone') }}<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text mdi mdi-cellphone" id="phone"></span>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        aria-label="phone" aria-describedby="phone" name="phone" value="{{ $profile->phone }}">
                                </div>
                                @error('phone')
                                    <span class="text-danger ">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>


                        </form>
                    </div>
                <!-- <hr class="bg-danger border-2 border-top border-primary"> -->
                <div class="col-xl-8">
                    <h4 class="mb-sm-0 font-size-18">{{ __('company.logo_favicon') }}</h4>
                    <div class="mt-3 alert alert-primary po-0" role="alert">
                        <ul>
                            <li>{{ __('common.maximum_size_allowed') }}: 2MB</li>
                            <li>{{ __('common.file_types_allowed') }}: png | jpeg | jpg | gif | ico</li>
                            <li>{{ __('common.ideal_image_dimension_for_light_background_logo') }}: 200x44 pixel</li>
                            <li> {{ __('common.ideal_image_dimension_for_dark_background_logo') }}: 200x44 pixel</li>
                            <li>{{ __('common.ideal_image_dimension_for_collapsed_logo') }}: 55 x 55
                                pixels</li>
                            <li> {{ __('common.ideal_image_dimension_for_favicon') }}: 32 x 32 pixels
                            </li>
                        </ul>
                    </div>

                    <div class="upload-company-photos">
                        <div class="row gy-4">
                            <div class="col-md-3 col-12">
                                <div class="card border row h-100 uploadlogo-box">
                                    <div class="card-body">
                                        <span class="form-text upload_logo_head">{{ __('company.logo_for_light_background') }} </span>
                                        <div>
                                            <form action="{{ route('companyLogo.update', $profile->id) }}" method="post"
                                                class="dropzone" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="logoType" value="logo">
                                                <div class="fallback">
                                                    <input name="login_logo" type="file" id="login_logo">
                                                </div>
                                                @if (isset($profile->logo))
                                                    <div class="dz-preview dz-message dz-image-preview needsclick"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Drop files here or click to upload.">
                                                        <img style="z-index: auto;" src="{{ $profile->logo }}" class="dz-image"
                                                            id="darkBackground">
                                                    </div>
                                                @else
                                                    <div class="dz-message needsclick">
                                                        <div class="mb-1">
                                                            <i class="display-4 text-muted bx bxs-cloud-upload"></i>
                                                        </div>
                                                        <h6>Drop files here or click to upload.</h6>
                                                    </div>
                                                @endif
                                            </form>
                                            @if (isset($profile->logo))
                                                <form action="{{ route('companyProfile.deleteImage') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="type" value="logo">
                                                    <div class="text-center mt-2">
                                                        <button type="submit" class="btn btn-danger waves-effect waves-light uploadimage-delete-btn"><i class="fa fa-trash"></i></button>
                                                    </div>
                                                </form>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-3 col-12">
                                <div class="card border row h-100 uploadlogo-box">
                                    <div class="card-body">
                                        <span class="form-text upload_logo_head">{{ __('company.logo_for_dark_background') }} </span>
                                        <div class="upload-dropzone">
                                            <form action="{{ route('companyLogo.update', $profile->id) }}" method="post"
                                                    class="dropzone" enctype="multipart/form-data">
                                                    @csrf
                                                <input type="hidden" name="logoType" value="logo_dark">
                                                <div class="fallback">
                                                    <input name="logo" type="file" id="logo"
                                                        onchange="logoChange(event)">
                                                </div>
                                                @if (isset($profile->logo_dark))
                                                    <div class="dz-preview dz-message dz-image-preview needsclick"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Drop files here or click to upload.">
                                                        <img style="z-index: auto;" src="{{ $profile->logo_dark }}" class="dz-image"
                                                            id="darkBackground">
                                                    </div>
                                                @else
                                                    <div class="dz-message needsclick">
                                                        <div class="mb-1">
                                                            <i class="display-4 text-muted bx bxs-cloud-upload"></i>
                                                        </div>

                                                        <h6>Drop files here or click to upload.</h6>
                                                    </div>
                                                @endif
                                            </form>
                                        </div>

                                        @if (isset($profile->logo_dark))
                                            <form action="{{ route('companyProfile.deleteImage') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="type" value="logo_dark">
                                                <div class="text-center mt-2">
                                                    <button type="submit" class="btn btn-danger waves-effect waves-light uploadimage-delete-btn"><i class="fa fa-trash"></i></button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="card border row h-100 uploadlogo-box">
                                    <div class="card-body">
                                        <span class="form-text upload_logo_head">{{ __('company.logo_for_collapsed_sidebar') }} </span>
                                        <div>
                                            <form action="{{ route('companyLogo.update', $profile->id) }}" method="post"
                                                class="dropzone" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="logoType" value="shrink_logo">
                                                <div class="fallback">
                                                    <input name="shrink_logo" type="file" id="shrink_logo"
                                                        onchange="shrinkLogoChange(event)">
                                                </div>
                                                @if (isset($profile->logo_shrink))
                                                    <div class="dz-preview dz-message dz-image-preview needsclick"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Drop files here or click to upload.">
                                                        <img style="z-index: auto;" src="{{ $profile->logo_shrink }}" class="dz-image"
                                                            id="shrinkLogo">
                                                    </div>
                                                @else
                                                    <div class="dz-message needsclick">
                                                        <div class="mb-1">
                                                            <i class="display-4 text-muted bx bxs-cloud-upload"></i>
                                                        </div>

                                                        <h6>Drop files here or click to upload. {{ $profile->logo_shrink }}
                                                        </h6>
                                                    </div>
                                                @endif
                                            </form>

                                            @if (isset($profile->logo_shrink))
                                            <form action="{{ route('companyProfile.deleteImage') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="type" value="shrink_logo">
                                                <div class="text-center mt-2">
                                                    <button type="submit" class="btn btn-danger waves-effect waves-light uploadimage-delete-btn"><i class="fa fa-trash"></i></button>
                                                </div>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="card border row h-100 uploadlogo-box">
                                    <div class="card-body">
                                        <span class="form-text upload_logo_head">{{ __('company.favicon') }} </span>
                                        <div>
                                            <form action="{{ route('companyLogo.update', $profile->id) }}" method="post"
                                                class="dropzone" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="logoType" value="favicon">
                                                <div class="fallback">
                                                    <input type="file" name="favicon" id="favicon">
                                                </div>

                                                @if (isset($profile->favicon))
                                                    <div class="dz-preview dz-message dz-image-preview"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Drop files here or click to upload.">
                                                        <div class="dz-image" style="z-index: auto;" >
                                                            <img src="{{ $profile->favicon }}" class="w-100" id="favicon">
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="dz-message needsclick">
                                                        <div class="mb-1">
                                                            <i class="display-4 text-muted bx bxs-cloud-upload"></i>
                                                        </div>
                                                        <h6>Drop files here or click to upload.</h6>
                                                    </div>
                                                @endif
                                            </form>

                                            @if (isset($profile->favicon))
                                            <form action="{{ route('companyProfile.deleteImage') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="type" value="favicon">
                                                <div class="text-center mt-2">
                                                    <button type="submit" class="btn btn-danger waves-effect waves-light uploadimage-delete-btn"><i class="fa fa-trash"></i></button>
                                                </div>
                                            </form>
                                            @endif
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
    </div>

@endsection
@push('scripts')
    <script>
        Dropzone.autoDiscover = false;
        let dropzoneForm = document.querySelector(".dropzone");
        dropzoneForm.addEventListener("submit", function(event) {
            event.preventDefault();
        });
        let myDropzones = [];
        let dropzoneElements = document.querySelectorAll('.dropzone');
        for (let i = 0; i < dropzoneElements.length; i++) {
            let dropzoneEl = dropzoneElements[i];
            let myDropzone = new Dropzone(dropzoneEl, {
                url: `{{ route('companyLogo.update', $profile->id) }}`,
                params: function() {
                    let logoTypeInput = this.element.querySelector('[name="logoType"]');
                    let logoType = logoTypeInput ? logoTypeInput.value : null;
                    return { logoType: logoType };
                },
                acceptedFiles: 'image/*',
                maxFiles: 1,
                clickable: true,
                // dictRemoveFile: "Remove",
            });
            myDropzones.push(myDropzone);
            myDropzone.on("error", function(file, response) {
                let errorMsg = response.message ? response.message : "An unknown error occurred.";
                let errorEl = file.previewElement.querySelector(".dz-error-message");
                errorEl.innerHTML = errorMsg;
                errorEl.style.display = "block";
                notifyError(errorMsg);

            });
            myDropzone.on( "success",function(file, response) {
                notifySuccess("Company Profile updated Successfully");
            });
        }
        function remove_image(el){
            console.log(el);
            document.getElementById(el).remove();
        }

        $(document).on('click', '.company-photo-delete-btn', function() {
            console.log('zvcx');
        });

    </script>
@endpush
