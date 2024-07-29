@foreach ($activeRank as $rank)
    <div class="form-group">
        <label class="required">
            {{ __('compensation.referral_commission') }}- {{ $rank->name }}
        </label>

        <input type="text" maxlength="5" class="level_percentage form-control" name="rank[{{ $rank->id }}]" min="0"
            value="{{ $rank->rankDetails->referral_commission }}">
        @error('rank.' . $rank->id)
            <span class="text-danger form-text">{{ $message }}</span>
        @enderror
    </div>
@endforeach
