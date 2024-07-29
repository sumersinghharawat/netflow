<div class="offcanvas offcanvas-end" tabindex="-1" id="addTextInvite" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">{{ __('tools.add_textInvite') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <form action="{{ route('invites.store') }}" method="post" class="mt-3" onsubmit="addInvites(this)"
                enctype="multipart/form-data">
                <noscript>
                    @csrf
                </noscript>
                <div class="form-group">
                    <label>{{ __('tools.subject') }}</label>
                    <input type="text" name="subject" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('tools.message') }}</label>
                    <textarea class="summernote form-control" name="content" rows="6"></textarea>
                    @error('content')
                        <div class="invalid-feedback d-block"></div>
                    @enderror
                </div>
                <input type="hidden" name="type" value="text" class="form-control">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="addBannerInvite" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">{{ __('tools.add_bannerInvite') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <form action="{{ route('invites.store') }}" method="post" class="mt-3" onsubmit="addInvites(this)"
                enctype="multipart/form-data">
                <noscript>
                    @csrf
                </noscript>
                <div class="form-group">
                    <label>{{ __('tools.bannerName') }}</label>
                    <input type="text" name="subject" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('tools.targetURL') }}</label>
                    <input type="text" name="target_url" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('tools.content') }}</label>
                    <input type="file" name="content" class="form-control">
                </div>
                <input type="hidden" name="type" value="banner" class="form-control">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="addSocialInvite" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addBaner"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <form action="{{ route('invites.store') }}" method="post" class="mt-3" onsubmit="addInvites(this)"
                enctype="multipart/form-data">
                <noscript>
                    @csrf
                </noscript>
                <div class="form-group">
                    <label>{{ __('tools.subject') }}</label>
                    <input type="text" name="subject" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('tools.message') }}</label>
                    <textarea class="summernote form-control" name="content"></textarea>
                </div>
                <input type="hidden" name="type" id="socialType" class="form-control">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="editPromotionalTool"
    aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">{{ __('tools.editInvites') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <form action="{{ route('invites.update') }}" method="post" class="mt-3" onsubmit="editInvites(this)"
                enctype="multipart/form-data">
                <noscript>
                    @csrf
                </noscript>
                <div class="form-group">
                    <label>{{ __('tools.subject') }}</label>
                    <input type="text" name="subject" id="subject" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('tools.message') }}</label>
                    <textarea name="content" id="content" class="form-group summernote"></textarea>
                </div>
                <input type="hidden" name="type" id="inviteType" class="form-control">
                <input type="hidden" name="inviteId" id="inviteId" class="form-control">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>

    </div>
</div>
