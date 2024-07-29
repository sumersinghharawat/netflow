@extends('layouts.app')
@section('content')
    @include('layouts.alert')
    <div class="row">
        <h4>
            {{ __('ewallet.e_wallet') }}
        </h4>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="card">
                <div class="row no-gutters align-items-center">
                    <div class="col-md-4">
                        <img class="card-img bg-success rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/income-w.png') }}" alt="Card image">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('ewallet.credited') }}</h5>
                            <p class="card-text">{{ $total['credit'] }}</p>
                            <p class="card-text"><small class="text-muted">{{ __('ewallet.last_updated_mins_ago'),[3] }}</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="row no-gutters align-items-center">
                    <div class="col-md-4">
                        <img class="card-img bg-success rounded bg-danger rounded"
                            src="{{ asset('assets/images/ewallet/Bonus-w.png') }}" alt="Card image">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('ewallet.dedited') }}</h5>
                            <p class="card-text">{{ $total['debit'] }}</p>
                            <p class="card-text"><small class="text-muted">{{ __('ewallet.last_updated_mins_ago'),[3] }}</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="row no-gutters align-items-center">
                    <div class="col-md-4">
                        <img class="card-img bg-info rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/E-Wallet-w.png') }}" alt="Card image">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('ewallet.ewallet_balance') }}</h5>
                            <p class="card-text">{{ $total['credit'] - $total['debit'] }}</p>
                            <p class="card-text"><small class="text-muted">{{ __('ewallet.last_updated_mins_ago'),[3] }}</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="row no-gutters align-items-center">
                    <div class="col-md-4">
                        <img class="card-img bg-info rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/income-w.png') }}" alt="Card image">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('ewallet.purchase_wallet') }}</h5>
                            <p class="card-text">{{ round($purchaseWalletBalance) }}</p>
                            <p class="card-text"><small class="text-muted">{{ __('ewallet.last_updated_mins_ago'),[3] }}</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="row no-gutters align-items-center">
                    <div class="col-md-4">
                        <img class="card-img bg-info rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/income-w.png') }}" alt="Card image">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('ewallet.commission_earned') }}</h5>
                            <p class="card-text">{{ $total['credit'] - $total['debit'] }}</p>
                            <p class="card-text"><small class="text-muted">{{ __('ewallet.last_updated_mins_ago'),[3] }}</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#ewallet" role="tab" id="ewallet-summary">
                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                    <span class="d-none d-sm-block">
                        {{ __('ewallet.ewallet_summary') }}
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="{{ route('ewallet.transaction') }}"
                    onclick="getEWallettransaction(this)" role="tab">
                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                    <span class="d-none d-sm-block">{{ __('ewallet.ewallet_transactions') }}</span>
                </a>
            </li>
            <li class="nav-item">
                {{-- <form method="post" id="EWalletBalance" action="{{ route('ewallet.balance') }}"
                    enctype="multipart/form-data" onclick="getEWalletbalance(this)">
                    @csrf --}}
                <a class="nav-link" data-bs-toggle="tab" href="#ewallet_balance" role="tab">
                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                    <span class="d-none d-sm-block">{{ __('ewallet.ewallet_balance') }}</span>
                </a>
                {{-- </form> --}}
            </li>
            <li class="nav-item">
                {{-- <form method="post" id="purWallet" action="{{ route('purchase.wallet') }}" enctype="multipart/form-data"
                    onclick="getPurchaseWallet(this)">
                    @csrf --}}
                <a class="nav-link" data-bs-toggle="tab" href="#pWallet" role="tab">
                    <span class="d-block d-sm-none" onclick="getPurchaseWallet()"><i class="fas fa-cog"></i></span>
                    <span class="d-none d-sm-block">{{ __('ewallet.purchase_wallet') }}
                    </span>
                </a>
                {{-- </form> --}}
            </li>
            <li class="nav-item">
                {{-- <form method="post" id="ewalletStatements" action="{{ route('ewallet.statement') }}" enctype="multipart/form-data"
                    onclick="getewalletstatement(this)">
                    @csrf --}}
                <a class="nav-link" data-bs-toggle="tab" href="#ewallet_state" role="tab">
                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                    <span class="d-none d-sm-block">{{ __('ewallet.ewallet_statement') }}
                    </span>
                </a>
                {{-- </form> --}}
            </li>
            <li class="nav-item">
                {{-- <form method="post" id="userEarningsForm" action="{{ route('user.earnings') }}"
                    enctype="multipart/form-data" onclick="getuserearnings(this)">
                    @csrf --}}
                <a class="nav-link" data-bs-toggle="tab" href="#userEarningstab" role="tab" id="userEarning">
                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                    <span class="d-none d-sm-block"> {{ __('ewallet.user_earnings') }}
                    </span>
                </a>
                {{-- </form> --}}
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content p-3 text-muted">
            <div class="tab-pane" id="ewallet" role="tabpanel">
                <form method="post" id="EWalletDateData" action="{{ route('ewallet.dateReport') }}"
                    enctype="multipart/form-data" onsubmit="getEWalletDateData(this)">
                    @csrf
                    <div class="row">

                        <div class="col-md-3">
                            <div class="row">
                                {{-- <div class="col-md-6">
                                    <input class="form-control" type="date" name="fromDate"
                                        value="{{ old('fromDate') }}">
                                </div>
                                <div class="col-md-6">
                                    <input class="form-control" type="date" name="toDate" value="{{ old('toDate') }}">
                                </div> --}}
                                <div class="mb-4">
                                    <div class="input-daterange input-group" id="datepicker6"
                                        data-date-format="dd M, yyyy" data-date-autoclose="true"
                                        data-provide="datepicker" data-date-container='#datepicker6'>
                                        <input type="text" class="form-control" name="fromDate"
                                            placeholder="Start Date" value="{{ old('fromDate') }}" />
                                        <input type="text" class="form-control" name="toDate" placeholder="End Date"
                                            value="{{ old('toDate') }}" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-2">

                            <button type="submit" class="btn btn-primary">{{ __('common.view') }}</button>
                            <a href="{{ route('reports.ewallet') }}" type="submit" class="btn btn-primary">{{ __('common.reset') }}</a>
                        </div>
                    </div>
                </form>

                <div id="dateReport">

                    <div class="debit-credit-all">
                        <div class="debit-credit">
                            <div class="list-group">
                                <div class="list-group-item list-group-item-header color-text credit">
                                    <h3> {{ __('ewallet.credit') }} </h3>
                                </div>
                                <div id="credited_items" class="summary-tile-grid">
                                    <div class="row">
                                        @foreach ($ewalletCategories as $category)
                                            @if (array_key_exists($category, $details))
                                                @if ($details[$category]['type'] == 'credit')
                                                    @if ($category != null)
                                                        <div class="col-md-4">
                                                            <div class="list-group-item">
                                                                <div>
                                                                    {{ $category }}

                                                                </div>

                                                                <span style="background-color: lightgreen;">
                                                                    ₹ {{ $details[$category]['amount'] }}
                                                                </span>

                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="debit-credit">
                            <div class="list-group">
                                <div class="list-group-item list-group-item-header color-text debit">
                                    <h3> {{ __('ewallet.debit') }}</h3>
                                </div>
                                <div id="debited_items" class="summary-tile-grid">

                                    <div class="row">
                                        @foreach ($ewalletCategories as $category)
                                            @if (array_key_exists($category, $details))
                                                @if ($details[$category]['type'] == 'debit')
                                                    @if ($category != null)
                                                        <div class="col-md-4">
                                                            <div class="list-group-item">
                                                                <div>
                                                                    {{ $category }}

                                                                </div>

                                                                <span style="background-color: lightgray;">
                                                                    ₹ {{ $details[$category]['amount'] }}
                                                                </span>

                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" role="tabpanel" id="ewallet_transaction">
                {{-- <div id="eWalletTransactionReport">

                </div> --}}
                <form method="post" id="TransactionDateData" action="{{ route('ewalletTransaction.dateReport') }}"
                    enctype="multipart/form-data" onsubmit="getewalletTransactionDateData(this)">

                    <div class="row ">
                        <div class="col-md-2">

                            <select class="form-control select2-ajax" id="eWalletuserData" style="width:100%;"
                                name="username"></select>
                            {{-- <input type="text" name="username" class="form-control" value="{{ old('username') }}"> --}}



                        </div>
                        <div class="col-md-2">
                            <div class="mb-3 row">
                                <div class="col-md-10">
                                    <select class="form-select" name="type">
                                        <option>{{ __('common.type') }}</option>
                                        <option value="credit">{{ __('ewallet.credited') }}</option>
                                        <option value="debit">{{ __('ewallet.debited') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3 row">
                                <div class="col-md-10" style="margin-left: -25px;">
                                    <select class="form-select" name="category">
                                        <option>Category</option>
                                        @foreach ($ewalletCategories as $category)
                                            @if ($category != null)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <div class="input-daterange input-group" id="datepicker7" data-date-format="dd M, yyyy"
                                    data-date-autoclose="true" data-provide="datepicker"
                                    data-date-container='#datepicker7'>
                                    <input type="text" class="form-control" name="fromDate"
                                        placeholder="Start Date" />
                                    <input type="text" class="form-control" name="toDate" placeholder="End Date" />
                                </div>
                            </div>

                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">{{ __('common.view') }}</button>
                            <a href="{{ route('reports.ewallet') }}" type="submit" class="btn btn-primary">{{ __('common.reset') }}</a>
                        </div>
                    </div>
                </form>
                <div id="TransDateReport">

                </div>
            </div>
            <div class="tab-pane" id="ewallet_balance" role="tabpanel">
                <div id="ewalletBalance">
                </div>
            </div>
            <div class="tab-pane" id="pWallet" role="tabpanel">
                {{-- <div id="purchaseWallet">
                </div> --}}
                <div class="container">
                    <div class="row">
                        <div class="card text-white-50">
                            <br>
                            <h5 class="mb-4 text-black"><i class="mdi mdi-alert-circle-outline me-3"></i>This is an addon
                                module</h5>
                        </div>

                        <a class="nav-link" data-bs-toggle="tab" href="#ewallet_transaction" role="tab"
                            {{-- onclick="getEWallettransaction()" --}}>
                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                            <span class="d-none d-sm-block">{{ __('ewallet.ewallet_transactions') }}</span>

                        </a>

                        </li>
                        <li class="nav-item">

                            <a class="nav-link" data-bs-toggle="tab" href="#ewallet_balance" role="tab"
                                {{-- onclick="getEWalletbalance()" --}}>
                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                <span class="d-none d-sm-block">{{ __('ewallet.ewallet_balance') }}</span>
                            </a>

                        </li>
                        <li class="nav-item">

                            <a class="nav-link" data-bs-toggle="tab" href="#pWallet" role="tab"
                                {{-- onclick="getPurchaseWallet()" --}}>
                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                <span class="d-none d-sm-block">{{ __('ewallet.purchase_wallet') }}
                                </span>
                            </a>

                        </li>
                        <li class="nav-item">


                            <form method="post" id="ewalletStatements" action="{{ route('ewallet.statement') }}"
                                enctype="multipart/form-data" onclick="getewalletstatement(this)">
                                @csrf
                            </form>
                            <a class="nav-link" data-bs-toggle="tab" href="#ewallet_state" role="tab">
                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                <span class="d-none d-sm-block">{{ __('ewallet.ewallet_statement') }}
                                </span>
                            </a>
                            </form>

                        </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active " id="ewallet" role="tabpanel">
                                <form method="post" id="EWalletDateData" action="{{ route('ewallet.dateReport') }}"
                                    enctype="multipart/form-data" onsubmit="getEWalletDateData(this)">
                                    @csrf

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input class="form-control" type="date" name="fromDate"
                                                        value="{{ old('fromDate') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input class="form-control" type="date" name="toDate"
                                                        value="{{ old('toDate') }}">
                                                </div>
                                                {{-- <div class="mb-4">
                                                <div class="input-daterange input-group" id="datepicker6" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                                                    <input type="text" class="form-control" name="fromDate" placeholder="Start Date" value= "{{ old('fromDate') }}"/>
                                                    <input type="text" class="form-control" name="toDate" placeholder="End Date" value= "{{ old('toDate') }}" />
                                                </div>
                                            </div> --}}

                                                <button type="submit" class="btn btn-primary">{{ __('common.view') }}</button>


                                                <a href="{{ route('reports.ewallet') }}" type="submit"
                                                    class="btn btn-primary">{{ __('common.reset') }}</a>
                                            </div>


                                        </div>
                                    </div>


                            </div>
                            </form>
                        </div>
                        <div id="purchaseWalletTable">

                        </div>


                    </div>
                    <div class="tab-pane" id="ewallet_state" role="tabpanel">
                        {{-- <div id="ewalletStatement">
                </div> --}}
                        <div class="row">
                            <form method="post" id="purchaseData" action="{{ route('ewalletStatement.report') }}"
                                enctype="multipart/form-data" onsubmit="getewalletstatementreport(this)">
                                @csrf

                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <select class="form-select" style="width: 300px"
                                                    id="eWalletstatReportData" name="username"></select>
                                                {{-- <input type="text" name="username" class="form-control" value="{{ old('username') }}"> --}}

                                            </div>
                                            <div class="col-md-4">

                                                <button type="submit" class="btn btn-primary">{{ __('common.view') }}</button>


                                                <a href="{{ route('reports.ewallet') }}" type="submit"
                                                    class="btn btn-primary">{{ __('common.reset') }}</a>
                                            </div>


                                        </div>
                                    </div>


                                </div>
                            </form>
                        </div>
                        <div id="ewalletStatement">

                        </div>









                    </div>
                    <div class="tab-pane" id="userEarningstab" role="tabpanel">

                        <form method="post" id="userEarnings" action="{{ route('userEarnings.report') }}"
                            enctype="multipart/form-data" onsubmit="getuserEarningsReport(this)">
                            @csrf

                            <div class="row">

                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select class="form-control form-select" style="width: 200px"
                                                id="userearningsuserData" name="username"></select>
                                            {{-- <input type="text" name="username" class="form-control" value="{{ old('username') }}"> --}}

                                        </div>
                                        <div class="col-md-4">


                                            <div class="col-md-10">
                                                <select class="form-select" name="category">
                                                    <option>{{ __('common.category') }}</option>
                                                    @foreach ($earningsCategories[0] as $category)
                                                        @if ($category != null)
                                                            <option value="{{ $category }}"
                                                                @if ($category == old('category')) selected @endif>
                                                                {{ $category }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-4">
                                                <div class="input-daterange input-group" id="datepicker8"
                                                    data-date-format="dd M, yyyy" data-date-autoclose="true"
                                                    data-provide="datepicker" data-date-container='#datepicker8'>
                                                    <input type="text" class="form-control" name="fromDate"
                                                        placeholder="Start Date" value="{{ old('fromDate') }}" />
                                                    <input type="text" class="form-control" name="toDate"
                                                        placeholder="End Date" value="{{ old('toDate') }}" />
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-2">

                                            <button type="submit" class="btn btn-primary">{{ __('common.view') }}</button>


                                            <a href="{{ route('reports.ewallet') }}" type="submit"
                                                class="btn btn-primary">{{ __('common.reset') }}</a>
                                        </div>


                                    </div>
                                </div>
                            </div>



                        </form>
                        <div id="userEarningsData">
                            @if ($tab == 'userEarnings')
                                @dd($data)

                                <table id="datatable-buttons" class="table table-bordered dt-responsive w-100">

                                    <thead>
                                        <tr>
                                            <th>
                                                {{ __('reports.category') }}
                                            </th>
                                            <th>
                                                {{ __('reports.total_amount') }}
                                            </th>
                                            <th>
                                                {{ __('reports.tax') }}
                                            </th>
                                            <th>
                                                {{ __('reports.service_charge') }}
                                            </th>
                                            <th>
                                                {{ __('reports.amount_payable') }}
                                            </th>


                                            <th>
                                                {{ __('reports.transaction_date') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @dd($data[0]['category']); --}}


                                        @forelse($data as $item)
                                            <tr>


                                                <td>
                                                    {{-- {{ $item['category'] }} --}}
                                                    @dump($item['category'])
                                                </td>
                                                <td>
                                                    ₹ {{ $item['amount'] }}
                                                </td>
                                                <td>
                                                    ₹ {{ $item['tax'] }}
                                                </td>
                                                <td>

                                                    ₹ {{ $item['service_charge'] }}
                                                </td>
                                                <td>
                                                    ₹ {{ $item['amount_payable'] }}
                                                </td>
                                                <td>
                                                    {{ Carbon\Carbon::parse($item['transaction_date'])->toDateString() }}
                                                </td>
                                            </tr>
                                        @empty
                                        <tr>
                                            <td colspan="100%">
                                                <div class="nodata_view"  >
                                                    <img src="{{asset('assets/images/nodata-icon.png')}}" alt="">
                                                    <span class="text-secondary">{{ __('common.no_data') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            @endif


                        </div>

                    </div>


                </div>
            </div>
        @endsection
        @push('scripts')
            <script>
                $(() => {
                    let userEarningTab = "{{ $tab }}"

                    if (userEarningTab == "userEarnings") {

                        $('#userEarning').addClass("active");
                    }
                });



                $(() => {
                    let activeId = "{{ $active }}"
                    $(`#ewallet-${activeId}`).addClass('active')

                })


                const getEWalletbalance = async (form) => {
                event.preventDefault()

                paging: true,
                autoWidth: true,

                });

                });

                $(document).ready(function() {
                    $("#datatable").DataTable(),
                        $("#datatable-buttons").DataTable({
                            lengthChange: !1,
                            buttons: ["copy", "excel", "pdf", "colvis"]
                        }).buttons().container().appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)"),
                        $(".dataTables_length select").addClass("form-select form-select-sm")

                    $("#datatable1").DataTable(),
                        $("#datatable1-buttons").DataTable({
                            lengthChange: !1,
                            buttons: ["copy", "excel", "pdf", "colvis"]
                        }).buttons().container().appendTo("#datatable1-buttons_wrapper .col-md-6:eq(0)"),
                        $(".dataTables_length select").addClass("form-select form-select-sm")

                    $("#datatable2").DataTable(),
                        $("#datatable2-buttons").DataTable({
                            lengthChange: !1,
                            buttons: ["copy", "excel", "pdf", "colvis"]
                        }).buttons().container().appendTo("#datatable2-buttons_wrapper .col-md-6:eq(0)"),
                        $(".dataTables_length select").addClass("form-select form-select-sm")

                    $("#datatable3").DataTable(),
                        $("#datatable3-buttons").DataTable({
                            lengthChange: !1,
                            buttons: ["copy", "excel", "pdf", "colvis"]
                        }).buttons().container().appendTo("#datatable3-buttons_wrapper .col-md-6:eq(0)"),
                        $(".dataTables_length select").addClass("form-select form-select-sm")

                });




                const getEWalletbalance = async () => {
                    let url = "{{ route('ewallet.balance') }}";

                    const res = $.get(`${url}`)
                        .catch((err) => {


                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {


                            $('#ewalletBalance').html(' ')
                            $('#ewalletBalance').html(result.data)
                        })



                }
                const getPurchaseWalletold = async (form) => {
                    event.preventDefault()

                    const res = $.get(`${url}`)

                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {

                            $('#purchaseWallet').html(' ')
                            $('#purchaseWallet').html(result.data)



                        })
                }
                const getPurchaseWallet = async (form) => {
                    let url = "{{ route('purchase.wallet') }}";

                    const res = $.get(`${url}`)

                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {

                            $('#purchaseWallet').html(' ')
                            $('#purchaseWallet').html(result.data)



                        })
                }
                const getewalletstatement = async (form) => {
                    let url = "{{ route('ewalletStatement.report') }}";

                    const res = $.get(`${url}`)

                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {

                            $('#ewalletStatement').html(' ')
                            $('#ewalletStatement').html(result.data)



                        })
                }

                const getewalletstatementold = async (form) => {
                    event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements) {
                        form.elements[key].classList.remove('is-invalid', 'd-block')
                    }
                    $('.invalid-feedback').remove()

                    let url = form.action
                    let data = getForm(form)

                    const res = await $.post(`${url}`, data)
                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {

                            $('#ewalletStatement').html(' ')
                            $('#ewalletStatement').html(result.data)



                        })
                }
                const getEWallettransaction = async (form) => {

                    event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements) {
                        form.elements[key].classList.remove('is-invalid', 'd-block')
                    }
                    $('.invalid-feedback').remove()

                    let url = form.action
                    let data = getForm(form)

                    const res = await $.get(`${url}`, data)
                        .catch(err => {
                            if (err.status === 422) formvalidationError(form, err)
                        })
                    $('#TransDateReport').html(' ')
                    $('#TransDateReport').html(result.data)
                }


                const getEWalletDateData = async (form) => {
                    event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements) {
                        form.elements[key].classList.remove('is-invalid', 'd-block')
                    }
                    $('.invalid-feedback').remove()

                    let url = form.action
                    let data = getForm(form)

                    const res = await $.post(`${url}`, data)
                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {
                            $('#dateReport').html(' ')
                            $('#dateReport').html(result.data)

                        })
                }





                $('#ewalletBalanceuserData').select2({
                    placeholder: 'Username',
                    ajax: {
                        url: "{{ route('load.users') }}",
                        dataType: 'json',
                        delay: 250,

                        processResults: function(result) {

                            return {
                                results: $.map(result.data, function(item) {
                                    return {
                                        text: item.username,
                                        id: item.id,

                                    }




                                })
                            };

                        },
                        cache: true
                    }

                });

                // const getEWalletbalance = async () =>
                //  {
                //      let url = "{{ route('ewallet.balance') }}";

                //        const res = $.get(`${url}`)
                //         .catch((err) =>
                //           {


                //             if (err.status === 422) {
                //                 formvalidationError(form, err)
                //             }
                //         }).then((result) =>
                //          {
                // const getEWalletbalance = async () => {
                //     let url = "{{ route('ewallet.balance') }}";

                //     const res = $.get(`${url}`)
                //         .catch((err) => {


                //             if (err.status === 422) {
                //                 formvalidationError(form, err)
                //             }
                //         }).then((result) => {


                //             $('#ewalletBalance').html(' ')
                //             $('#ewalletBalance').html(result.data)


                //         })



                // }


                const getewalletBalanceData = async (form) => {
                    event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements) {
                        form.elements[key].classList.remove('is-invalid', 'd-block')
                    }
                    $('.invalid-feedback').remove()

                    let url = form.action
                    let data = getForm(form)

                    const res = await $.post(`${url}`, data)
                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {

                            $('#ewalletBalance').html(' ')
                            $('#ewalletBalance').html(result.data)

                        })
                }

                const getuserearnings = async (form) => {
                    event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements) {
                        form.elements[key].classList.remove('is-invalid', 'd-block')
                    }
                    $('.invalid-feedback').remove()

                    let url = form.action
                    let data = getForm(form)

                    const res = await $.post(`${url}`, data)
                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {

                            $('#userEarningsData').html(' ')
                            $('#userEarningsData').html(result.data)



                        })
                }
                $('#eWalletuserData').select2({
                    placeholder: 'Username',
                    ajax: {
                        url: "{{ route('load.users') }}",
                        dataType: 'json',
                        delay: 250,

                        processResults: function(result) {

                            return {
                                results: $.map(result.data, function(item) {
                                    return {
                                        text: item.username,
                                        id: item.id,

                                    }

                                })
                            };

                        },
                        cache: true
                    }

                });


                $('#purchaseWalletuserData').select2({
                    placeholder: 'Username',
                    ajax: {
                        url: "{{ route('load.allusers') }}",
                        dataType: 'json',
                        delay: 250,

                        processResults: function(result) {

                            return {
                                results: $.map(result.data, function(item) {
                                    return {
                                        text: item.username,
                                        id: item.id,

                                    }

                                })
                            };

                        },
                        cache: true
                    }

                });

                const getpurchaseWalletData = async (form) => {
                    event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements) {
                        form.elements[key].classList.remove('is-invalid', 'd-block')
                    }
                    $('.invalid-feedback').remove()

                    let url = form.action
                    let data = getForm(form)

                    const res = await $.post(`${url}`, data)
                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {
                            console.table(result);
                            $('#purchaseWalletTable').html(' ')
                            $('#purchaseWalletTable').html(result.data)

                        })

                }
                const getewalletTransactionDateData = async (form) => {
                    event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements) {
                        form.elements[key].classList.remove('is-invalid', 'd-block')
                    }
                    $('.invalid-feedback').remove()

                    let url = form.action
                    let data = getForm(form)

                    const res = await $.post(`${url}`, data)
                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {

                            $('#TransDateReport').html(' ')
                            $('#TransDateReport').html(result.data)

                        })
                }
                $('#ewalletTranuserReportData').select2({
                    placeholder: 'Username',
                    ajax: {
                        url: "{{ route('load.users') }}",
                        dataType: 'json',
                        delay: 250,

                        processResults: function(result) {

                            return {
                                results: $.map(result.data, function(item) {
                                    return {
                                        text: item.username,
                                        id: item.id,

                                    }




                                })
                            };

                        },
                        cache: true
                    }

                });

                const getuserEarningsReport = async (form) => {
                    event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements) {
                        form.elements[key].classList.remove('is-invalid', 'd-block')
                    }
                    $('.invalid-feedback').remove()

                    let url = form.action
                    let data = getForm(form)

                    const res = await $.post(`${url}`, data)
                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {

                            // $('#userEarnings').html(' ')
                            // $('#userEarnings').html(result.data)
                            $('#userEarningsData').html('')
                            $('#userEarningsData').html(result.data)

                        })
                }
                $('#userearningsuserData').select2({
                    placeholder: 'Username',
                    ajax: {
                        url: "{{ route('load.users') }}",
                        dataType: 'json',
                        delay: 250,

                        processResults: function(result) {

                            return {
                                results: $.map(result.data, function(item) {
                                    return {
                                        text: item.username,
                                        id: item.id,

                                    }




                                })
                            };

                        },
                        cache: true
                    }

                });


                const getewalletstatementreport = async (form) => {
                    event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements) {
                        form.elements[key].classList.remove('is-invalid', 'd-block')
                    }
                    $('.invalid-feedback').remove()

                    let url = form.action
                    let data = getForm(form)

                    const res = await $.post(`${url}`, data)
                        .catch((err) => {

                            if (err.status === 422) {
                                formvalidationError(form, err)
                            }
                        }).then((result) => {

                            $('#ewalletStatement').html(' ')
                            $('#ewalletStatement').html(result.data)

                        })
                }
                $('#eWalletstatReportData').select2({

                    placeholder: 'Username',
                    ajax: {
                        url: "{{ route('load.users') }}",
                        dataType: 'json',
                        delay: 250,

                        processResults: function(result) {

                            return {
                                results: $.map(result.data, function(item) {
                                    return {
                                        text: item.username,
                                        id: item.id,

                                    }
                                })
                            };

                        },
                        cache: true
                    }

                });
            </script>
        @endpush
