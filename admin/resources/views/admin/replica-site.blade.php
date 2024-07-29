@extends('layouts.header')
@section('content')
    <div class="container">
        <h4>{{ __('replica.replica_site') }}</h4>

        <div class="container">
            <div class="panel-body">
                <form method="post" action="{{ route('bannerdefault.update') }}" enctype="multipart/form-data">
                    @csrf
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <input type="text" class="form-control" name="language" value="Top Banner (Default)" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.upload_top_banner') }}*
                            <input type="file" class="form-control" name="banner" value="Top Banner (Default)">
                            Note: Please choose a png/jpeg/jpg file. Max size 2MB
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.current_top_banner') }}*
                            <input type="text" class="form-control" name="language" value="{{ $default_banner }}"
                                readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit">{{ __('common.update') }}</button>
                    </div>
                </form>
            </div>

            <div class="panel-body">
                <form method="post" action="{{ route('banner.update') }}" enctype="multipart/form-data">
                    @csrf
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <input type="text" class="form-control" name="language" value="Top Banner" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.upload_top_banner') }}*
                            <input type="file" class="form-control" name="banner" value="Top Banner (Default)">
                            Note: Please choose a png/jpeg/jpg file. Max size 2MB
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.current_top_banner') }} *
                            <input type="text" class="form-control" name="language" value="{{ $banner }}" readonly>
                        </div>
                    </div>


                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit">{{ __('common.update') }}</button>
                    </div>
                </form>
            </div>
            <div class="panel-body">
                <table class="table">
                    <tr>
                        <td>1</td>
                        <td>{{ __('common.language') }}</td>
                        <td>{{ __('common.action') }}</td>

                    </tr>
                    <tr>
                        <td>1</td>
                        <td>{{ __('common.english') }}</td>
                        <td><a href="{{ route('replication.site.edit') }}">{{ __('common.edit') }}</a>
                        </td>

                    </tr>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#summernote').summernote();
            });
        </script>
    @endpush
@endsection
