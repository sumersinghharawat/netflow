<div class="form-group">
    <label for="Epin">{{ __('epin.epin') }} <span class="text-danger">*</span></label>
    @isset($epinList)
        <select name="epin" id="Epin" class="form-select">
            <option value="">{{ __('epin.selectEpin') }}</option>
            @forelse ($epinList as $epin)
                <option value="{{ $epin->id }}">{{ $epin->numbers }}</option>
            @empty
                <option value="">{{ __('epin.noActiveEpins') }}</option>
            @endforelse
        </select>
    @else
        <select name="epin" id="" class="form-selct is-invalid">
            <option value="">{{ __('epin.selectEpin') }}</option>
        </select>
        <span class="text-danger">{{ __('epin.noEpinFound') }}</span>
    @endisset

</div>
