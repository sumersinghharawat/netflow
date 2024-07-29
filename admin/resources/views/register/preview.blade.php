@extends('layouts.app')
@section('title', trans('register.letter_preview'))
@section('content')
    <div class="page-title-box d-sm-flex">
        <h4 class="mb-sm-0 font-size-18">{{ __('register.letter_preview') }}</h4>
    </div>
    <div class="row d-print-none">
        <div class="col-md-3">
            <a href="{{ route('network.genealogy') }}" class="btn btn-primary">{{ __('register.goto_tree_view') }}</a>
            <a href="#" class="btn btn-primary" onclick="printDiv('print-div')">{{ __('common.print') }}</a>

        </div>
    </div>
    <main class="my-5">
        <div class="container">
            @if (session()->has('success'))
                <div class="alert alert-success dismissable">{{ session()->get('success') }}</div>
            @endif
            <div class="card" id="print-div">
                <div class="card-head row my-3">
                    <div class="col-md-2">
                        @if ($companyDetails->logo && isFileExists($companyDetails->logo))
                            <img src="{{ $companyDetails->logo }}" alt="no image" class="img-fluid">
                        @else
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="no image" width="250px">
                        @endif
                    </div>
                    <div class="col-md-3 offset-md-7">
                        <div class="report_address_box">
                            <h4 class="card-title ">{{ $companyDetails->name }}</h4>
                            <p class="text-muted fw-bolder">{{ $companyDetails->address }}</p>
                            <p class="text-muted fw-bolder">{{ __('common.phone') }} : {{ $companyDetails->phone }}
                            </p>
                            <p class="text-muted fw-bolder">{{ __('common.email') }} : {{ $companyDetails->email }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row card-body">
                    <table class="table">
                        <tr>
                            <td>{{ __('common.username') }}</td>
                            <td>
                                @if (isset($pendingDetails))
                                    {{ Str::upper($pendingDetails->username) }}
                                @else
                                    {{ Str::upper($registeredDetails->username) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('common.full_name') }}</td>
                            <td>
                                @if (isset($userDetails))
                                    {{ $userDetails['first_name'] ?? $userDetails['username'] }}
                                @else
                                    {{ $user->userDetail->name }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('common.sponsor') }}</td>
                            <td>
                                @if (isset($userDetails))
                                    {{ $userDetails['sponsorName'] }}
                                @else
                                    {{ $user->sponsor->username }}
                                @endif
                            </td>
                        </tr>
                        @if ($moduleStatus->product_status)
                            <tr>
                                <td>{{ __('register.registration_amount') }}</td>
                                <td>
                                    @if (isset($userDetails))
                                        {{ $currency . ' ' . formatCurrency($userDetails['reg_amount']) }}
                                    @else
                                        {{ $currency . ' ' . formatCurrency($registeredDetails->reg_amount) }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{ __('common.package') }}</td>
                                <td>
                                    @if (isset($pendingDetails))
                                        {{ Str::upper($pendingDetails->RegistraionPackage->name ?? 'NA') }}
                                    @else
                                        {{ Str::upper($user->package->name ?? 'NA') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{ __('common.package_amount') }}</td>
                                <td>
                                    @if (isset($pendingDetails))
                                        {{ $currency . ' ' . formatCurrency($pendingDetails->RegistraionPackage->price ?? 0) }}
                                    @else
                                        {{ $currency . ' ' . formatCurrency($user->package->price ?? 0) }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>{{ __('common.total_amount') }}</td>
                            <td>
                                @if (isset($userDetails))
                                    {{ $currency . ' ' . formatCurrency($userDetails['totalAmount'] ?? 0) }}
                                @else
                                    {{ $currency . ' ' . formatCurrency($registeredDetails->total_amount ?? 0) }}
                                @endif
                            </td>
                        </tr>
                    </table>
                    <br>
                    <div class="card">
                        <div class="p-3">
                            {!! str_replace(':company' , $companyDetails->name , $welcomeletter->content) !!}
                        </div>
                        <div class="ms-3 mb-2">
                            <label for="">{{ __('register.winning_regards') }},</label><br>
                            <label for="">Admin</label><br>
                            <label for="">{{ $companyDetails->name }}</label><br>
                            <label for="">{{ __('common.date') }}: {{ now()->format('d-m-Y') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
