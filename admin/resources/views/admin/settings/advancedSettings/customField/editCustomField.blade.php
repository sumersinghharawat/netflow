<div class="offcanvas offcanvas-end" tabindex="-1" id="editCustomField" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">{{ __('settings.custom_sign_up_form_fields') }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="row">
            <div class="card">
                <form action="{{ route('customField.update', $signupField->id) }}" method="post"
                    onsubmit="updateCustomField(this)">
                    <noscript>
                        @csrf
                    </noscript>
                    @forelse ($languages as $item)
                        <div class="form-group">
                            <label>{{ __('settings.field_name') }}: {{ $item->name }}
                                {{ $item->default ? '(Default)' : '' }}</label>
                            <input type="text" name="name[{{ $item->id }}]" id="" class="form-control"
                                value="{{ $signupField->customFieldLang->where('language_id', $item->id)->first()->value ?? 'NA' }}">
                        </div>
                    @empty
                    @endforelse
                    <div class="form-group">
                        <label>{{ __('settings.type') }}</label>
                        <select name="type" id="" class="form-select">
                            <option value="text" @selected($signupField->type == 'text')>{{ __('settings.text') }}</option>
                            <option value="email" @selected($signupField->type == 'email')>{{ __('settings.email') }}</option>
                            <option value="number" @selected($signupField->type == 'number')>{{ __('settings.number') }}</option>
                            <option value="textarea" @selected($signupField->type == 'textarea')>{{ __('settings.textarea') }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('settings.enabled') }}</label>
                        <select name="status" id="" class="form-select">
                            <option value="1" @selected($signupField->status)>Yes</option>
                            <option value="0" @selected(!$signupField->status)>no</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('settings.mandatory') }}</label>
                        <select name="required" id="" class="form-select">
                            <option value="1" @selected($signupField->required)>{{ __('settings.yes') }}</option>
                            <option value="0 " @selected(!$signupField->required)>{{ __('settings.no') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{ __('settings.sort_order') }}</label>
                        <input type="number" name="sort_order" class="form-control"
                            value="{{ $signupField->sort_order }}" min="0">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">{{ __('settings.save_changes') }}</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer"></div>
</div>
