@extends('layouts.app')
@section('title', __('settings.api_key'))
@section('content')

    <div class="container-fluid settings_page ">
        <div class="card">
            <div class="card-header ">
                @include('admin.settings.inc.links')
                <div class="">
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        <h4><i class="fas fa-key"></i>
                            {{ __('settings.api_key') }}
                        </h4>
                        <p class="text-justify mt-lg-2">


                            {{ __('settings.api_key_description') }}
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6><strong>{{ __('settings.api_base_url') }}:</strong> </h6>
                    </div>
                    <div class="col-md-8">
                        {{ url('/') }}/api
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4">
                        <h6><strong>{{ __('settings.api_doc_link') }}:</strong> </h6>
                    </div>
                    <div class="col-md-8">
                        <a target="_blank"
                            href="https://infinitemlmsoftware.com/docs/integration/rest-api">https://infinitemlmsoftware.com/docs/integration/rest-api</a>
                    </div>
                </div><br>
                <form method="post" action="{{ route('update.apiKey') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <h6><strong>{{ __('settings.api_key') }}:</strong> </h6>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="apiKey" value="{{ $api }}" class="form-control"
                                id="apiField" readonly>
                            @if (config('mlm.demo_status') == 'yes')
                                <input type="hidden" name="prefix"
                                    value="{{ config('database.connections.mysql.prefix') }}">
                            @endif
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-primary" type="button"
                                onclick="generateApi()">{{ __('common.generate') }}</button>
                            <button class="btn btn-primary" onclick="this.form.submit()">{{ __('common.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>



    </div>
@endsection
@push('scripts')
    <script>
        function generateApi() {

            $key = generateUUID();
            //alert($key);
            document.getElementById('apiField').value = $key



        }

        function generateUUID() {
            var d = new Date().getTime();

            if (window.performance && typeof window.performance.now === "function") {
                d += performance.now();
            }

            var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = (d + Math.random() * 16) % 16 | 0;
                d = Math.floor(d / 16);
                return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });

            return uuid;
        }
    </script>
@endpush
