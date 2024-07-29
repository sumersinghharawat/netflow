<div class="modal fade" id="addCategoryfvd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('settings.add_your_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('kyc.category.add') }}" method="post" class="needs-validation" novalidate onsubmit="addKycCategory(this)">
                    <noscript>
                        @csrf
                    </noscript>
                    <div class="form-group">
                        <input type="text" name="category" class="form-control" value="{{ old('category') }}"
                            min="0" placeholder="category name" required>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal">{{ __('common.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
