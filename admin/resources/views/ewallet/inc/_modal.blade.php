

<div class="offcanvas offcanvas-end" id="ewallet-fund-transfer" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{__('ewallet.fund_transfer')}}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('ewallet.fund.transfer') }}" method="post" onsubmit="fundTransfer(this)">
            @csrf
            <div class="form-group">
                <label for="user">{{ __('common.username') }} <span class="text-danger">*</span></label>
                <select class="form-control select2-search-user-canvas" name="transfer_from" onchange="showBalance()" id="select2-canvas-transfer-from">
                </select>
                @error('transfer_from')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="amount">{{ __('ewallet.ewallet_balance') }}</label>
                <div class="input-group" id="timepicker-input-group1">
                    <span class="input-group-text"><i>{{$currency}}</i></span>
                    <input type="text" name="ewallet_balance" id="ewallet_balance" disabled class="form-control" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="user">{{ __('ewallet.transfer_to') }} <span class="text-danger">*</span></label>
                <select class="form-control select2-ajax" name="transfer_to" id="select2-canvas-transfer-to">
                </select>
                @error('transfer_to')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="count">{{ __('common.amount') }} <span class="text-danger">*</span></label>
                <div class="input-group" id="timepicker-input-group1">
                    <span class="input-group-text"><i>{{$currency}}</i></span>
                    <input type="number" class="form-control" name="amount" placeholder="Amount">
                </div>
                @error('count')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="date">{{ __('ewallet.notes') }}</label>
                <textarea name="notes" id="" cols="30" rows="5" class="form-control" placeholder="Transaction notes"></textarea>
                @error('expiry')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="amount">{{ __('ewallet.transaction_fee') }}</label>
                <div class="input-group" id="timepicker-input-group1">
                    <span class="input-group-text"><i>{{$currency}}</i></span>
                    <input type="text" name="balance" disabled class="form-control" value={{ formatCurrency($transferFee) }}>
                </div>
            </div>
            <div class="form-group">
                <label for="user">{{ __('ewallet.transaction_password') }} <span class="text-danger">*</span></label>
                <input type="password" name="transaction_password" id="transaction-password" class="form-control" placeholder="Transaction Password">
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{__('common.submit')}}</button>
            </div>
        </form>
    </div>
</div>


{{-- fund credit --}}
<div class="offcanvas offcanvas-end" id="ewallet-fund-credit" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('ewallet.fund_credit') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('ewallet.fund.credit') }}" method="post" onsubmit="fundCredit(this)">
            @csrf
            <div class="form-group">
                <label for="user">{{ __('common.username') }} <span class="text-danger">*</span></label>
                <select class="form-control select2-search-user-canvas" name="username" id="select2-canvas-credit">
                </select>
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="count">{{ __('common.amount') }} <span class="text-danger">*</span></label>
                <div class="input-group" id="timepicker-input-group1">
                    <span class="input-group-text"><i>{{$currency}}</i></span>
                    <input type="number" class="form-control" name="amount" placeholder="Amount">
                </div>
                @error('count')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="date">{{ __('ewallet.notes') }}</label>
                <textarea name="notes" id="" cols="30" rows="5" class="form-control" placeholder="Transaction notes"></textarea>
                @error('expiry')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{__('common.submit')}}</button>
            </div>
        </form>
    </div>
</div>


{{-- fund debit --}}
<div class="offcanvas offcanvas-end" id="ewallet-fund-debit" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('ewallet.fund_debit') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('ewallet.fund.debit') }}" method="post" onsubmit="fundDebit(this)">
            @csrf
            <div class="form-group">
                <label for="user">{{ __('common.username') }} <span class="text-danger">*</span></label>
                <select class="form-control select2-search-user-canvas" name="username" id="select2-canvas-debit">
                </select>
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="count">{{ __('common.amount') }} <span class="text-danger">*</span></label>
                <div class="input-group" id="timepicker-input-group1">
                    <span class="input-group-text"><i>{{$currency}}</i></span>
                    <input type="number" class="form-control" name="amount" placeholder="Amount">
                </div>
                @error('count')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="date">{{ __('ewallet.notes') }}</label>
                <textarea name="notes" id="" cols="30" rows="5" class="form-control" placeholder="Transaction notes"></textarea>
                @error('expiry')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{__('common.submit')}}</button>
            </div>
        </form>
    </div>
</div>
