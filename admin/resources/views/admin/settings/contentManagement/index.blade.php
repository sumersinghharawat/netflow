@extends('layouts.app')
@section('title', 'Content Management')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>{{ __('settings.content_management') }}</h4>
            </div>
        </div>
    </div><br>
    <div class="row">
        <div class="card">
            <div class="card-body">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#welcomePanel" role="tab"
                            aria-selected="true">
                            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                            <span class="d-none d-sm-block"> {{ __('settings.welcome_letter') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#termsPanel" role="tab" aria-selected="false">
                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                            <span class="d-none d-sm-block">{{ __('settings.terms_and_conditions') }}</span>
                        </a>
                    </li>
                    @if ($moduleStatus->replicated_site_status)
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#replicaPanel" role="tab" aria-selected="false">
                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                <span class="d-none d-sm-block">{{ __('settings.replication_site') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>

                <!-- Tab panes -->
                <div class="tab-content pt-3 text-muted">
                    <div class="tab-pane active" id="welcomePanel" role="tabpanel">
                        <table class="table">
                            <tr>
                                <td>#</td>
                                <td>{{ __('common.language') }}</td>
                                <td>{{ __('common.content') }}</td>
                                <td>{{ __('common.action') }}</td>
                            </tr>
                            @forelse ($welcomeletter as $item)
                                <tr>
                                    <td>1</td>
                                    <td>{{ $item->language->name }}</td>
                                    <td>
                                        @if (isset($item->content))
                                            {!! substr($item->content, 0, 15) . '..' !!}
                                        @endif
                                    </td>
                                    <td><button class="btn btn-outline-success" data-bs-toggle="modal"
                                            data-bs-target="#welcome-{{ $item->id }}"><i class="bx bxs-pencil"></i></button></td>
                                </tr>
                            @empty

                            @endforelse

                        </table>
                    </div>
                    <div class="tab-pane" id="termsPanel" role="tabpanel">
                        <table class="table">
                            <tr>
                                <td>#</td>
                                <td>{{ __('common.language') }}</td>
                                <td>{{ __('common.content') }}</td>
                                <td>{{ __('common.action') }}</td>
                            </tr>
                            @forelse ($termsandcond as $item)
                                <tr>
                                    <td>1</td>
                                    <td>{{ $item->language->name }}</td>
                                    <td>
                                        @if ($item)
                                            {!! substr($item->terms_and_conditions, 0, 15) . '..' !!}
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#tc-{{ $item->id }}"><i
                                                class="bx bxs-pencil"></i></button>
                                    </td>
                                </tr>
                            @empty

                            @endforelse
                        </table>
                    </div>
                    @if ($moduleStatus->replicated_site_status)
                        <div class="tab-pane" id="replicaPanel" role="tabpanel">
                            <h4>{{ __('replica.replica_stite') }}</h4>
                            <div class="container-fluid">
                                <div class="card-box">
                                    <h4>{{ __('replica.top_banner') }} ({{ __('common.default') }})</h4>
                                    <form method="post"
                                        action="{{ route('bannerdefault.update') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <br>
                                        <div class="row">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="language"
                                                    value="Top Banner (Default)" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group">
                                                {{ __('replica.upload_top_banner') }}*
                                                <input type="file" class="form-control" name="banner[]"
                                                    value="Top Banner (Default)" multiple>
                                                Note: Please choose a png/jpeg/jpg file. Max size 2MB
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group">
                                                {{ __('replica.current_top_banner') }} *
                                                <input type="text" class="form-control" name="language"
                                                    value="{{ $default_banner[0]->image ?? '' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary" type="submit">{{ __('common.update') }}</button>
                                        </div>
                                    </form>

                                </div>
                                @if ($banner)
                                    <div class="card-box">
                                        <h4>{{ __('replica.top_banner') }}</h4>
                                        <form method="post" action="{{ route('banner.update', $banner->id ?? null) }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <br>
                                            <div class="row">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="language"
                                                        value="Top Banner" readonly>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                {{ __('replica.upload_top_banner') }}*
                                                    <input type="file" class="form-control" name="banner"
                                                        value="Top Banner (Default)">
                                                    Note: Please choose a png/jpeg/jpg file. Max size 2MB
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    {{ __('replica.current_top_banner') }} *
                                                    <input type="text" class="form-control" name="language"
                                                        value="{{ $banner->image ?? '' }}" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button class="btn btn-primary" type="submit" id="edit">{{ __('common.update') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div class="card-box">
                                        <h4>{{ __('replica.top_banner') }} ({{ __('common.for_admin') }})</h4>
                                        <form method="post" action="{{ route('banner.create') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <br>
                                            <div class="row">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="language"
                                                        value="Top Banner" readonly>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                {{ __('replica.upload_top_banner') }}*
                                                    <input type="file" class="form-control" name="banner"
                                                        value="Top Banner (Default)">
                                                    Note: Please choose a png/jpeg/jpg file. Max size 2MB
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group">
                                                    {{ __('replica.current_top_banner') }} *
                                                    <input type="text" class="form-control" name="language" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-primary" type="submit" id="submit">{{ __('common.add') }}</button>
                                            </div>
                                        </form>

                                    </div>
                                @endif
                                <div class="panel-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>1</th>
                                                <th>{{ __('common.language') }}</th>
                                                <th>{{ __('common.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($languages as $item)
                                                <tr>
                                                    <td>{{ $loop->index +1 }}</td>
                                                    <td>{{ $item->name_in_english }}</td>
                                                    <td><a href="{{ route('replication.site.edit', ['id' => $item->id]) }}">{{ __('common.edit') }}</a>
                                                    </td>
                                                </tr>
                                            @empty

                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
    </div>
    </div>
    @include('admin.settings.contentManagement.models._model')
@endsection

