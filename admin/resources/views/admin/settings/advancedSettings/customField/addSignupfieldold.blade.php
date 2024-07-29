<table class="table table-hover" id="custFields">
    <thead>
        <tr>
            <th>#</th>
            <th class="w-100">{{ __('common.name') }}</th>
            <th class="w-100">{{ __('common.type') }}</th>
            <th class="w-100">{{ __('common.sort_order') }}</th>
            <th>{{ __('common.enabled') }}</th>
            <th>{{ __('common.mandatory') }}</th>
            <th>{{ __('common.action') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($signupField as $field)
            <tr id="signupField_{{ $field->id }}">
                <td>{{ $loop->index + $signupField->firstItem() }}</td>
                <td>
                    @if ($field->editable == 1)
                        <input type="text" name="field_{{ $field->id }}_name" class="form-control col-sm"
                            value="{{ $field->name }}" style="width: 50%;">
                    @else
                        {{ $field->name }}
                    @endif
                </td>
                <td>

                    @if ($field->editable == 1)
                        <input type="text" name="field_{{ $field->id }}_type" class="form-control col-sm"
                            value="{{ $field->type }}" style="width: 100px;">
                    @else
                        {{ $field->type }}
                    @endif
                </td>
                <td>
                    <input type="number" name="sortorder_{{ $field->id }}" id=""
                        class="form-control col-md" value="{{ $field->sort_order }}">
                </td>
                <td> <input class="form-check-input" type="checkbox" value="1" id="flexCheckChecked"
                        {{ $field->status == 1 ? 'checked' : '' }} name="is_enabled_{{ $field->id }}_">
                </td>
                <td>
                    <input class="form-check-input" type="checkbox" value="1" id="flexCheckChecked"
                        {{ $field->required == 1 ? 'checked' : '' }} name="is_required_{{ $field->id }}_">
                </td>
                <td>
                    @if ($field->editable == 1)
                        <button type="button" class="btn btn-danger"
                            onclick="deleteField({{ $field->id }})">{{ __('common.delete') }}</button>
                    @endif
                    <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>

                </td>
            </tr>
            @if ($field->editable == 1)
                <form action="{{ route('signupField.destroy', $field->id) }}" method="post"
                    id="deleteForm{{ $field->id }}" onsubmit="deleteField($field->id)">
                    @csrf
                </form>
            @endif
        @endforeach


    </tbody>

</table>
