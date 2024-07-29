<table class="table table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('settings.name') }}</th>
            <th>{{ __('settings.type') }}</th>
            <th style="min-width:100px">{{ __('settings.sort_order') }}</th>
            <th>{{ __('settings.enabled') }}</th>
            <th>{{ __('settings.mandatory') }}</th>
            <th>{{ __('settings.action') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customField as $field)
        <tr id="signupField_{{ $field->id }}">
            <td>{{ $loop->index + 1 }}</td>
            <td>
                @if ($field->editable)
                    <input type="text"
                        name="field[{{ $field->id }}][field_name]"
                        class="form-control col-sm" value="{{ $field->name }}"
                        style="width: 50%;">
                @else
                    {{ __('settings.' . $field->name) }}
                @endif
            </td>
            <td>
                @if ($field->editable)
                    <input type="text"
                        name="field[{{ $field->id }}][field_type]"
                        class="form-control col-sm" value="{{ $field->type }}"
                        style="width: 100px;">
                @else
                    {{ $field->type }}
                @endif
            </td>
            <td>
                <input type="number" name="field[{{ $field->id }}][sortorder]"
                    id="" class="form-control col-md"
                    value="{{ $field->sort_order }}" style="width: 50px;">
            </td>
            <td>
                <input class="form-check-input" type="checkbox" value="1"
                    id="is_enabled_{{ $field->id }}"
                    {{ $field->status == 1 ? 'checked' : '' }}
                    name="field[{{ $field->id }}][is_enabled]"
                    onclick="disableCheckBox({{ $field->id }})"
                    @disabled($field->name == 'first_name' ||
                            $field->name == 'date_of_birth' ||
                            $field->name == 'email' ||
                            $field->name == 'mobile')>
            </td>
            <td>
                <input class="form-check-input" type="checkbox" value="1"
                    id="is_required_{{ $field->id }}"
                    {{ $field->required == 1 ? 'checked' : '' }}
                    name="field[{{ $field->id }}][is_required]"
                    @disabled($field->name == 'first_name' ||
                            $field->name == 'date_of_birth' ||
                            $field->name == 'email' ||
                            $field->name == 'mobile')>
            </td>
            {{-- <td>
                @if ($field->editable == 1)
                    <button type="button" class="btn btn-danger"
                        onclick="deleteField({{ $field->id }})">DEL</button>
                @endif
            </td> --}}
            <td>
                @if ($field->editable == 1)
                    <button type="button" class="btn btn-danger"
                        onclick="deleteField({{ $field->id }})">DEL</button>
                    <button type="button" class="btn btn-primary"
                        onclick="editField({{ $field->id }})">{{ __('common.edit') }}</button>
                @endif
            </td>
        </tr>
            @if ($field->editable == 1)
                <form action="{{ route('signupField.destroy', $field->id) }}" method="post"
                    id="deleteForm{{ $field->id }}" onsubmit="deleteField1({{ $field->id }})">
            @endif
        @endforeach

    </tbody>
    <tfoot>
        <tr>

        </tr>
    </tfoot>


</table>
{{-- <div class="row">
    <div class="col-md-4">
    </div>
    <div class="col-md-4" style="justify-content: center;display: flex;">
        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
    </div>
    <div class="col-md-4">
    </div>
</div> --}}
