<div id="addressModal" class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" aria-hidden="true">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="myModalLabel">{{ __('common.enter_a_new_shiping_address') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <form action="{{ route('cart.add-new-address') }}" method="post" class="needs-validation" novalidate
            onsubmit="addNewAddress(this)">
            <noscript>
                @csrf
            </noscript>
            <label>
                {{ __('common.name') }}<span class="text-danger">*</span>
            </label>
            <div class="form-group">
                <input type="text" name="name" class="form-control" placeholder="Name" required>
            </div>
            <label>
                {{ __('common.address') }}<span class="text-danger">*</span>
            </label>
            <div class="form-group">
                <input type="text" name="address" class="form-control" placeholder="Address" required>
            </div>
            <label>
                {{ __('common.zip') }}<span class="text-danger">*</span>
            </label>
            <div class="form-group">
                <input type="number" name="zip" class="form-control" placeholder="Pin" required>
            </div>
            <label>
                {{ __('common.city') }}<span class="text-danger">*</span>
            </label>
            <div class="form-group">
                <input type="text" name="city" class="form-control" placeholder="Town" required>
            </div>
            <label>
                {{ __('common.mobile') }}<span class="text-danger">*</span>
            </label>
            <div class="form-group">
                <input type="number" min="0" name="mobile" class="form-control" placeholder="Mobile Number" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>
            </div>

    </div>

    </form>
</div><!-- /.modal -->

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>
