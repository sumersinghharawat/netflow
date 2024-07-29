<select name="state" id="state" class="form-select w-75" {{ $data['status']->required == 'yes' ? 'required' : '' }}>
    <option value="">Select State</option>
    @foreach ($data['state'] as $state)
        <option value="{{ $state->id }}">{{ $state->name }}</option>
    @endforeach
</select>
