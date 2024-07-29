<div class="card">
    <div class="card-body">
        @php
            $balance = $finalBalance;
        @endphp
        @forelse ($oldPins as $oldEpin)
            <div class="alert alert-secondary alert-dismissible fade show" role="alert" id="epin_{{ $oldEpin['id'] }}">
                <div class="row">
                    <div class="col-6">{{ $oldEpin['value'] }}</div>
                    <div class="col-6">
                        {{ $oldEpin['usedAmount'] }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    onclick="clearEpin({{ $oldEpin['id'] }})"></button>
                <input type="hidden" name="epinOld[{{ $oldEpin['id'] }}]" data-epinId="{{ $oldEpin['id'] }}" data-usedAmount="{{ $oldEpin['usedAmount'] }}" class="old-epins" value="{{ $oldEpin['value'] }}">
                <input type="hidden" name="epinUsedAmount[{{ $oldEpin['id'] }}]" value="{{ $oldEpin['usedAmount'] }}">

            </div>
        @empty
            {{-- <div class="alert alert-dark" role="alert">
                No E-pin Found
            </div> --}}
        @endforelse
        @if ($pinNumber)
            <div class="alert alert-secondary alert-dismissible fade show" role="alert">
                <div class="row">
                    <div class="col-6">{{ $pinNumber->numbers }}</div>
                    <div class="col-6">
                    {{ $currency }}&nbsp;{{ formatCurrency($usedPinAmount) }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    onclick="clearEpin({{ $pinNumber->id }})"></button>
                <input type="hidden" name="epinOld[{{ $pinNumber->id }}]" data-epinId="{{ $pinNumber->id }}" data-usedAmount="{{ $usedPinAmount }}" class="old-epins" value="{{ $pinNumber->numbers }}" id="epin_{{ $pinNumber->id }}">
                <input type="hidden" name="epinUsedAmount[{{ $pinNumber->id }}]" value="{{ $usedPinAmount }}">

            </div>
        @endif

        <div class="alert alert-primary fade show" role="alert">
            <div class="row">
                <div class="col-6 fw-bolder">E-pin {{ __('common.total') }}</div>
                <div class="col-6">{{ $currency }}&nbsp;{{ formatCurrency($totalEpinAmount) }}</div>
            </div>
        </div>
        @if (!$billStatus)
            <div class="row">
                <div class="row">
                    <div class="form-group col-md-8">
                        <input type="text" name="epin" id="epin" onkeyup="activateApplyEpin()" class="form-control epins" placeholder="Enter E-pin">

                    </div>
                    <div class="form-group col-md-4">
                        <button type="button" disabled
                            class="btn btn-primary mt-1"
                            id="apply-epin"
                            onclick="checkEpinAvailability()">{{ __('common.apply') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
