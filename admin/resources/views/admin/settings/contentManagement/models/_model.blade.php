@forelse ($welcomeletter as $item)
    <form action="{{ route('welcome-letter.update') }}" method="post">
        @csrf
        <div class="modal" id="welcome-{{ $item->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('settings.update_welcome_letter') }}</h4>
                        <button type="button" class="btn btn-shadow-none" data-bs-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="language_id" value="{{ $item->language->id }}">
                        <textarea style="height:150px;" name="content" id="" cols="30" rows="10" class="summernote form-control">{{ $item->content }}</textarea>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    </form>
@empty

@endforelse

<!-- Terms&Cond Modal -->

@forelse ($termsandcond as $item)
    <form action="{{ route('termsconditions.update') }}" method="post">
        @csrf
        <div class="modal" id="tc-{{ $item->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('settings.update_terms_and_conditions') }}</h4>
                        <button type="button" class="close btn " data-bs-dismiss="modal">&times;</button>
                    </div>
                    <input type="hidden" class="form-control" name="language_id" value="{{ $item->language->id }}">

                    <!-- Modal body -->
                    <div class="modal-body">
                        <textarea style="height:150px;" name="content" id="" cols="30" rows="10" class="summernote form-control">{{ $item->terms_and_conditions }}</textarea>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    </form>
@empty

@endforelse
