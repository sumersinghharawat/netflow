<label for="" class="form-label">Country*</label>
<select name="country" class="form-control" id="country">
    @forelse ($countries as $country)
        <option value="{{ $country->name }}"> {{ $country->name }}</option>
    @empty
        <option value="">{{ __('common.no_data') }}</option>
    @endforelse
</select>
