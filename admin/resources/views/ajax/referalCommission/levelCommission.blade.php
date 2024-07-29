@foreach ($levelCommissionPackage as $item)
    <div class="form-group">
        <label class="required">
            {{ __('compensation.referral_commission') }} - {{ $item->name ?? $item->model }}
        </label>
        <input type="text" maxlength="5" class="level_percentage form-control" name="product[{{ $item->id  ?? $item->product_id}}]"
            min="0" value="{{ $item->referral_commission }}">
        @error('product.' . $item->id)
            <span class="text-danger form-text">{{ $message }}</span>
        @enderror
    </div>
@endforeach
