<div class="container ">
    <div class="card">
        <div class="card-header">
            <div class="py-3">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <span class="fw-bolder text-primary"><i
                            class="bx bx-notepad me-2"></i>{{ __('stairStep.step_settings') }}</span>
                    <p class="text-justify mt-lg-2">
                        {{ __('stairStep.description') }}
                    </p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <h4>{{ __('stairStep.step_settings') }}</h4>
                <hr class="bg-primary">
            </div>
        </div>
        <div class="card-body">
            @php
                $id = isset($data['stairstepSingle']) ? $data['stairstepSingle']->id : null;
            @endphp
            <form action="{{ route('stairstep.config.store', $id) }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">{{ __('stairStep.stepName') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                        value="{{ isset($data['stairstepSingle']) ? $data['stairstepSingle']->name : '' }}"
                        class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="persoanlPv">{{ __('stairStep.personalPv') }} <span
                            class="text-danger">*</span></label>
                    <input type="number" name="persoanlPv" id="persoanlPv"
                        class="form-control @error('personalPv') is-invalid @enderror" min="0"
                        value="{{ isset($data['stairstepSingle']) ? $data['stairstepSingle']->personal_pv : '' }}"
                        required>
                    @error('persoanlPv')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="groupPv">{{ __('stairStep.groupPv') }} <span class="text-danger">*</span></label>
                    <input type="number" name="groupPv" id="groupPv" min="0"
                        value="{{ isset($data['stairstepSingle']) ? $data['stairstepSingle']->group_pv : '' }}"
                        class="form-control
                        @error('groupPv') is-invalid @enderror" required>
                    @error('groupPv')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="commission">{{ __('stairStep.stepCommission') }}(%) <span
                            class="text-danger">*</span></label>
                    <input type="number" name="commission" id="commission" min="0"
                        class="form-control @error('commission') is-invalid @enderror"
                        value="{{ isset($data['stairstepSingle']) ? $data['stairstepSingle']->commission : '' }}"
                        required>
                    @error('commission')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('common.submit') }}</button>
                </div>

            </form>
        </div>
    </div>
    @isset($data['stairstep'])

        <div class="card mt-5">
            <div class="card-header">
                <h4>{{ __('stairStep.step_settings') }}</h4>
                <hr class="bg-primary">
            </div>

            <div class="card-body">

                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('stairStep.stepName') }}</th>
                            <th>{{ __('stairStep.personalPv') }}</th>
                            <th>{{ __('stairStep.groupPv') }}</span></th>
                            <th>{{ __('stairStep.stepCommission') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('common.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($data['stairstep'] as $stairstep)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $stairstep->name }}</td>
                                <td>{{ $stairstep->personal_pv }}</td>
                                <td>{{ $stairstep->group_pv }}</td>
                                <td>{{ $stairstep->commission }}</td>
                                <td>{{ $stairstep->status ? 'Active' : 'Inactive' }}</td>
                                <td class="ipad_button_table">
                                    <div class="field">
                                        <a href="{{ route('settings.plan', $stairstep->id) }}"
                                            class="has-tooltip btn btn_size text-danger btn-link"><i class="fa fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{ __('common.no_data') }}
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    @endisset

    <div class="card mt-5">
        <div class="card-header">
            <h4>{{ __('stairStep.overrideCommission') }}</h4>
            <hr class="bg-primary">
        </div>
        <div class="card-body">
            <form action="{{ route('stairstep.config.update') }}" method="post">
                @method('put')
                @csrf
                <div class="form-group">
                    <label for="override">{{ __('stairStep.overrideCommission') }} <span
                            class="text-danger">*</span></label>
                    <input type="number" name="override_commission" id="override"
                        class="form-control @error('override_commission') is-invalid @enderror" min="0"
                        value="{{ $configuration->override_commission }}" required>
                    @error('override_commission')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
