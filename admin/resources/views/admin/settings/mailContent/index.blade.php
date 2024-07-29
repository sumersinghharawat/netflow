@extends('layouts.app')
@section('title', 'Mail Content')
@section('content')
        <h4>{{ __('mail.mail_content') }}</h4>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach ($commonMail as $item)
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabContent{{ $item->id }}"
                                    role="tab">
                                    <span
                                        class="d-sm-block text-capitalize">{{ str_replace('_', ' ', $item->mail_type) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content mt-3 text-muted">
                        @foreach ($commonMail as $item)
                            <div class="tab-pane" id="tabContent{{ $item->id }}" role="tabpanel">
                                <table class="table">
                                    <tr>
                                        <td>#</td>
                                        <td>{{ __('common.language') }}</td>
                                        <td>{{ __('mail.subject') }}</td>
                                        <td>{{ __('common.action') }}</td>
                                    </tr>
                                    @foreach ($languages as $key => $language)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $language->name }}</td>
                                            <td>
                                                {{ $item->subject }}
                                            </td>
                                            <td><a href="{{ route('mailcontent-edit', ['id' => $item->mail_type, 'language_id' => $language->id]) }}"
                                                    class="btn btn-outline-secondary"><i
                                                        class="bx bx-edit-alt"></i></button></a></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.tab-pane').first().addClass('active');
        })
    </script>
@endpush