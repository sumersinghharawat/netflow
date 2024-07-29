-@extends('layouts.app')
@section('title', 'Custom Field-Settings')

@section('content')
    <div class="container-fluid settings_page profile_page">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.advancedSettings.inc.links')
            </div>
            <div class="card-body">
                <div class="">
                    <div class="row d-flex align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-0">{{ __('settings.sign_up_form_fields') }}</h4>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#addCustSignUpField" aria-controls="offcanvasRight"
                                style="float: right;">{{ __('settings.add') }} <span class="fas fa-plus"></span></button>
                        </div>
                    </div>
                    <br>
                </div>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="addCustSignUpField"
                    aria-labelledby="offcanvasRightLabel">
                    <div class="offcanvas-header">
                        <h5 id="offcanvasRightLabel">{{ __('settings.custom_sign_up_form_fields') }}</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <div class="row">
                            <div class="card">
                                <form action="{{ route('signupField.store') }}" method="post" onsubmit="storeSignup(this)">
                                    @csrf
                                    {{-- <div class="form-group">
                                        <label>{{ __('settings.field_name') }}:</label>
                                        <input type="text" name="name" id="" class="form-control">
                                    </div> --}}
                                    @forelse ($languages as $item)
                                        <div class="form-group">
                                            <label>{{ __('settings.field_name') }}: {{ $item->name }}
                                                {{ $item->default ? '(Default)' : '' }}</label>
                                            <input type="text" name="name[{{ $item->id }}]" id=""
                                                class="form-control">
                                            {{-- <input type="text" name="lang" value="{{ $item->id }}"> --}}
                                        </div>
                                    @empty
                                    @endforelse
                                    <div class="form-group">
                                        <label>{{ __('settings.type') }}</label>
                                        <select name="type" id="" class="form-select">
                                            <option value="text">{{ __('settings.text') }}</option>
                                            <option value="email">{{ __('settings.email') }}</option>
                                            <option value="number">{{ __('settings.number') }}</option>
                                            {{-- <option value="radio">{{__('settings.radio-button')}}</option>
                                            <option value="dropdown">{{__('settings.drop-down')}}</option> --}}
                                            <option value="textarea">{{ __('settings.textarea') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('settings.enabled') }}</label>
                                        <select name="status" id="" class="form-select">
                                            <option value="1">Yes</option>
                                            <option value="0">no</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('settings.mandatory') }}</label>
                                        <select name="required" id="" class="form-select">
                                            <option value="1">{{ __('settings.yes') }}</option>
                                            <option value="0 ">{{ __('settings.no') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('settings.save_changes') }}</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="offcanvas-footer"></div>
                </div>

                <div class="row">
                    <div class="table-responsive">
                        <form action="{{ route('signupField.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div id="signupFieldTable">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('settings.name') }}</th>
                                            <th>{{ __('settings.type') }}</th>
                                            <th style="min-width:100px">{{ __('settings.sort_order') }}</th>
                                            <th>{{ __('settings.enabled') }}</th>
                                            <th>{{ __('settings.mandatory') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($signupField as $field)
                                            <tr id="signupField_{{ $field->id }}">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>
                                                    @if ($field->editable)
                                                        <input type="text" name="field[{{ $field->id }}][field_name]"
                                                            class="form-control col-sm" value="{{ $field->name }}"
                                                            style="width: 50%;">
                                                    @else
                                                        {{ __('settings.' . $field->name) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($field->editable)
                                                        <input type="text" name="field[{{ $field->id }}][field_type]"
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

                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="row">
                                    <div class="col-md-4">
                                    </div>
                                    <div class="col-md-8 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <hr>
            <div class="card-body">
                <div class="">
                    <div class="row d-flex align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-0">{{ __('settings.custom_sign_up_form_fields') }}</h4>
                        </div>
                    </div>
                    <br>
                </div>

                <div class="row">
                    <div class="table-responsive">
                        <div id="custFieldTable">
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
                                    @forelse ($customField as $field)
                                        <tr id="signupField_{{ $field->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>

                                                {{ $field->name }}
                                            </td>
                                            <td>
                                                {{ $field->type }}
                                            </td>
                                            <td>
                                                {{ $field->sort_order }}
                                            </td>
                                            <td>
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    id="is_enabled_{{ $field->id }}"
                                                    name="field[{{ $field->id }}][is_enabled]"
                                                    @checked($field->status) @disabled(true)>
                                            </td>
                                            <td>
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    id="is_required_{{ $field->id }}"
                                                    name="field[{{ $field->id }}][is_required]"
                                                    @checked($field->required) @disabled(true)>
                                            </td>
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
                                                id="deleteForm{{ $field->id }}"
                                                onsubmit="deleteField1({{ $field->id }})">
                                        @endif
                                    @empty
                                        <tr>
                                            <td>{{ __('common.no_data') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>

                                    </tr>
                                </tfoot>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="edit-custom-field"></div>
@endsection
@push('scripts')
    <script>
        const storeSignup = async (form) => {
            event.preventDefault()

            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)

            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    }
                })
            $('#addCustSignUpField').offcanvas('hide')

            $('#custFieldTable').html('')

            $('#custFieldTable').html(res.data)
            notifySuccess(res.message)

        }

        const updateSignup = async (form) => {
            event.preventDefault()
            let url = form.action
            let data = getForm(form)
            data._method = 'put'
            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        notifySuccess(res.message)
                    }
                })
            if (typeof(res) != "undefined") {
                $('#custFieldTable').html(res.data)
                notifySuccess(res.message)
            }
        }

        const deleteField = async (id) => {
            event.preventDefault()
            let confirm = await confirmSwal()
            if (confirm.isConfirmed == true) {
                let url = "{{ route('signupField.destroy', ':id') }}";
                url = url.replace(":id", id)
                const res = $.post(`${url}`)
                if (typeof(res) != "undefined") {
                    $('#signupField_' + id).remove()
                    notifySuccess(res.message)
                }

            }
        };

        const disableCheckBox = (id) => {
            try {
                let checkbox = "#is_enabled_" + id;
                let required = '#is_required_' + id;
                if ($(checkbox).is(':checked')) {
                    $(required).removeAttr('disabled')
                } else {
                    $(required).prop('checked', false);
                    $(required).attr('disabled', true);
                }
            } catch (error) {
                console.log(error);
            }
        }

        const editField = async (id) => {
            try {
                let url = `{{ route('edit.customfield') }}` + `/` + id;
                const res = await $.get(`${url}`);
                $('#edit-custom-field').html('');
                $('#edit-custom-field').html(res.data);
                $('#editCustomField').offcanvas('show');
            } catch (error) {
                console.log(error);
            }
        }


        const updateCustomField = async (form) => {
            try {
                event.preventDefault()
                let url = form.action
                let data = getForm(form)
                const res = await $.post(`${url}`, data)
                notifySuccess(res.message);
                $('#editCustomField').offcanvas('hide');
                form.reset();
                location.reload();

            } catch (error) {
                if (error.status == 422)
                    formvalidationError(form, error)
            }

        }
    </script>
@endpush
