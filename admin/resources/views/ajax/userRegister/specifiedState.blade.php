<select name="state" id="state" class="form-control" {{ $data['status']->required == 'yes' ? 'required' : '' }}>
    <option value="">Select State</option>

    @foreach ($data['state'] as $state)
        <option value="{{ $state->id }}" @selected($state->id == $stateId)>{{ $state->name }}</option>
    @endforeach
</select>

