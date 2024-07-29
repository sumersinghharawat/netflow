<div class="offcanvas offcanvas-end" id="addCategory" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('settings.bank_details') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('kyc.category.add') }}" method="post" class="needs-validation" novalidate onsubmit="addKycCategory(this)">
            <noscript>
                @csrf
            </noscript>
            <div class="form-group">
                <input type="text" name="category" class="form-control" value="{{ old('category') }}"
                    min="0" placeholder="category name" required>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">{{ __('common.close') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>
            </div>
        </form>
    </div>
</div>
