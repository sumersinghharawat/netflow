<div class="offcanvas offcanvas-end" tabindex="-1" id="addEpin" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('epin.addnewEpin') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="addEpin">
        <form action="{{ route('epin.store') }}" method="post" onsubmit="addNewEpin(this)">
            @csrf
            <div class="form-group">
                <label for="">{{ __('common.username') }} <span class="text-danger">*</span></label>
                <select name="username" id="userName-add-epin" class="form-select select2-search-user"></select>
            </div>
            <div class="form-group">
                <label for="amount">{{ __('common.amount') }} <span class="text-danger">*</span></label>
                <select name="amount" id="amount" class="form-select @error('amount') is-invalid @enderror">
                    <option value="">{{ __('epin.selectAmount') }}</option>
                    @foreach ($amounts as $amount)
                        <option value="{{ $amount->id }}">{{ formatCurrency($amount->amount) }}</option>
                    @endforeach
                </select>
                @error('amount')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

            </div>

            <div class="form-group">
                <label for="count">{{ __('epin.epinCount') }} <span class="text-danger">*</span></label>
                <input type="number" name="count" id="count" min="0"
                    class="form-control @error('count') is-invalid @enderror">
                @error('count')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="date">{{ __('common.expiryDate') }} <span class="text-danger">*</span></label>
                <input type="date" name="expiry" id="date"
                    class="form-control @error('expiry') is-invalid @enderror">
                @error('expiry')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
            </div>
        </form>
    </div>
</div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="addPurchaseEpin" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('epin.epinPurchase') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('epinPurchase.store') }}" method="post" onsubmit="addEpinPurchase(this)">
            @csrf
            <div class="form-group">
                <label for="">{{ __('common.username') }} <span class="text-danger">*</span></label>
                <select name="username" id="userName-epinPurchase" class="form-select select2-search-user"></select>
            </div>
            <div class="form-group">
                <label for="amount">{{ __('common.amount') }} <span class="text-danger">*</span></label>
                <select name="purchase_amount" id="amount"
                    class="form-select @error('purchase_amount') is-invalid @enderror">
                    <option value="">{{ __('common.selectAmount') }}</option>
                    @foreach ($amounts as $amount)
                        <option value="{{ $amount->id }}">{{ formatCurrency($amount->amount) }}</option>
                    @endforeach
                </select>
                @error('purchase_amount')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

            </div>

            <div class="form-group">
                <label for="count">{{ __('epin.epinCount') }}<span class="text-danger">*</span></label>
                <input type="number" name="purchase_count" id="count" min="0"
                    class="form-control @error('purchase_count') is-invalid @enderror">
                @error('purchase_count')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="date">{{ __('common.expiryDate') }} <span class="text-danger">*</span></label>
                <input type="date" name="purchase_expiry" id="date"
                    class="form-control @error('purchase_expiry') is-invalid @enderror">
                @error('purchase_expiry')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('epin.epinPurchase') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="transferEpin" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('epin.epinTransfer') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('epin.transfer') }}" method="post" onsubmit="transferEpin(this)">
            @csrf
            <div class="form-group">
                <label for="username">{{ __('epin.fromUsername') }}<span class="text-danger">*</span></label>
                <select name="from_user" id="fromUSER" class="form-select select2-search-user"></select>
                <span class="text-danger" id="Error_display"></span>
                @error('from_user')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label for="count">{{ __('epin.toUsername') }}<span class="text-danger">*</span></label>
                <select name="to_user" id="transfer-to_user"
                    class="form-select select2-search-user @error('to_user') is-invalid @enderror"></select>
                @error('to_user')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group" id="Epin">
                <div class="form-group">
                    <label for="Epin">{{ __('epin.epin') }} <span class="text-danger">*</span></label>
                    <select class="form-select" name="epin">
                        <option value="">{{ __('epin.selectEpin') }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('epin.epinTransfer') }}</button>
            </div>
        </form>
    </div>
</div>
